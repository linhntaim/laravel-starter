# Laravel Starter

## Commands

### Check version

```
php artisan about
```

### Limit the client to access

```
php artisan client:limit {--u} {--allow=} {--deny=} {--admin}
```

- `--u`: Remove all limitation.
- `--allow`: List of IPs, separated by comma. Only these IPs can access.
- `--deny`: List of IPs, separated by comma. Only these IPs cannot access.
- `--admin`: Limit with admin site only.

### Setup migration

```
php artisan setup:migration {--u} {--key} {--dummy-data}
```

- `--u`: Remove all tables and some files to run the application.
- `--key`: Enable to generate application key.
- `--dummy-data`: Enable to generate dummy data.

### Setup dummy data

```
php artisan setup:dummy-data {--u}
```

- `--u`: Truncate all data from tables related to events.

### Setup data for testing

```
php artisan setup:test-data {--u}
```

- `--u`: Truncate all data from tables related to events.

### Test to send mail

```
php artisan test:send-mail
```

### Update password
```
php artisan update:password {email} {--password=}

```
- `email`: User's email need for updating password
- `--password`: The password needs to update. Leave empty for auto-generating random password.

### Try something

Go into `app\Console\Commands` and clone the file `TryCommand.php.example` to `TryCommand.php`. Then change some codes in it and run:

```
php artisan try
```
