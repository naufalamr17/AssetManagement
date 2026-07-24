# SIMA — Sistem Informasi Manajemen Aset

SIMA is a Laravel application for recording, tracking, repairing, disposing of, and reporting company assets for PT MLP and PT KES.

## Requirements

- PHP 8.1+
- Composer
- MySQL/MariaDB
- Node.js only if you plan to rebuild front-end assets

## Installation

```sh
git clone https://github.com/naufalamr17/AssetManagement.git
cd AssetManagement
composer install
copy .env.example .env
php artisan key:generate
```

Set the database credentials in `.env`, then run the migrations:

```sh
php artisan migrate --seed
php artisan serve
```

Use `php artisan migrate:fresh --seed` only for a disposable development database: it deletes all existing records.

## Multi-company support

Every user and asset belongs to either `MLP` or `KES`. Existing records are migrated to `MLP` automatically. Administrators assign a company when creating a user; normal users only see and operate on assets in their own company. Super Admin users can work across both companies.

Asset codes use the same location/category logic for both companies. MLP keeps its existing format; KES codes receive the requested prefix:

| Company | Example code |
| --- | --- |
| PT MLP | `FG 03-01-0001` |
| PT KES | `KES FG 03-01-0001` |

The sequence is generated from the highest existing number for the same company and code prefix, so different companies do not affect each other's numbering.

## Feature matrix

| Feature | Viewer / Auditor | Creator | Modified | Administrator | Super Admin |
| --- | --- | --- | --- | --- | --- |
| View dashboard, assets, history, repair and disposal records | Yes | Yes | Yes | Yes | Yes |
| Create assets / upload barcode data | No | Yes | Yes | Yes | Yes |
| Update assets, repair status and disposal requests | No | Yes | Yes | Yes | Yes |
| Delete assets | No | No | No | Yes | Yes |
| Import Excel | No | No | No | Yes | Yes |
| Manage users | No | No | No | Yes | Yes |
| Company visibility | Assigned company | Assigned company | Assigned company | Assigned company | MLP and KES |

## Excel import

Download the provided template from **Import Data**. Select the target company before importing when signed in as Super Admin. For all other administrators, the import is automatically assigned to their own company. The required location and category values must match the available form options so a valid code can be generated.

## Verification

```sh
php artisan test
php artisan route:list
```
