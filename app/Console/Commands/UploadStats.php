<?php

namespace App\Console\Commands;

use App\Models\DailyStat;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('stats:uploads {--days=30 : How many days back to show}')]
#[Description('Show uploads per day (survives document expiry)')]
class UploadStats extends Command
{
    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));

        $rows = DailyStat::query()
            ->where('date', '>=', now()->subDays($days - 1)->toDateString())
            ->orderByDesc('date')
            ->get();

        if ($rows->isEmpty()) {
            $this->info("No uploads in the last {$days} day(s).");
            return Command::SUCCESS;
        }

        $this->table(
            ['Date', 'Uploads'],
            $rows->map(fn (DailyStat $s) => [$s->date->toDateString(), $s->uploads])->all(),
        );
        $this->info("Total: {$rows->sum('uploads')} over {$rows->count()} day(s).");

        return Command::SUCCESS;
    }
}
