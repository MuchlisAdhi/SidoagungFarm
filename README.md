# LandingPage SAAP

Landing page company profile + panel admin berbasis Laravel.

## Stack Utama

- PHP `8.2.12+`
- Laravel `12.x`
- Composer `2.x`
- MySQL/MariaDB (default env menggunakan MySQL)
- Laravel Sanctum `4.x`
- Laravel Mix `6` untuk asset frontend

## Struktur Fitur

- Website publik:
  - Home
  - About Us
  - Products
  - CSR
  - Talk To Us (pertanyaan, kemitraan, karir)
- Panel admin (prefix `/admin`):
  - Login/logout admin
  - Manajemen user
  - Home banner & banner menu
  - Produk
  - Feedback (karir, pertanyaan, mitra)
  - Ticketing (list + response)
  - Email Config (SMTP master)
  - Email Logs (list + detail error)
  - Karir
  - CSR (env, safety, sosial)
  - News
  - Testimoni
  - Resep

## Refactor Arsitektur (Controller Tipis)

Project ini sekarang memakai pola pemisahan tanggung jawab agar controller hanya mengatur alur request -> response.

### 1) Form Request

Validasi dipindahkan dari controller ke Form Request:

- Public:
  - `app/Http/Requests/Web/SubmitQuestionRequest.php`
  - `app/Http/Requests/Web/JoinAsPartnerRequest.php`
  - `app/Http/Requests/Web/CareerApplyRequest.php`
  - `app/Http/Requests/Web/SubmitProductFaqRequest.php`
- Admin:
  - `app/Http/Requests/Admin/FaqGetRequest.php`
  - `app/Http/Requests/Admin/ListTicketRequest.php`
  - `app/Http/Requests/Admin/ListEmailLogRequest.php`

### 2) Service Layer

Business logic dipindahkan ke service:

- Public:
  - `app/Services/WebsiteInquiryService.php`
  - `app/Services/PartnerApplicationService.php`
  - `app/Services/CareerApplicationService.php`
- Admin:
  - `app/Services/Admin/FaqFeedbackService.php`
  - `app/Services/Admin/CareerFeedbackService.php`
  - `app/Services/Admin/PartnerFeedbackService.php`
  - `app/Services/Admin/TicketManagementService.php`
  - `app/Services/Admin/EmailLogService.php`

Controller yang sudah direfaktor:

- `app/Http/Controllers/WeController.php`
- `app/Http/Controllers/ProductController.php`
- `app/Http/Controllers/Admin/FeedbackController.php`
- `app/Http/Controllers/Admin/TicketController.php`
- `app/Http/Controllers/Admin/EmailLogController.php`

### 3) Observer

Observer dipakai untuk logic lintas alur yang harus selalu sinkron:

- `app/Observers/ClientQuestionObserver.php`
  - saat `ClientQuestion` dibuat, otomatis trigger `TicketingJob` mode `q1`
- `app/Observers/ClientQuestion2Observer.php`
  - saat `ClientQuestion2` dibuat, otomatis trigger `TicketingJob` mode `q2`
- `app/Observers/TicketObserver.php`
  - setiap ticket dibuat/diupdate, status ticket disinkronkan ke tabel pertanyaan (`clientquestion` / `clientquestion2`)

Registrasi observer ada di:

- `app/Providers/EventServiceProvider.php`

## Admin Global Loader & Submit Utility

Panel admin sekarang memakai loader global + helper submit untuk mencegah double click saat proses simpan/update.

Lokasi implementasi:

- `resources/views/admin/master.blade.php`

Komponen yang tersedia:

- Overlay loader global `#adminLoadingOverlay` dengan animasi snake
- Base color loader: `#00A651`
- Helper JavaScript global: `window.AdminSubmit`

API helper:

- `AdminSubmit.start(button, text)`
  - Disable tombol target
  - Ubah teks tombol (contoh: `Menyimpan...`)
  - Tampilkan overlay loader
  - Return `false` jika tombol sedang terkunci (anti klik berulang)
- `AdminSubmit.stop(button)`
  - Restore tombol target (enabled + teks awal)
  - Sembunyikan overlay loader
- `AdminSubmit.stopAll()`
  - Reset semua tombol yang sedang mode loading

Auto-protection submit form:

- Semua `form` dengan method `POST` di admin otomatis diberi lock submit sekali.
- Jika user submit ulang saat request belum selesai, submit kedua dibatalkan.

### Pola Pakai (AJAX)

```javascript
if (!AdminSubmit.start("#btnSaveForm", "Menyimpan...")) {
    return;
}

$.post("/admin/example/save", payload)
    .done(function(res) {
        if (res.code === 200) {
            window.location.reload();
            return;
        }

        AdminSubmit.stop("#btnSaveForm");
    })
    .fail(function() {
        AdminSubmit.stop("#btnSaveForm");
    });
```

### Pola Pakai (Form Submit Biasa)

```javascript
if (!AdminSubmit.start("#btnSave", "Menyimpan...")) {
    return;
}

$("#frmExample")
    .prop("method", "post")
    .prop("action", "/admin/example/save")
    .submit();
```

Catatan:

- Untuk flow sukses yang `window.location.reload()` / redirect, tidak perlu `AdminSubmit.stop()` manual.
- Untuk flow gagal (validasi/server error), wajib panggil `AdminSubmit.stop(...)` agar tombol aktif lagi.

## Instalasi Lokal

1. Install dependency PHP:

```bash
composer install
```

2. Buat file env:

```bash
cp .env.example .env
```

3. Generate app key:

```bash
php artisan key:generate
```

4. Atur koneksi database pada `.env` (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

5. Jalankan migrasi + seeder:

```bash
php artisan migrate --seed
```

6. Jalankan aplikasi:

```bash
php artisan serve
```

7. (Opsional) build asset frontend:

```bash
npm install
npm run dev
```

## Akun Default Seeder

Seeder `UserSeeder` membuat akun admin:

- Email: `admin@admin.com`
- Password: `123Empat`

Ganti kredensial ini untuk environment non-development.

## Konfigurasi Notifikasi Email Admin

Agar notifikasi ticket baru terkirim ke admin (dan tidak gagal ke alamat `no-reply`), pastikan salah satu ini diisi:

1. Field `Admin Recipient Email` di menu `Email Config` (disimpan di kolom `report` pada konfigurasi SMTP aktif).
2. Variable `.env` `ADMIN_NOTIFICATION_EMAIL` (akan override field `Admin Recipient Email` jika diisi).

Catatan:

- Jangan gunakan alamat `no-reply@...` sebagai penerima notifikasi admin jika mailbox tersebut memang tidak menerima incoming email.
- Untuk memastikan logo email selalu muncul, isi `APP_URL` dengan domain production dan (opsional, disarankan) isi `MAIL_LOGO_URL=https://sidoagungfarm.com/images/saf/logo.png`.
- Setelah ubah `.env`, jalankan `php artisan optimize:clear`.

## Dokumentasi Routes

Sumber utama route ada di:

- `routes/web.php`
- `routes/api.php`

### 1) Public Web Routes

| Method | URI | Name |
| --- | --- | --- |
| GET | `/` | `main` |
| GET | `/getResource/{id}` | `main.getResource` |
| GET | `/about-us` | `about-us` |
| GET | `/products` | `products` |
| GET | `/products/get-product/{id}` | `products.get` |
| POST | `/products/sendFaq` | `products.faq` |
| ANY (redirect) | `/csr` | `csr` |
| GET | `/csr/summary` | `csr.summary` |
| GET | `/csr/news` | `csr.news` |
| GET | `/csr/resep` | `csr.resep` |
| GET | `/csr/getList` | `csr.getList` |
| GET | `/csr/getDetail` | `csr.getDetail` |
| ANY (redirect) | `/talk-us` | `we` |
| GET | `/talk-us/summary` | `we.summary` |
| POST | `/talk-us/sent-question` | `we.question` |
| GET | `/talk-us/join-us` | `we.join-us` |
| GET | `/talk-us/be-our-partner` | `we.be-our-partner` |
| GET | `/talk-us/career/{id?}` | `we.career` |
| GET | `/talk-us/career-apply/{id}` | `we.career.apply` |
| POST | `/talk-us/job-apply` | `we.job-apply` |
| POST | `/talk-us/join-as-partner` | `we.join-as-partner` |

### 2) API Routes

| Method | URI | Middleware | Keterangan |
| --- | --- | --- | --- |
| GET | `/api/user` | `auth:sanctum` | Get user terautentikasi |

Catatan:

- Route `GET /sanctum/csrf-cookie` otomatis disediakan oleh Sanctum.

### 3) Admin Auth Routes (`/admin`)

| Method | URI | Name | Keterangan |
| --- | --- | --- | --- |
| GET | `/admin` | - | Redirect ke login |
| GET\|POST | `/admin/login` | `login` | Form + proses login |
| GET | `/admin/logout` | `logout` | Logout |

### 4) Admin Protected Routes (`middleware: auth`, name prefix `admin`)

Semua route berikut berada di bawah prefix `/admin`.

#### Dashboard & User

- `GET /main` -> `admin.main`
- `GET /users` -> `admin.users`
- `GET /users/remove/{id}` -> `admin.users`
- `GET /users/getOne/{id}` -> `admin.users`
- `POST /users/save` -> `admin.users`

#### Home Banner

- `GET /home/banner` -> `admin.home.banner`
- `GET /home/banner/remove/{id}` -> `admin.home.banner`
- `GET /home/banner/publish/{id}` -> `admin.home.banner`
- `POST /home/banner/save` -> `admin.home.banner`

#### Home Banner Menu

- `GET /home/banner-menu` -> `admin.home.banner-menu`
- `GET /home/banner-menu/removeMenu/{id}` -> `admin.home.banner-menu`
- `GET /home/banner-menu/publishMenu/{id}` -> `admin.home.banner-menu`
- `POST /home/banner-menu/saveMenu` -> `admin.home.banner-menu`

#### Product

- `GET /product` -> `admin.product`
- `GET /product/get/{id}` -> `admin.product`
- `GET /product/remove/{id}` -> `admin.product`
- `GET /product/publish/{id}` -> `admin.product`
- `POST /product/save` -> `admin.product`

#### Feedback Karir

- `GET /feedback/karir` -> `admin.feedback.karir`
- `GET /feedback/karir/applicants/{careerId}` -> `admin.feedback.karir`
- `GET /feedback/karir/getApplicant/{id}` -> `admin.feedback.karir`
- `GET /feedback/karir/approveApp/{id}` -> `admin.feedback.karir`
- `POST /feedback/karir/rejectApp` -> `admin.feedback.karir`
- `GET /feedback/karir/download-cv/{id}` -> `admin.feedback.karir`

#### Feedback Pertanyaan

- `GET /feedback/pertanyaan` -> `admin.feedback.pertanyaan`
- `GET /feedback/pertanyaan/get` -> `admin.feedback.pertanyaan`
- `GET|POST /feedback/pertanyaan/replied` -> `admin.feedback.pertanyaan`

#### Feedback Mitra

- `GET /feedback/mitra` -> `admin.feedback.mitra`
- `GET /feedback/mitra/get/{id}` -> `admin.feedback.mitra`
- `GET /feedback/mitra/replied/{id}` -> `admin.feedback.mitra`

#### Karir

- `GET /karir` -> `admin.karir`
- `GET /karir/add` -> `admin.karir`
- `GET /karir/edit/{id}` -> `admin.karir`
- `GET /karir/form` -> `admin.karir`
- `POST /karir/save` -> `admin.karir`
- `GET /karir/delete/{id}` -> `admin.karir`

#### CSR Env

- `GET /csr/env` -> `admin.csr.env`
- `GET /csr/env/add` -> `admin.csr.env`
- `GET /csr/env/edit/{id}` -> `admin.csr.env`
- `GET /csr/env/form` -> `admin.csr.env`
- `POST /csr/env/save` -> `admin.csr.env`
- `GET /csr/env/delete/{id}` -> `admin.csr.env`
- `GET /csr/env/publish/{id}` -> `admin.csr.env`

#### CSR Safety

- `GET /csr/safety` -> `admin.csr.safety`
- `GET /csr/safety/add` -> `admin.csr.safety`
- `GET /csr/safety/edit/{id}` -> `admin.csr.safety`
- `GET /csr/safety/form` -> `admin.csr.safety`
- `POST /csr/safety/save` -> `admin.csr.safety`
- `GET /csr/safety/delete/{id}` -> `admin.csr.safety`
- `GET /csr/safety/publish/{id}` -> `admin.csr.safety`

#### CSR Sosial

- `GET /csr/sosial` -> `admin.csr.sosial`
- `GET /csr/sosial/add` -> `admin.csr.sosial`
- `GET /csr/sosial/edit/{id}` -> `admin.csr.sosial`
- `GET /csr/sosial/form` -> `admin.csr.sosial`
- `POST /csr/sosial/save` -> `admin.csr.sosial`
- `GET /csr/sosial/delete/{id}` -> `admin.csr.sosial`
- `GET /csr/sosial/publish/{id}` -> `admin.csr.sosial`

#### News

- `GET /news` -> `admin.news`
- `GET /news/add` -> `admin.news`
- `GET /news/edit/{id}` -> `admin.news`
- `GET /news/form` -> `admin.news`
- `POST /news/save` -> `admin.news`
- `GET /news/delete/{id}` -> `admin.news`
- `GET /news/publish/{id}` -> `admin.news`

#### Testimoni

- `GET /testimoni` -> `admin.testimoni`
- `GET /testimoni/remove/{id}` -> `admin.testimoni`
- `POST /testimoni/save` -> `admin.testimoni`

#### Resep

- `GET /resep` -> `admin.resep`
- `GET /resep/add` -> `admin.resep`
- `GET /resep/edit/{id}` -> `admin.resep`
- `GET /resep/form` -> `admin.resep`
- `POST /resep/save` -> `admin.resep`
- `GET /resep/delete/{id}` -> `admin.resep`
- `GET /resep/publish/{id}` -> `admin.resep`

#### Ticketing

- `GET /ticket` -> `admin.ticket`
- `GET /ticket/show/{id}` -> `admin.ticket`
- `POST /ticket/update/{id}` -> `admin.ticket`

#### Email Config

- `GET /email-config` -> `admin.email-config`
- `GET /email-config/add` -> `admin.email-config`
- `GET /email-config/edit/{id}` -> `admin.email-config`
- `GET /email-config/form` -> `admin.email-config`
- `POST /email-config/save` -> `admin.email-config`
- `GET /email-config/delete/{id}` -> `admin.email-config`
- `GET /email-config/activate/{id}` -> `admin.email-config`

#### Email Log

- `GET /email-log` -> `admin.email-log`
- `GET /email-log/show/{id}` -> `admin.email-log`

## Route Checking

Untuk melihat route terbaru langsung dari aplikasi:

```bash
php artisan route:list
php artisan route:list --path=admin
php artisan route:list --path=api
```

## Testing

```bash
php artisan test
```

## Image Optimization (Spatie)

Project ini sudah menggunakan package:

- `spatie/laravel-image-optimizer`

Binary yang dipakai Spatie:

- `jpegoptim`
- `optipng`
- `pngquant`
- `gifsicle`

### Cek Binary di Server (cPanel Terminal/SSH)

Jalankan:

```bash
which jpegoptim
which optipng
which pngquant
which gifsicle
```

Jika semua ada path output, artinya binary sudah tersedia dan bisa langsung dipakai.

### Instalasi di Shared Hosting cPanel (tanpa root)

Di shared hosting biasanya Anda tidak punya akses `apt/yum`, jadi gunakan salah satu cara berikut.

1. Cara paling aman (recommended): minta provider hosting install binary global:
   - `jpegoptim`
   - `optipng`
   - `pngquant`
   - `gifsicle`
2. Jika provider tidak bisa install global, gunakan binary custom per-user:
   - Buat folder binary:

```bash
mkdir -p /home/sidoagu1/bin/image-optimizer
```

   - Download/upload binary Linux x86_64 (static build) ke folder tersebut dengan nama:
     - `/home/sidoagu1/bin/image-optimizer/jpegoptim`
     - `/home/sidoagu1/bin/image-optimizer/optipng`
     - `/home/sidoagu1/bin/image-optimizer/pngquant`
     - `/home/sidoagu1/bin/image-optimizer/gifsicle`
   - Set permission executable:

```bash
chmod +x /home/sidoagu1/bin/image-optimizer/jpegoptim
chmod +x /home/sidoagu1/bin/image-optimizer/optipng
chmod +x /home/sidoagu1/bin/image-optimizer/pngquant
chmod +x /home/sidoagu1/bin/image-optimizer/gifsicle
```

### Set Path Binary di Laravel

Edit file `config/image-optimizer.php` pada key `binary_path`:

```php
'binary_path' => '/home/sidoagu1/bin/image-optimizer/',
```

Lalu jalankan:

```bash
php artisan config:clear
php artisan cache:clear
```

### Verifikasi

Jalankan:

```bash
/home/sidoagu1/bin/image-optimizer/jpegoptim --version
/home/sidoagu1/bin/image-optimizer/optipng -v
/home/sidoagu1/bin/image-optimizer/pngquant --version
/home/sidoagu1/bin/image-optimizer/gifsicle --version
```

Catatan:

- Jika command di atas gagal, binary belum cocok dengan OS server Anda.
- Pastikan fungsi proses di PHP tidak diblok (`proc_open`, `exec`, `shell_exec`), karena Spatie mengeksekusi binary lewat process.
- Jika hosting membatasi binary custom, gunakan opsi request ke provider agar binary diinstall global.

## Queue Job (Ticketing & Notification)

Project ini memakai job:

- `App\Jobs\TicketingJob`
- `App\Jobs\NotificationJob`

### Menjalankan Job di Lokal

Secara default `.env.example` menggunakan:

```env
QUEUE_CONNECTION=database
```

Karena mode async dipakai, worker perlu dijalankan terpisah.

1. Pastikan tabel queue sudah ada (migration `2026_03_10_014221_create_jobs_table.php`):

```bash
php artisan migrate
```

2. Jalankan worker di terminal terpisah:

```bash
php artisan queue:work database --queue=tickets,emails,default --sleep=1 --tries=3 --timeout=120
```

3. Cek job gagal:

```bash
php artisan queue:failed
php artisan queue:retry all
```

Catatan throttling email:

- `NotificationJob` memakai Job Middleware `RateLimiter` dengan limit global `1 job/jam`.
- Limit ini berlaku untuk seluruh jenis email di `NotificationJob` (termasuk `auto-reply` dan `notification-admin`) tanpa pengecualian.
- Untuk event `ticket-created`, email admin dijadwalkan sebagai job terpisah dengan delay `1 jam` setelah auto-reply customer.

### Menjalankan Job di cPanel Shared Hosting

Di shared hosting biasanya tidak ada Supervisor, jadi worker dijalankan via Cron Job.

1. (Opsional) Jika ingin membuat class job langsung di server (via Terminal cPanel):

```bash
php artisan make:job NamaJobBaru
```

Catatan:

- Lokasi file job akan dibuat di `app/Jobs`.
- Jika terminal cPanel tidak tersedia, buat job di lokal lalu deploy ke server.

2. Set `.env` production:

```env
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
QUEUE_CONNECTION=database
```

3. Jalankan migration di server (sekali saat deploy/ubah schema):

```bash
php artisan migrate --force
```

4. Pasang cron scheduler:

```bash
/bin/bash -lc 'cd /home/sidoagu1/sidoagungfarm && /usr/local/bin/php artisan schedule:run --no-interaction >> storage/logs/log-schedule.log 2>&1'
```

5. Tidak perlu terminal interaktif. Cukup pastikan cron scheduler aktif setiap menit.

Pada project ini, saat `QUEUE_CONNECTION=database`, worker queue sudah dijalankan oleh scheduler lewat `routes/console.php` dengan command:

```bash
php artisan queue:work database --queue=tickets,emails,default --sleep=1 --tries=3 --timeout=120 --stop-when-empty
```

Jika ingin worker dipisah dari scheduler, Anda bisa menambahkan cron worker terpisah:

```bash
/bin/bash -lc 'cd /home/sidoagu1/sidoagungfarm && /usr/local/bin/php artisan queue:work database --queue=tickets --sleep=1 --tries=3 --stop-when-empty --no-interaction >> storage/logs/log-queue-tickets.log 2>&1'
/bin/bash -lc 'cd /home/sidoagu1/sidoagungfarm && /usr/local/bin/php artisan queue:work database --queue=emails --sleep=1 --tries=3 --stop-when-empty --no-interaction >> storage/logs/log-queue-emails.log 2>&1'
```

Alternatif path PHP bawaan cPanel:

```bash
/usr/local/bin/php /home/sidoagu1/sidoagungfarm/artisan schedule:run --no-interaction >> /home/sidoagu1/sidoagungfarm/storage/logs/log-schedule.log 2>&1
```

Catatan:

- Pastikan folder log tersedia:
```bash
mkdir -p /home/sidoagu1/sidoagungfarm/storage/logs
```
- Path project harus menunjuk folder root Laravel (folder yang berisi file `artisan`), bukan folder `public`.
- Saat mode async, gunakan queue name `tickets` dan `emails` sesuai job pada project ini.
- Untuk melihat error queue, cek `storage/logs/laravel.log`, `storage/logs/log-schedule.log`, `storage/logs/log-queue-tickets.log`, dan `storage/logs/log-queue-emails.log`.

## Systemd Service Template

Template service untuk auto-start dan auto-restart worker ada di:

- `deploy/systemd/laravel-queue-worker.service`
- `deploy/systemd/README.md`

Catatan penting:

- `systemd` hanya bisa dipakai di VPS/dedicated server (butuh akses root).
- Pada shared hosting cPanel tanpa terminal/root, gunakan Cron Jobs via UI cPanel (lihat `deploy/systemd/README.md`).
