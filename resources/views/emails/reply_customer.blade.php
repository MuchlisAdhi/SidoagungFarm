<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Respon Pertanyaan</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f7f7f7; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f7f7f7; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 24px;">
                            <div style="margin-bottom: 16px;">
                                <img src="cid:{{ $logoCid }}" alt="Sidoagung Farm" style="height: 48px;">
                            </div>
                            <h2 style="margin: 0 0 12px; color: #1f2937;">Respon Pertanyaan Anda</h2>
                            <p style="margin: 0 0 16px; color: #4b5563;">Halo {{ $customerName }},</p>
                            <p style="margin: 0 0 16px; color: #4b5563;">
                                Terima kasih telah menghubungi kami. Berikut respon dari tim PT. Sidoagung Farm untuk pertanyaan Anda.
                            </p>
                            <div style="background-color: #f3f4f6; padding: 16px; border-radius: 6px; margin-bottom: 16px;">
                                <p style="margin: 0 0 8px; font-weight: 600;">Nomor Tiket</p>
                                <p style="margin: 0; color: #111827; font-size: 18px;">{{ $ticketNo }}</p>
                            </div>
                            <p style="margin: 0 0 8px; color: #4b5563;"><strong>Pertanyaan Anda:</strong></p>
                            <p style="margin: 0 0 16px; color: #4b5563; white-space: pre-line;">{{ $questionMessage }}</p>
                            <p style="margin: 0 0 8px; color: #4b5563;"><strong>Respon Kami:</strong></p>
                            <p style="margin: 0; color: #4b5563; white-space: pre-line;">{{ $responseMessage }}</p>
                            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">
                            <p style="margin: 0; color: #6b7280; font-size: 12px;">
                                Email ini dikirim otomatis oleh sistem.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
