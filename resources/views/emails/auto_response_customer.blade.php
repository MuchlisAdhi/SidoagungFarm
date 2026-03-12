<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Auto Response</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f7f7f7; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f7f7f7; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 24px;">
                            <div style="margin-bottom: 16px;">
                                <img src="{{ !empty($logoEmbedded) ? 'cid:'.$logoCid : $logoUrl }}" alt="Sidoagung Farm" style="height: 48px;">
                            </div>
                            <h2 style="margin: 0 0 12px; color: #2c3e50;">Terima kasih telah menghubungi PT. Sidoagung Farm</h2>
                            <p style="margin: 0 0 16px; color: #4b5563;">Halo {{ $customerName }},</p>
                            <p style="margin: 0 0 16px; color: #4b5563;">
                                Kami telah menerima pertanyaan Anda. Tim kami akan segera meninjau dan memberikan respon secepatnya.
                            </p>
                            <div style="background-color: #f3f4f6; padding: 16px; border-radius: 6px; margin-bottom: 16px;">
                                <p style="margin: 0 0 8px; font-weight: 600;">Nomor Tiket</p>
                                <p style="margin: 0; color: #111827; font-size: 18px;">{{ $ticketNo }}</p>
                            </div>
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 16px;">
                                <tr>
                                    <td style="padding: 8px 0; color: #6b7280; width: 140px;">Tipe Pertanyaan</td>
                                    <td style="padding: 8px 0; color: #111827;">{{ $questionType }}</td>
                                </tr>
                            </table>
                            <p style="margin: 0 0 8px; color: #4b5563;"><strong>Rangkuman pesan:</strong></p>
                            <p style="margin: 0; color: #4b5563; white-space: pre-line;">{{ $questionMessage }}</p>
                            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">
                            <p style="margin: 0; color: #6b7280; font-size: 12px;">
                                Email ini dikirim otomatis oleh sistem. Mohon tidak membalas email ini.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
