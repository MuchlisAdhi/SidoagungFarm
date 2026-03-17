<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Unauthorized Access</title>
    <style>
        :root {
            --bg: #f4f6f9;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --accent: #16a34a;
            --danger: #dc2626;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: var(--bg);
            color: var(--text);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            width: 100%;
            max-width: 560px;
            background: var(--card);
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            border-top: 4px solid var(--danger);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 28px;
            line-height: 1.2;
        }

        p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .code {
            display: inline-block;
            font-size: 13px;
            margin-bottom: 12px;
            padding: 4px 10px;
            border-radius: 999px;
            background: #fee2e2;
            color: #991b1b;
            font-weight: 700;
        }

        .actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 600;
        }

        .btn-primary {
            background: var(--accent);
            color: #fff;
        }

        .btn-light {
            background: #e5e7eb;
            color: #111827;
        }
    </style>
</head>
<body>
    <div class="card">
        <span class="code">HTTP 403</span>
        <h1>Unauthorized Access</h1>
        <p>{{ $message ?? 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}</p>
        <div class="actions">
            <a class="btn btn-primary" href="{{ url('/admin/main') }}">Kembali ke Dashboard</a>
            <a class="btn btn-light" href="{{ url('/admin/logout') }}">Logout</a>
        </div>
    </div>
</body>
</html>
