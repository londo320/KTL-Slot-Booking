<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Slot;
use App\Models\SlotReleaseRule;
use Carbon\Carbon;

class AutoReleaseSlots extends Command
{
    protected $signature = 'app:auto-release-slots';

    protected $description = 'Release slots per rule or publicly if no rule exists';

    public function handle()

    {
        $this->info('[DEBUG] AutoReleaseSlots ran at ' . now());

        $now = now();
        $rules = SlotReleaseRule::with('customers', 'depot')->orderByDesc('priority')->get();
        $processedDepotIds = [];
        $depotsWithRules = $rules->pluck('depot_id')->unique();

        // === RULE-BASED RELEASE ===
        foreach ($rules as $rule) {
            $depotName = $rule->depot->name ?? 'Unknown Depot';
            $this->line("🔍 Checking rule for depot '{$depotName}' (release day: {$rule->release_day}, time: {$rule->release_time})...");

            $releaseDayOfWeek = is_numeric($rule->release_day)
                ? (int) $rule->release_day
                : Carbon::parse($rule->release_day)->dayOfWeek;

            $releaseTime = Carbon::parse($rule->release_time)->setTimezone(config('app.timezone'));
            $cutoffTime = Carbon::parse($rule->lock_cutoff_time);

            $daysUntilNext = (7 + $releaseDayOfWeek - $now->dayOfWeek) % 7;
            $daysUntilNext = $daysUntilNext === 0 ? 7 : $daysUntilNext; // Always go to next week's same day
            $releaseWindowEnd = $now->copy()->addDays($daysUntilNext)->startOfDay();

            $scheduledRelease = $now->copy()->startOfWeek()->addDays(($releaseDayOfWeek - 1))->setTimeFromTimeString($releaseTime->format('H:i'));
            if ($now->greaterThanOrEqualTo($scheduledRelease)) {
                $slots = Slot::where('depot_id', $rule->depot_id)
                    ->where('start_at', '<', $releaseWindowEnd)
                    ->whereNull('released_at')
                    ->get();

                if ($slots->isEmpty()) {
                    $this->line("ℹ️  No slots to release for depot '{$depotName}' today.");
                    continue;
                }

                foreach ($slots as $slot) {
                    $slot->released_at = $now;
                    $slot->locked_at = $slot->start_at
                        ->copy()
                        ->subDays($rule->lock_cutoff_days)
                        ->setTimeFromTimeString($cutoffTime->format('H:i'));
                    $slot->save();

                    // At release time, slots become public — remove customer restrictions
                    $slot->allowed_customers()->detach();

                    $this->info("✅ Released slot ID {$slot->id} for depot '{$depotName}'");
                }

                $this->line("➡️  Total released for depot '{$depotName}': {$slots->count()}");
                $processedDepotIds[] = $rule->depot_id;
            } else {
                $this->line("⏭️  Skipping: Today is not the release day or time not reached yet.");
            }
        }

        // === FALLBACK RELEASE FOR DEPOTS WITHOUT RULES ===
        $fallbackDepotIds = Slot::select('depot_id')
            ->distinct()
            ->whereNotIn('depot_id', $depotsWithRules)
            ->pluck('depot_id');

        foreach ($fallbackDepotIds as $depotId) {
            $slots = Slot::where('depot_id', $depotId)
                ->whereNull('released_at')
                ->where('start_at', '>', now())
                ->get();

            if ($slots->isEmpty()) {
                $this->line("ℹ️  No fallback slots to release for depot ID {$depotId}.");
                continue;
            }

            foreach ($slots as $slot) {
                $slot->released_at = $now;
                $slot->locked_at = $slot->start_at->copy()->subDays(1)->setTime(16, 0);
                $slot->save();

                $slot->allowed_customers()->detach(); // make public
                $this->info("🌐 Fallback released slot ID {$slot->id} for depot ID {$depotId}");
            }

            $this->line("🟢 Fallback released {$slots->count()} slots for depot ID {$depotId}.");
        }

        $this->info("🏁 Slot release process completed.");
        $this->line("📊 Processed {$rules->count()} rules across " . count($processedDepotIds) . " rule-based depots.");
        $this->line("📊 Fallback processed depots: " . implode(', ', $fallbackDepotIds->toArray()));
    }
}
