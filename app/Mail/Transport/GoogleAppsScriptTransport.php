<?php

namespace App\Mail\Transport;

use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\MessageConverter;
use Illuminate\Support\Facades\Http;

class GoogleAppsScriptTransport extends AbstractTransport
{
    protected $url;
    protected $token;

    public function __construct(string $url, string $token)
    {
        parent::__construct();
        $this->url = $url;
        $this->token = $token;
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        
        $to = collect($email->getTo())->map(function ($address) {
            return $address->getAddress();
        })->implode(',');

        $subject = $email->getSubject();
        
        // Get HTML Body, fallback to Text Body if empty
        $html = $email->getHtmlBody();
        if (empty($html)) {
            $html = $email->getTextBody();
        }

        // Send HTTP POST request to Google Apps Script Web App
        $response = Http::timeout(10)->post($this->url, [
            'token' => $this->token,
            'to' => $to,
            'subject' => $subject,
            'html' => $html,
        ]);

        if ($response->failed() || !$response->json('success')) {
            throw new \Exception('Gagal mengirim email via Google Apps Script: ' . ($response->json('error') ?? $response->body()));
        }
    }

    public function __toString(): string
    {
        return 'google_apps_script';
    }
}
