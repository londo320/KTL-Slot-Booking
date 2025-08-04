<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SlotTemplate;
use App\Models\Slot;
use App\Models\SlotReleaseRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class GenerateSlots extends Command
{
    protected $signature = 'slots:generate {--days=7 : How many days ahead to generate}';

    protected $description = 'Generate slots from templates for the next X days';

    public function handle()
    {
        $days = (int)$this->option('days');
        $this->info("Generating slots for next {$days} day(s)…");

        $templates = SlotTemplate::all();
        if ($templates->isEmpty()) {
            $this->warn("No slot templates found. 👋");
            return 0;
        }

        $created = 0;
        $today   = Carbon::today();

       for ($i = 0; $i < $days; $i++) {
    $date = $today->copy()->addDays($i);

    foreach ($templates as $tpl) {
        if ($date->dayOfWeek !== (int)$tpl->day_of_week) {
            continue;
        }

        $start = Carbon::parse("{$date->toDateString()} {$tpl->start_time}");
        $end   = Carbon::parse("{$date->toDateString()} {$tpl->end_time}");

                $exists = Slot::where('depot_id',        $tpl->depot_id)
                              ->where('booking_type_id', $tpl->booking_type_id)
                              ->where('start_at',        $start)
                              ->where('end_at',          $end)
                              ->exists();

                if ($exists) {
                    $this->line(" • {$start->toDateTimeString()} exists, skipping");
                    continue;
                }

                $slot = Slot::create([
                    'depot_id'         => $tpl->depot_id,
                    'booking_type_id'  => $tpl->booking_type_id,
                    'start_at'         => $start,
                    'end_at'           => $end,
                    'is_blocked'       => false,
                ]);

              $rule = SlotReleaseRule::with('customers')
                        ->where('depot_id', $tpl->depot_id)
                        ->orderByDesc('priority')
                        ->first();

                    if ($rule) {
                        $customerIds = $rule->customers->pluck('id')->toArray();

                        if (!empty($customerIds)) {
                            $slot->allowed_customers()->syncWithoutDetaching($customerIds);
                        } else {
                            $slot->released_at = now();
                        }
                    } else {
                        $slot->released_at = now();
                    }

                    $slot->save();

                $this->info(" ✔ created {$start->toDateTimeString()} → {$end->toTimeString()}");
                $created++;
            }
        }

        $this->info("Done! Created {$created} slot(s).");

        // Run auto-release command
        $this->info("\n📦 Running auto-release logic...");
        Artisan::call('app:auto-release-slots');
        $this->info(Artisan::output());

        return 0;
    }
}
