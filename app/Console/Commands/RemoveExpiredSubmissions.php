<?php

namespace App\Console\Commands;

use App\Models\Submission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RemoveExpiredSubmissions extends Command
{
    protected $signature = 'submissions:remove-expired';
    protected $description = 'Delete expired submissions and any stored poster images';

    public function handle(): int
    {
        $expiredSubmissions = Submission::whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($expiredSubmissions as $submission) {
            if ($submission->file_path) {
                Storage::disk('public')->delete($submission->file_path);
            }

            $submission->delete();
        }

        $this->info('Removed ' . $expiredSubmissions->count() . ' expired submissions.');

        return self::SUCCESS;
    }
}

