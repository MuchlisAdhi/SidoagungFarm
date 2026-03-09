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
- Panel admin (prefix `/wongelek`):
  - Login/logout admin
  - Manajemen user
  - Home banner & banner menu
  - Produk
  - Feedback (karir, pertanyaan, mitra)
  - Karir
  - CSR (env, safety, sosial)
  - News
  - Testimoni
  - Resep

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

### 3) Admin Auth Routes (`/wongelek`)

| Method | URI | Name | Keterangan |
| --- | --- | --- | --- |
| GET | `/wongelek` | - | Redirect ke login |
| GET\|POST | `/wongelek/login` | `login` | Form + proses login |
| GET | `/wongelek/logout` | `logout` | Logout |

### 4) Admin Protected Routes (`middleware: auth`, name prefix `admin`)

Semua route berikut berada di bawah prefix `/wongelek`.

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
- `GET /feedback/pertanyaan/replied` -> `admin.feedback.pertanyaan`

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

## Route Checking

Untuk melihat route terbaru langsung dari aplikasi:

```bash
php artisan route:list
php artisan route:list --path=wongelek
php artisan route:list --path=api
```

## Testing

```bash
php artisan test
```
