<?php

namespace App\Services;

class EmailService
{
    private $mailApiLink;

    public function __construct()
    {
        $this->mailApiLink = config('mailhog.api_link');
    }

    public function getMessages(): array
    {
        $response = file_get_contents("{$this->mailApiLink}messages");
        return json_decode($response, true);
    }

    public function getMessageDetails(string $messageId): array
    {
        $response = file_get_contents("{$this->mailApiLink}message/{$messageId}");
        return json_decode($response, true);
    }

    public function downloadAttachment(string $messageId, string $partId, string $filePath): void
    {
        $content = file_get_contents("{$this->mailApiLink}message/{$messageId}/part/{$partId}");
        file_force_contents($filePath, $content);
    }
}
