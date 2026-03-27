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

Path final hosting yang dipakai:

- Project root: `/home/sidoagu1/sidoagungfarm`
- Artisan: `/home/sidoagu1/sidoagungfarm/artisan`
- Log dir: `/home/sidoagu1/sidoagungfarm/storage/logs`

1. Buat Cron Job scheduler (wajib, tiap menit)

- Minute: `*`
- Hour: `*`
- Day: `*`
- Month: `*`
- Weekday: `*`
- Command (disarankan, paling aman di cPanel):

```bash
/bin/bash -lc 'cd /home/sidoagu1/sidoagungfarm && /usr/local/bin/php artisan schedule:run --no-interaction >> storage/logs/log-schedule.log 2>&1'
```

2. Alternatif command lebih singkat:

```bash
/usr/local/bin/php /home/sidoagu1/sidoagungfarm/artisan schedule:run --no-interaction >> /home/sidoagu1/sidoagungfarm/storage/logs/log-schedule.log 2>&1
```

3. Opsional: jika ingin worker dipisah dari scheduler, tambahkan Cron Job worker (tiap menit):

- Minute: `*`
- Hour: `*`
- Day: `*`
- Month: `*`
- Weekday: `*`
- Command (disarankan):

```bash
/bin/bash -lc 'cd /home/sidoagu1/sidoagungfarm && /usr/local/bin/php artisan queue:work database --queue=tickets,emails,default --sleep=1 --tries=3 --timeout=120 --stop-when-empty --no-interaction >> storage/logs/log-queue-worker.log 2>&1'
```

4. Pastikan folder log ada:

```bash
mkdir -p /home/sidoagu1/sidoagungfarm/storage/logs
```

