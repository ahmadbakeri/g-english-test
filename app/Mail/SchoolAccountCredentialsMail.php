<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SchoolAccountCredentialsMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array<string, mixed>>  $participants
     */
    public function __construct(
        public string $schoolName,
        public string $coordinatorName,
        public string $loginUrl,
        public string $username,
        public string $plainPassword,
        public array $participants = [],
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Akses Portal Sekolah & Bukti Registrasi G-English Brain Champion — ' . $this->schoolName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.school-credentials',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
