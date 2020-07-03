# Product's Live

## Commands

### Check version

```
php artisan about
```

### Limit the client to access

```
php artisan client:limit {--none} {--allow=} {--deny=} {--admin}
```

- `--none`: Remove all limitation.
- `--allow`: List of IPs, separated by comma. Only these IPs can access.
- `--deny`: List of IPs, separated by comma. Only these IPs cannot access.
- `--admin`: Limit with admin site only.

### Setup migration

```
php artisan setup:migration {--u} {--dummy-data}
```

- `--u`: Remove all tables and some files to run the application.
- `--dummy-data`: Enable to generate test data.

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
- `email`: User's email need to update password
- `--password=`Input your options to generate new password

### Try something

Go into `app\Console\Commands` and clone the file `TryCommand.php.example` to `TryCommand.php`. Then change some codes in it and run:

```
php artisan try
```