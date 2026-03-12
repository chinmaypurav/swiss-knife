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

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
npm run build
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
