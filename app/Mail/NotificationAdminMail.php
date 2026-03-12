<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Symfony\Component\Mime\Part\DataPart;

class NotificationAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $logoCid;
    protected string $embeddedLogoPath;

    public function __construct(
        public string $requesterName,
        public string $requesterEmail,
        public ?string $requesterPhone,
        public string $ticketNo,
        public string $questionType,
        public string $questionMessage,
        public string $questionMode,
        public string $submittedAt
    ) {
        $this->embeddedLogoPath = public_path('images/saf/logo.png');
        $this->logoCid = 'sidoagung-logo-'.Str::uuid()->toString();

        $this->withSymfonyMessage(function ($message): void {
            if (! is_file($this->embeddedLogoPath)) {
                return;
            }

            $part = DataPart::fromPath($this->embeddedLogoPath, 'logo.png', 'image/png');
            $part->asInline();
            $part->setContentId($this->logoCid);

            $message->addPart($part);
        });
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notifikasi Ticket Baru (' . $this->ticketNo . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notification_admin',
            with: $this->viewData(),
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function mailSubject(): string
    {
        return (string) $this->envelope()->subject;
    }

    public function viewName(): string
    {
        return 'emails.notification_admin';
    }

    public function templateName(): string
    {
        return 'notification-admin';
    }

    public function viewData(): array
    {
        return [
            'requesterName' => $this->requesterName,
            'requesterEmail' => $this->requesterEmail,
            'requesterPhone' => $this->requesterPhone ?: '-',
            'ticketNo' => $this->ticketNo,
            'questionType' => $this->questionType,
            'questionMessage' => $this->questionMessage,
            'questionMode' => strtoupper($this->questionMode),
            'submittedAt' => $this->submittedAt,
            'logoCid' => $this->logoCid,
        ];
    }

    public function logoPath(): string
    {
        return $this->embeddedLogoPath;
    }
}
