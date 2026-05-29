<?php

namespace App\Console\Commands;

use App\Models\Document;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('documents:prune')]
#[Description('Delete expired documents')]
class PruneExpiredDocuments extends Command
{
    public function handle(): int
    {
        $count = Document::whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->delete();

        $this->info("Pruned {$count} expired document(s).");
        return Command::SUCCESS;
    }
}
