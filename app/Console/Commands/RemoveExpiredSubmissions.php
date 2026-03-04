<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Submission;

class RemoveExpiredSubmissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'submissions:remove-expired-submissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove submissions past their expiry date';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $count = Submission::where('expires_at', '<', now())
                        ->where('status', 'approved')
                        ->delete();
        $this->info("Removed {$count} expired submissions.");

    }
}
