<?php

namespace App\Console\Commands;

use App\Models\DailyStat;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('stats:uploads {--days=30 : How many days back to show}')]
#[Description('Show daily usage totals: uploads by client, updates, opens, deletes (survive document expiry)')]
class UploadStats extends Command
{
    /**
     * Column headings, in display order, keyed by daily_stats column.
     *
     * @var array<string, string>
     */
    private const COLUMNS = [
        'uploads' => 'Uploads',
        'tiny_uploads' => 'Tiny',
        'web_uploads' => 'Web',
        'cli_uploads' => 'CLI',
        'mcp_uploads' => 'MCP',
        'updates' => 'Updates',
        'opens' => 'Opens',
        'deletes' => 'Deletes',
    ];

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));

        $rows = DailyStat::query()
            ->where('date', '>=', now()->subDays($days - 1)->toDateString())
            ->orderByDesc('date')
            ->get();

        if ($rows->isEmpty()) {
            $this->info("Nothing recorded in the last {$days} day(s).");

            return Command::SUCCESS;
        }

        $this->table(
            ['Date', ...array_values(self::COLUMNS)],
            [
                ...$rows->map(fn (DailyStat $stat) => [
                    $stat->date->toDateString(),
                    ...array_map(fn (string $column) => $stat->{$column}, array_keys(self::COLUMNS)),
                ])->all(),
                ['Total', ...array_map(fn (string $column) => $rows->sum($column), array_keys(self::COLUMNS))],
            ],
        );
        $this->line('Tiny = under 100 bytes, usually scanners testing the MCP server. Web/CLI/MCP only count clients that identify themselves (newer versions).');

        return Command::SUCCESS;
    }
}
