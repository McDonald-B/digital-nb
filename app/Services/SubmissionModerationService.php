<?php

namespace App\Services;

class SubmissionModerationService
{
    /**
     * Returns:
     * [
     *   'status' => 'pending'|'flagged',
     *   'reason' => null|string,
     * ]
     */
    public function moderate(string $title, ?string $content = null): array
    {
        $bannedWords = [
            'scam',
            'fraud',
            'hate',
            'violence',
            'kill',
            'weapon',
            'drugs',
            'terror',
            'abuse',
        ];

        $text = strtolower(trim($title . ' ' . ($content ?? '')));

        foreach ($bannedWords as $word) {
            if (str_contains($text, $word)) {
                return [
                    'status' => 'flagged',
                    'reason' => "Flagged automatically because it contains the word: {$word}",
                ];
            }
        }

        return [
            'status' => 'pending',
            'reason' => null,
        ];
    }
}
