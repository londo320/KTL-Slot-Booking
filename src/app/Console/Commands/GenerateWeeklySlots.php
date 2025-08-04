<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SlotTemplate;
use App\Models\Slot;
use Carbon\Carbon;

class GenerateWeeklySlots extends Command
{
    protected $signature = 'slots:generate';
    protected $description = 'Roll next 7 days of Slots from templates';

    public function handle()
    {
        $today = Carbon::today();

        SlotTemplate::with(['depot','bookingType'])->get()
            ->groupBy('weekday')
            ->each(function($group,$weekday) use($today) {
                $date = $today->copy()->addDays($weekday - $today->dayOfWeek);
                if ($date->lt($today)) {
                    $date->addWeek();
                }
                foreach ($group as $tpl) {
                    $start = Carbon::parse("{$date->toDateString()} {$tpl->start_time}");
                    if (! Slot::where('depot_id',$tpl->depot_id)
                              ->where('start_at',$start)
                              ->exists()) {
                        Slot::create([
                            'depot_id'         => $tpl->depot_id,
                            'booking_type_id'  => $tpl->booking_type_id,
                            'start_at'         => $start,
                            'end_at'           => $start->copy()->addMinutes($tpl->default_length),
                            'is_blocked'       => false,
                        ]);
                        $this->info("Created slot for {$tpl->depot->name} on {$start}");
                    }
                }
            });
    }
}
