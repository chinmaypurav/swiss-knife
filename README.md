# Swiss Knife

A collection of handy utility tools built with Laravel, DaisyUI, and Alpine.js.

> ⚠️ **Warning:** This application is a work in progress and does not include any authentication. It is intended for local use only. Do not expose it to the public internet. ⚠️

## Tools

### S3 File Browser

Browse, upload, download, and generate signed URLs for files stored in Amazon S3 (or S3-compatible services like MinIO).

**Route:** `/files/s3`

## Environment Variables

Copy `.env.example` to `.env` and configure the following:

### Application

| Variable | Description |
|----------|-------------|
| `APP_KEY` | Application encryption key. Generate with `php artisan key:generate` |

### AWS / S3

| Variable | Description |
|----------|-------------|
| `AWS_ACCESS_KEY_ID` | Your AWS access key |
| `AWS_SECRET_ACCESS_KEY` | Your AWS secret key |
| `AWS_DEFAULT_REGION` | AWS region (e.g. `ap-south-1`) |
| `AWS_BUCKET` | S3 bucket name |
| `AWS_ENDPOINT` | Custom endpoint URL (optional, for S3-compatible services like MinIO) |
| `AWS_USE_PATH_STYLE_ENDPOINT` | Set to `true` when using MinIO or other path-style services |

## Setup (Local)

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
npm run build
```

## Setup (Docker)

Build the production image:

```bash
docker build --target deploy -t swiss-knife .
```

Run the container, passing the required environment variables:

```bash
docker run -d -p 8080:8080 \
  -e APP_KEY=base64:your-generated-key \
  -e AWS_ACCESS_KEY_ID=your-access-key \
  -e AWS_SECRET_ACCESS_KEY=your-secret-key \
  -e AWS_DEFAULT_REGION=ap-south-1 \
  -e AWS_BUCKET=your-bucket \
  swiss-knife
```

Generate an `APP_KEY` beforehand with:

```bash
docker run --rm swiss-knife php artisan key:generate --show
```

The app will be available at `http://localhost:8080`.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
