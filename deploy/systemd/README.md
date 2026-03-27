# Queue Worker Service (systemd)

File template:

- `deploy/systemd/laravel-queue-worker.service`

## VPS / Dedicated Server (with root access)

1. Salin file service ke server:

```bash
sudo cp deploy/systemd/laravel-queue-worker.service /etc/systemd/system/laravel-queue-worker.service
```

2. Ubah nilai berikut sesuai server:

- `User` / `Group`
- `WorkingDirectory`
- path PHP pada `ExecStart` / `ExecReload`
- path log `StandardOutput` / `StandardError`

3. Aktifkan service:

```bash
sudo systemctl daemon-reload
sudo systemctl enable laravel-queue-worker.service
sudo systemctl start laravel-queue-worker.service
sudo systemctl status laravel-queue-worker.service
```

4. Saat deploy code baru:

```bash
sudo systemctl reload laravel-queue-worker.service
```

## cPanel Shared Hosting (tanpa terminal / tanpa root)

`systemd` tidak tersedia untuk user shared hosting biasa. Gunakan Cron Jobs via UI cPanel:

1. Cron tiap menit untuk scheduler:

```bash
php -q /home/<cpanel_user>/<app_dir>/artisan schedule:run >> /home/<cpanel_user>/<app_dir>/storage/logs/log-schedule.log 2>&1
```

2. Jika tidak ingin worker dipicu dari scheduler, tambahkan cron worker:

```bash
php -q /home/<cpanel_user>/<app_dir>/artisan queue:work database --queue=tickets,emails,default --sleep=1 --tries=3 --timeout=120 --stop-when-empty >> /home/<cpanel_user>/<app_dir>/storage/logs/log-queue-worker.log 2>&1
```

