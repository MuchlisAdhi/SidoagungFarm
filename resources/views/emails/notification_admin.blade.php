<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Notifikasi Ticket Baru</title>
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
                            <h2 style="margin: 0 0 12px; color: #1f2937;">Notifikasi Ticket Baru</h2>
                            <p style="margin: 0 0 16px; color: #4b5563;">Sebuah pertanyaan baru telah masuk melalui website.</p>

                            <div style="background-color: #f3f4f6; padding: 16px; border-radius: 6px; margin-bottom: 16px;">
                                <p style="margin: 0 0 8px; font-weight: 600;">Nomor Tiket</p>
                                <p style="margin: 0; color: #111827; font-size: 18px;">{{ $ticketNo }}</p>
                            </div>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 16px;">
                                <tr>
                                    <td style="padding: 8px 0; color: #6b7280; width: 180px;">Nama</td>
                                    <td style="padding: 8px 0; color: #111827;">{{ $requesterName }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #6b7280;">Email</td>
                                    <td style="padding: 8px 0; color: #111827;">{{ $requesterEmail }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #6b7280;">Telepon</td>
                                    <td style="padding: 8px 0; color: #111827;">{{ $requesterPhone }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #6b7280;">Topik</td>
                                    <td style="padding: 8px 0; color: #111827;">{{ $questionType }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #6b7280;">Mode</td>
                                    <td style="padding: 8px 0; color: #111827;">{{ $questionMode }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #6b7280;">Waktu Masuk</td>
                                    <td style="padding: 8px 0; color: #111827;">{{ $submittedAt }}</td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 8px; color: #4b5563;"><strong>Pesan:</strong></p>
                            <p style="margin: 0; color: #4b5563; white-space: pre-line;">{{ $questionMessage }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

