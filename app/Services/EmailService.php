<?php

namespace App\Services;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Check if mail queue is enabled.
     */
    public static function isQueueEnabled(): bool
    {
        return filter_var(config('mail.queue_enabled', true), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Send email to a single recipient.
     */
    public static function sendTo(string|array $email, Mailable $mailable): void
    {
        if (self::isQueueEnabled()) {
            Mail::to($email)->queue($mailable);
        } else {
            Mail::to($email)->send($mailable);
        }
    }

    /**
     * Send email to multiple recipients.
     */
    public static function sendToMany(Collection|array $emails, Mailable $mailable): void
    {
        $emails = is_array($emails) ? collect($emails) : $emails;

        foreach ($emails as $email) {
            self::sendTo($email, $mailable);
        }
    }

    /**
     * Send email with BCC recipients.
     */
    public static function sendWithBcc(string|array $to, string|array $bcc, Mailable $mailable): void
    {
        if (self::isQueueEnabled()) {
            Mail::to($to)->bcc($bcc)->queue($mailable);
        } else {
            Mail::to($to)->bcc($bcc)->send($mailable);
        }
    }
}
