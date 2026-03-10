<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Symfony\Component\Mime\Part\DataPart;

class ReplyCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $logoCid;
    protected string $embeddedLogoPath;

    public function __construct(
        public string $customerName,
        public string $ticketNo,
        public string $questionMessage,
        public string $responseMessage
    ) {
        $this->embeddedLogoPath = public_path('images/saf/logo.png');
        $this->logoCid = 'sidoagung-logo-'.Str::uuid().'@sidoagungfarm';

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
            subject: 'Respon Pertanyaan Anda - Ticket ' . $this->ticketNo,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reply_customer',
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
        return 'emails.reply_customer';
    }

    public function templateName(): string
    {
        return 'reply-customer';
    }

    public function viewData(): array
    {
        return [
            'customerName' => $this->customerName,
            'ticketNo' => $this->ticketNo,
            'questionMessage' => $this->questionMessage,
            'responseMessage' => $this->responseMessage,
            'logoCid' => $this->logoCid,
        ];
    }

    public function logoPath(): string
    {
        return $this->embeddedLogoPath;
    }
}

