<?php

namespace App\Services;

use App\Mail\AutoResponseCustomerMail;
use App\Mail\NotificationAdminMail;
use App\Mail\ReplyCustomerMail;
use App\Models\ClientQuestion2;
use App\Models\EmailConfig;
use App\Models\LogEmailSender;
use Illuminate\Support\Str;
use PHPMailer\PHPMailer\Exception as PhpMailerException;
use PHPMailer\PHPMailer\PHPMailer;
use RuntimeException;

class PhpMailerService
{
    /**
     * Send auto-response when customer creates a ticket from contact form.
     */
    public function sendTicketCreatedNotification(ClientQuestion2 $question): void
    {
        $this->sendTicketCreatedNotificationData(
            customerName: (string) ($question->name ?? 'Customer'),
            customerEmail: (string) ($question->email ?? ''),
            ticketNo: (string) ($question->ticket_no ?? '-'),
            questionType: (string) ($question->qtype ?? '-'),
            questionMessage: (string) ($question->description ?? '-'),
            questionMode: 'q2',
            questionId: (string) $question->id
        );
    }

    public function sendTicketCreatedNotificationData(
        string $customerName,
        string $customerEmail,
        string $ticketNo,
        string $questionType,
        string $questionMessage,
        ?string $questionMode = null,
        ?string $questionId = null,
        ?string $ticketId = null
    ): void {
        if (! $customerEmail) {
            return;
        }

        $mailTemplate = new AutoResponseCustomerMail(
            customerName: $customerName ?: 'Customer',
            ticketNo: $ticketNo ?: '-',
            questionType: $questionType ?: '-',
            questionMessage: $questionMessage ?: '-'
        );

        $this->sendHtml(
            toEmail: $customerEmail,
            toName: $customerName ?: 'Customer',
            subject: $mailTemplate->mailSubject(),
            viewName: $mailTemplate->viewName(),
            viewData: $mailTemplate->viewData(),
            templateName: $mailTemplate->templateName(),
            questionMode: $questionMode,
            questionId: $questionId,
            ticketNo: $ticketNo,
            ticketId: $ticketId,
            logoPath: $mailTemplate->logoPath(),
            logoCid: $mailTemplate->logoCid
        );
    }

    /**
     * Send admin response to customer.
     */
    public function sendQuestionReplyNotification(
        string $customerName,
        string $customerEmail,
        string $ticketNo,
        string $questionMessage,
        string $responseMessage,
        ?string $questionMode = null,
        ?string $questionId = null,
        ?string $ticketId = null
    ): void {
        if (! $customerEmail) {
            return;
        }

        $mailTemplate = new ReplyCustomerMail(
            customerName: $customerName ?: 'Customer',
            ticketNo: $ticketNo ?: '-',
            questionMessage: $questionMessage ?: '-',
            responseMessage: $responseMessage ?: '-'
        );

        $this->sendHtml(
            toEmail: $customerEmail,
            toName: $customerName ?: 'Customer',
            subject: $mailTemplate->mailSubject(),
            viewName: $mailTemplate->viewName(),
            viewData: $mailTemplate->viewData(),
            templateName: $mailTemplate->templateName(),
            questionMode: $questionMode,
            questionId: $questionId,
            ticketNo: $ticketNo,
            ticketId: $ticketId,
            logoPath: $mailTemplate->logoPath(),
            logoCid: $mailTemplate->logoCid
        );
    }

    /**
     * Send admin notification for newly created ticket.
     */
    public function sendNotificationAdmin(
        string $requesterName,
        string $requesterEmail,
        ?string $requesterPhone,
        string $ticketNo,
        string $questionType,
        string $questionMessage,
        string $questionMode,
        string $submittedAt,
        ?string $questionModeLog = null,
        ?string $questionId = null,
        ?string $ticketId = null
    ): void {
        $toEmail = $this->getAdminNotificationRecipientEmail();
        if (! $toEmail) {
            logger()->warning('Admin notification email belum dikonfigurasi.', [
                'ticket_no' => $ticketNo,
            ]);
            return;
        }

        $mailTemplate = new NotificationAdminMail(
            requesterName: $requesterName ?: 'Customer',
            requesterEmail: $requesterEmail ?: '-',
            requesterPhone: $requesterPhone,
            ticketNo: $ticketNo ?: '-',
            questionType: $questionType ?: '-',
            questionMessage: $questionMessage ?: '-',
            questionMode: $questionMode ?: '-',
            submittedAt: $submittedAt ?: now()->format('Y-m-d H:i:s')
        );

        $this->sendHtml(
            toEmail: $toEmail,
            toName: 'Admin',
            subject: $mailTemplate->mailSubject(),
            viewName: $mailTemplate->viewName(),
            viewData: $mailTemplate->viewData(),
            templateName: $mailTemplate->templateName(),
            questionMode: $questionModeLog,
            questionId: $questionId,
            ticketNo: $ticketNo,
            ticketId: $ticketId,
            logoPath: $mailTemplate->logoPath(),
            logoCid: $mailTemplate->logoCid
        );
    }

    public function getAdminNotificationRecipientEmail(): ?string
    {
        $activeConfig = EmailConfig::where('is_active', true)->first();

        return $this->resolveAdminNotificationEmail($activeConfig);
    }

    /**
     * @throws PhpMailerException
     */
    protected function sendHtml(
        string $toEmail,
        string $toName,
        string $subject,
        string $viewName,
        array $viewData = [],
        string $templateName = 'generic',
        ?string $questionMode = null,
        ?string $questionId = null,
        ?string $ticketNo = null,
        ?string $ticketId = null,
        ?string $logoPath = null,
        ?string $logoCid = null
    ): void {
        $activeConfig = EmailConfig::where('is_active', true)->first();
        $mailer = $this->buildMailer($activeConfig);

        $fromAddress = $activeConfig?->from_address ?: (string) config('mail.from.address');
        $fromName = $activeConfig?->from_name ?: (string) config('mail.from.name', config('app.name'));

        if (! $fromAddress) {
            throw new RuntimeException('From email belum dikonfigurasi.');
        }

        $logoCid = $logoCid ?: ('sidoagung-logo-'.Str::uuid()->toString());
        $logoUrl = $this->resolveLogoUrl($logoPath);
        $logoEmbedded = false;

        $logoPath = $logoPath ?: public_path('images/saf/logo.png');
        if (is_file($logoPath)) {
            $logoEmbedded = (bool) $mailer->addEmbeddedImage(
                $logoPath,
                $logoCid,
                basename($logoPath),
                PHPMailer::ENCODING_BASE64,
                'image/png'
            );
        }

        $viewData['logoCid'] = $logoCid;
        $viewData['logoUrl'] = $logoUrl;
        $viewData['logoEmbedded'] = $logoEmbedded;

        $htmlBody = view($viewName, $viewData)->render();

        $log = LogEmailSender::create([
            'question_mode' => $questionMode,
            'question_id' => $questionId,
            'ticket_id' => $ticketId,
            'ticket_no' => $ticketNo,
            'recipient_email' => $toEmail,
            'subject' => $subject,
            'template' => $templateName,
            'body' => $htmlBody,
            'status' => 'queued',
        ]);

        $mailer->setFrom($fromAddress, $fromName);
        $mailer->addAddress($toEmail, $toName);
        $mailer->Subject = $subject;
        $mailer->isHTML(true);
        $mailer->Body = $htmlBody;
        $mailer->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n"], $htmlBody));

        try {
            $mailer->send();
            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Throwable $th) {
            $log->update([
                'status' => 'failed',
                'error_message' => $th->getMessage(),
            ]);
            throw $th;
        }
    }

    /**
     * @throws PhpMailerException
     */
    protected function buildMailer(?EmailConfig $activeConfig): PHPMailer
    {
        $mailer = new PHPMailer(true);
        $mailer->CharSet = 'UTF-8';
        $mailer->isSMTP();

        $host = $activeConfig?->host ?: config('mail.mailers.smtp.host');
        $port = (int) ($activeConfig?->port ?: config('mail.mailers.smtp.port', 587));
        $username = $activeConfig?->username ?: config('mail.mailers.smtp.username');
        $password = $activeConfig?->password ?: config('mail.mailers.smtp.password');
        $encryption = $activeConfig?->encryption ?: config('mail.mailers.smtp.encryption');

        if (! $host) {
            throw new RuntimeException('SMTP host belum dikonfigurasi.');
        }

        $mailer->Host = (string) $host;
        $mailer->Port = $port;

        if ($username) {
            $mailer->SMTPAuth = true;
            $mailer->Username = (string) $username;
            $mailer->Password = (string) $password;
        } else {
            $mailer->SMTPAuth = false;
        }

        $enc = strtolower((string) $encryption);
        if ($enc === 'ssl') {
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($enc === 'tls' || $enc === 'starttls') {
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        return $mailer;
    }

    protected function resolveAdminNotificationEmail(?EmailConfig $activeConfig = null): ?string
    {
        $envRecipient = $this->sanitizeRecipientEmail((string) env('ADMIN_NOTIFICATION_EMAIL', ''), true);
        if ($envRecipient) {
            return $envRecipient;
        }

        if (! $activeConfig) {
            return $this->sanitizeRecipientEmail((string) config('mail.from.address'));
        }

        $reportRecipient = $this->sanitizeRecipientEmail((string) $activeConfig->report, true);
        if ($reportRecipient) {
            return $reportRecipient;
        }

        $username = $this->sanitizeRecipientEmail((string) $activeConfig->username);
        if ($username) {
            return $username;
        }

        $fromAddress = $this->sanitizeRecipientEmail((string) $activeConfig->from_address);
        if ($fromAddress) {
            return $fromAddress;
        }

        return $this->sanitizeRecipientEmail((string) config('mail.from.address'));
    }

    protected function sanitizeRecipientEmail(string $rawEmail, bool $allowNoReply = false): ?string
    {
        $email = trim(strtok($rawEmail, ',') ?: '');
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        if ($allowNoReply) {
            return $email;
        }

        $localPart = strtolower((string) strtok($email, '@'));
        if (str_contains($localPart, 'no-reply') || str_contains($localPart, 'noreply')) {
            return null;
        }

        return $email;
    }

    protected function resolveLogoUrl(?string $logoPath = null): string
    {
        $configuredLogoUrl = trim((string) env('MAIL_LOGO_URL', ''));
        if ($configuredLogoUrl !== '' && filter_var($configuredLogoUrl, FILTER_VALIDATE_URL)) {
            return $configuredLogoUrl;
        }

        $logoPath = $logoPath ?: public_path('images/saf/logo.png');
        $publicRoot = str_replace('\\', '/', public_path());
        $normalizedLogoPath = str_replace('\\', '/', $logoPath);

        if (Str::startsWith($normalizedLogoPath, $publicRoot)) {
            $relativePath = ltrim((string) Str::after($normalizedLogoPath, $publicRoot), '/');
            if ($relativePath !== '') {
                return url($relativePath);
            }
        }

        return url('images/saf/logo.png');
    }
}
