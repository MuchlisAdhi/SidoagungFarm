# Image Optimizer Binary Bundle

This folder builds a Linux amd64 bundle for:

- `jpegoptim`
- `optipng`
- `pngquant`
- `gifsicle`

The process downloads official Debian `.deb` packages, extracts binaries from `usr/bin`, and packages them into:

- `packages/image-optimizer-linux-amd64.tar.gz`

## Build

```bash
make package
```

## Clean

```bash
make clean
```

## Deploy to cPanel

1. Upload extracted binaries to:
   - `/home/sidoagu1/bin/image-optimizer/`
2. Set file permissions to `0755` for all binaries.
3. Set `.env`:

```env
IMAGE_OPTIMIZER_BINARY_PATH=/home/sidoagu1/bin/image-optimizer/
```

4. Clear config cache:

```bash
php artisan optimize:clear
```

## Notes

- These are dynamically linked Linux binaries.
- Compatibility depends on your hosting OS/glibc version.
- If one binary fails to execute on server, replace that binary with a compatible build.
