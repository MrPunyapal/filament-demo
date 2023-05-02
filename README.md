# Filament demo

## Steps
```bash
git clone https://github.com/mr-punyapal/filament-demo.git
```

```bash
composer install
```

```bash
cp .env.example .env
```
```bash
php artisan generate:key
```
```bash
php artisan storage:link
```
setup database in .env
```bash
php artisan migrate --seed
```

```bash
php artisan serv
```
