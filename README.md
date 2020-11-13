# Laravel Starter

---

&raquo; [Commands](#commands)

&raquo; [Configuration](#configuration)

&raquo; [Features](#features)

---

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
- `--allow`: List of IPs, separated by the comma. Only these IPs can access.
- `--deny`: List of IPs, separated by the comma. Only these IPs cannot access.
- `--admin`: Limit with admin site only.

### Impersonate

Generate token to impersonate user.

```
php artisan impersonate {user} {admin_id}
```

- `user`: User for impersonating.
- `admin_id`: Admin who goes impersonating.

### Manual run a shell

```
php artisan shell:manual {shell}
```

- `shell`: Shell for running, usually put in double quotes, i.e: `"echo Hello world!"`.

### Setup web server

Make some configuration for web server if it needs to.

```
php artisan setup:web-server {--u} {--f}
```

- `--u`: Remove configuration made.
- `--f`: Forced to make fresh copies of configuration.

### Setup packages

Automatically install or remove extra packages based on the configuration if they need to.

```
php artisan setup:packages {--u} {--f}
```

- `--u`: Remove extra packages.
- `--f`: Forced to install or remove without any condition.

### Setup application's key

Generate the application's key if it does not exist.

```
php artisan setup:key:generate {--u} {--f}
```

- `--u`: Remove the key.
- `--f`: Forced to re-generate a new key.

### Setup storage link

Create all storage links if it does not exist.

```
php artisan setup:storage:link {--u} {--f}
```

- `--u`: Remove all storage links.
- `--f`: Forced to re-create all storage links.

### Setup migrate

Create all tables and seeds if it does not exist.

```
php artisan setup:migrate {--u} {--f}
```

- `--u`: Remove all tables.
- `--f`: Forced to re-create all tables and seeds.

### Setup default data

Seed default data.

```
php artisan setup:seed:default {--u} {--f}
```

- `--u`: Do nothing.
- `--f`: Do nothing.

### Setup dummy data

Seed dummy data.

```
php artisan setup:seed:dummy {--u} {--f}
```

- `--u`: Do nothing.
- `--f`: Do nothing.

### Setup test data

Seed test data.

```
php artisan setup:seed:test {--u} {--f}
```

- `--u`: Do nothing.
- `--f`: Do nothing.

### Setup

Setup **everything** if it needs to.

```
php artisan setup {--u} {--f} {--seed-dummy} {--seed-test} {--skip=*}
```

- `--u`: Remove everything.
- `--f`: Forced to re-setup everything.
- `--seed-dummy`: To seed dummy data.
- `--seed-test`: To seed test data.
- `--skip`: 
    - To skip some steps in set up.
    - Can be multiple values, i.e. to skip [Setup Packages](#setup-packages) 
    and [Setup Application Key](#setup-applications-key), 
    please run: `php artisan setup --skip=packages --skip=key:generate`.
    
**Everything** means to include:

- [Setup Web Server](#setup-web-server)
- [Setup Packages](#setup-packages)
- [Setup Application Key](#setup-applications-key)
- [Setup Storage Link](#setup-storage-link)
- [Setup Migrate](#setup-migrate)
- [Setup Default Data](#setup-default-data)
- [Setup Dummy Data](#setup-dummy-data) (optional)
- [Setup Test Data](#setup-test-data) (optional)

### Test to execute event

```
php artisan test:event
```

### Test to execute job

```
php artisan test:job
```

### Test to send notification

```
php artisan test:notification
```

### Test to send mail

```
php artisan test:mail
```

### Test to send mail by executing event

```
php artisan test:mail-event
```

### Update password

```
php artisan update:password {user} {--password=}
```

- `user`: Email or ID of user who needs to update password.
- `--password`: The password needs to update. Leave empty for auto-generating random password.

### Try something

Go into `app\Console\Commands` and clone the file `TryCommand.php.example` to `TryCommand.php` 
(or quickly, run [Make try command](#make-try-command)), then change some codes in it 
(find `TODO` block) and run:

```
php artisan try
```

### Make try command

Clone `TryCommand.php.example` to `TryCommand.php` in directory `app\Console\Commands` if it does not exist.
(`TryCommand.php` is ignored from Git)

```
php artisan make:command:try {--f}
```

- `--f`: Forced to make a fresh copy.

## Configuration

### Environment Variables

#### APP_ENV

Determine the environment to run.

Available values: 

- `production`: Run on production.
- `local`: Run on development.

#### APP_KEY

Used for encryption.

Value is set by running this command:

```
php artisan key:generate
```

Or by running the `setup:migration` command:

```
php artisan setup:migration --key
```

#### APP_DEBUG

Enable for debugging.

Available values:

- `true`: Enable.
- `false`: Disable.

#### APP_NAME

Display name of the application.

Value is string.

#### APP_ID

The unique name of the application.

Value is string. Should be valid with this regular expression: `^[a-z][a-z0-9_]*$`.

#### APP_URL

URL of the application. The application will get this URL as the root URL when running in the console.

#### APP_VERSION

Version of the application.

Value should be valid with this regular expression: `^\d+\.\d+\.\d+$`.

#### APP_LOCALE_SUPPORTED

Locales that the application supports.

Value is locales in ISO 639-1 format separated by comma.

#### LOG_CHANNEL

How the application writes logs.

Default value is `daily` which tells the application to write one log file per day.

#### DB_*

Setup the default database connection.

For multi-bytes characters stored in Unicode format, the database connection should use `utf8mb4` for encoding:

```
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

For MySQL database, to split READ/WRITE connection, modify those configurations: 

- **`DB_SPLIT`**:
    - Default value is `false`. 
    - Set the value to `true` to split.
- **`DB_READ_HOST`**:
    - Set the host for READ connection when `DB_SPLIT` is `true`.
- **`DB_WRITE_HOST`**:
    - Set the host for WRITE connection when `DB_SPLIT` is `true`.

#### MYSQL_ATTR_SSL_CA

For the default database connection by MySQL, if it is necessary to connect database server via SSL, 
place the absolute path to SSL certificate file here as the value.

For example, if this application is deployed to Azure App Service, the setting should be:

```
# Windows
MYSQL_ATTR_SSL_CA="D:\\home\\site\\wwwroot\\storage\\BaltimoreCyberTrustRoot.crt.pem"

# Linux
MYSQL_ATTR_SSL_CA="/home/site/wwwroot/storage/BaltimoreCyberTrustRoot.crt.pem"
```

- `BaltimoreCyberTrustRoot.crt.pem` The SSL certificate file which is used for connecting to 
the `Azure Database for MySQL server` 
([reference](https://docs.microsoft.com/en-us/azure/mysql/howto-configure-ssl)), 
which is already included in `storage` folder.

#### CACHE_DRIVER

For caching.

Default value is `file` and cache will be stored in local files.

If the application is deployed on multi-instance infrastructure, value should be set to `database`.
When value is set to `database`, a table named `sys_cache` will be automatically created when running migration
(see `database/migrations/2018_08_15_000000_create_cache_table.php` file) and cache will be stored in this table.

#### QUEUE_CONNECTION

For running queued jobs.

Default value is `sync` and queued jobs will be executed immediately when running code.

If you want to queue the jobs, value could be set to `database`.
When value is set to `database`, a table named `sys_jobs` will be automatically created when running migration 
(see `database/migrations/2018_08_16_000000_create_failed_jobs_table.php` file) and queued jobs will be stored in 
this table then wait for executing.

#### MAIL_*

Mail server settings:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=dsquare.gbu@gmail.com
MAIL_PASSWORD=oviajbqtrcgnalfk
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="${MAIL_USERNAME}"
MAIL_FROM_NAME="${APP_NAME}"
```

Some other setting to control how to send email:

```
MAIL_SEND_OFF=false
MAIL_SEND_RATE_KEY=mailing
MAIL_SEND_RATE_PER_SECOND=
MAIL_SEND_RATE_WAIT_FOR_SECONDS=1
MAIL_NO_REPLY_FROM_ADDRESS="${MAIL_FROM_ADDRESS}"
MAIL_NO_REPLY_FROM_NAME="${MAIL_FROM_NAME}"
MAIL_TESTED_USED=false
MAIL_TESTED_TO_ADDRESS=
MAIL_TESTED_TO_NAME=
```

- **`MAIL_SEND_OFF`**: Set the value to `true` if you don't want to send any email. Useful when doing massive tests 
without sending **massive emails**.
- **`MAIL_SEND_RATE_*`**: Control limit the rate of sending emails.
    - **`MAIL_SEND_RATE_KEY`**: Name of the cache storing the current rate of sending emails.
    - **`MAIL_SEND_RATE_PER_SECOND`**: Limitation of number of emails should be sent in a second.
    - **`MAIL_SEND_RATE_WAIT_FOR_SECONDS`**: Time in seconds the application should wait before sending next email 
    if the rate limit is reached.
- **`MAIL_NO_REPLY_*`**: If there is no `from` header set when sending email, the no-reply email account 
(**`MAIL_NO_REPLY_FROM_ADDRESS`** as email address and **`MAIL_NO_REPLY_FROM_NAME`** as name) will be set 
as default `from` header.
- **`MAIL_TESTED_USED`**: Set the value to `true` then all emails will be sent to the only tested-to email account 
(**`MAIL_TESTED_TO_ADDRESS`** as email address and **`MAIL_TESTED_TO_NAME`** as name) instead of email account 
set in `to` header. Useful when doing massive tests without sending massive emails to **massive users**.

** **Note**: Always use methods of `App\Utils\Mail\MailHelper` class to send any email.

#### TRUSTED_PROXIES

When deploying to hosting service, the value should be set to `*` (or specific proxy IPs separated by the comma).

#### PUBLIC_PATH

If you want not to use default `public` folder of Laravel (i.e `wwwroot`), change the value here.

#### PASSPORT_PASSWORD_*

Setup for migration with Passport.

```
PASSPORT_PASSWORD_CLIENT_ID=2
PASSPORT_PASSWORD_CLIENT_SECRET=<secret>
```

Currently, this setting is automatically configured by running [Setup migration](#setup-migration) command.

#### SOCIAL_LOGIN_*

- **`SOCIAL_LOGIN_ENABLED`**: 
    - Set the value to `true` if you want to enable the application to support the features of login with 
    social networks.
    - If value is set to `true`, a table named `user_socials` will ve automatically created when running migration. 
    (See `database/migrations/2019_11_13_000004_create_user_socials_table.php` file)
- **`SOCIAL_LOGIN_EMAIL_DOMAIN_*`**: Limit the email domains of social account can log in with. 
Domains should be separated by the comma.
    - **`SOCIAL_LOGIN_EMAIL_DOMAIN_ALLOWED`**: Allowed domains separated by the comma.
    - **`SOCIAL_LOGIN_EMAIL_DOMAIN_DENIED`**: Denied domains separated by the comma.

#### ADMIN_FORGOT_PASSWORD_ENABLED

Set the value to `true` to enable the application to handle the feature of forgetting password in admin site.

#### IMPERSONATED_BY_ADMIN_ENABLED

Set the value to `true` to enable the feature of impersonating.

#### THROTTLE_REQUEST_*

Set the limitation for accessing API with max access (set in **`THROTTLE_REQUEST_MAX_ATTEMPTS`**) in a time of minutes 
(set in **`THROTTLE_REQUEST_DECAY_MINUTES`**).

#### API_RESPONSE_OK

Set the value to `true` to force all the API responses to return status of `200 OK` (include error responses).

** **Note**: It will help the application to pass the 
[penetration test](https://homepage-gbu.azurewebsites.net/back-end/penetration-testing#problem-2-inconsistent-response).

#### FORCE_COMMON_EXCEPTION

Set the value to `true` to force all the API response to return the same message for all error caught.

#### FILESYSTEM_CLOUD

Set the default disk for cloud storage.

If the value is set to empty, `s3` will be used as default.

For Azure support, see [Azure Blob Storage Supported](#azure-blob-storage-supported). 

#### HANDLED_FILE_*

Uploaded/Created files should be handled by `App\Utils\HandledFiles` feature.

By default, files should be handled in local. 
To save information of files into the database, use the `App\ModelRepositories\HandledFileRepository` class.
When saving to database, there are some configuration to automatically do extra jobs as following:

##### HANDLED_FILE_CLOUD_*

To determine if files could be stored in the cloud.

- **`HANDLED_FILE_CLOUD_ENABLED`**: Set the value to `true` and every file will be additionally stored in the cloud.
- **`HANDLED_FILE_CLOUD_ONLY`**: Set the value to `true` and every file will be stored in the cloud, 
and the local ones will be deleted.

##### HANDLED_FILE_CLOUD_SERVICE_*

To enable to use cloud service. When any cloud service is enabled, 
the required packages will be automatically installed by running [Setup migrate](#setup-migrate) 
or [Setup packages](#setup-packages) command.

- **`HANDLED_FILE_CLOUD_SERVICE_S3`**: Set the value to `true` to enable Amazon S3. 
- **`HANDLED_FILE_CLOUD_SERVICE_AZURE`**: Set the value to `true` to enable Microsoft Azure Blog Storage. 

##### HANDLED_FILE_IMAGE_*

- **`HANDLED_FILE_IMAGE_MAX_*`**: 
    - If these configurations are set, the image will be automatically resized when it gets over the limitation 
    of **`HANDLED_FILE_IMAGE_MAX_WIDTH`** as maximum width and **`HANDLED_FILE_IMAGE_MAX_HEIGHT`** as maximum height.
    - Leave values empty for no limitation.
- **`HANDLED_FILE_IMAGE_INLINE`**: Set the value to `true` and images will be stored in the database 
instead of local or cloud.

#### ACTIVITY_LOG_ENABLED

Set the value to `true` to enable to store activity logs to database.
When value is set to `true`, a table named `activity_logs` will be automatically created when running migration 
(see `database/migrations/2019_08_16_000006_create_activity_logs_table.php` file) and activity logs could be stored 
in this table.

#### NOTIFICATION_*

##### NOTIFICATION_VIA_*

- **`NOTIFICATION_VIA_DATABASE`**: 
    - Set the value to `true` to enable to store notification to database.
    - When value is set to `true`, a table named `notifications` will be automatically created when running migration
      (see `database/migrations/2018_08_15_000002_create_notifications_table.php` file) 
      and notifications could be stored in this table.

#### VARIABLES

If there are extra unallocated configurations, put them as value of this setting in JSON format.

For example:

- Has this setting:

```
VARIABLES={"sample":"Value of sample"}
```

- ... access in code:

```
$variables = ConfigHeldper::get('variables);

print_r($variables);

/** Output:
Array (
    "sample" => "Value of sample"
)
**/
```

- ... or get it in response of Prerequisite API - Server:

```
/**
Request to Prerequisite API - Server
- URL: /api/prerequisite/?server=1
- Method: GET
**/

// Response
{
    ...
    "_data": {
        ...
        "variables": {
            "sample": "Value of sample"
        }
        ...
    }
    ...
}
```

#### CLIENT_LIMIT_TIMEOUT

The application has a feature of access limitation and this setting is used to set the time in seconds 
to cache the client limitation settings from database to local file.

Default value is `60` seconds.

#### CLIENT_*

Default settings for client of admin and home can be found here:

```
CLIENT_ADMIN_NAME="${APP_NAME}"
CLIENT_ADMIN_URL="${APP_URL}/admin"
CLIENT_ADMIN_COOKIE_DEFAULT_NAME="${APP_ID}"
CLIENT_ADMIN_SECRET=eRYppvYr3veR4vMsq3vdedSvu4D53XU3PkhmBW7HZT7VxbTpwYwjk5zUTv5zfBcj
CLIENT_HOME_NAME="${APP_NAME}"
CLIENT_HOME_URL="${APP_URL}"
```

The application now treats client as admin or home cause maybe the user sets and use cases of them is different.

For each client, the application needs to know the name and URL of it. 
Besides, the cookie settings is also needed for some cases.

#### HEADER_*

Set the name of some request headers.

##### HEADER_SCREEN_NAME

The current screen will be passed from client to the application via default `X-Screen` header.

##### HEADER_SETTINGS_NAME

Localization settings will be passed from client to the application via default `X-Settings` header.

##### HEADER_DEVICE_NAME

Device identification will be passed form client to the application via default `X-Device` header.

##### HEADER_TOKEN_AUTHORIZATION_NAME

Default value is empty.

If the name of authorization header sent from client is different from `Authorization`, 
please set it as value here (i.e. `X-Authorization`).

#### AZURE_*

See [Azure Blob Storage Supported](#azure-blob-storage-supported).

## Features

### Azure Blob Storage Supported

To add Azure Blob Storage as a disk for cloud storage.

This feature does require the package 
[matthewbdaly/laravel-azure-storage](https://github.com/matthewbdaly/laravel-azure-storage).

You can manually install it by running this command:

```
composer require matthewbdaly/laravel-azure-storage
```

... or **the better way**, set the value of `HANDLED_FILE_CLOUD_SERVICE_AZURE` in `.env` file to `true`, 
then run [Setup migrate](#setup-migrate) or [Setup packages](#setup-packages) command. 

Besides, there's configuration in `filesystems.php`:

```php
'disks' => [
    ...
    'azure' => [
        'driver'    => 'azure',
        'name'      => env('AZURE_STORAGE_NAME'),
        'key'       => env('AZURE_STORAGE_KEY'),
        'container' => env('AZURE_STORAGE_CONTAINER'),
        'url'       => env('AZURE_STORAGE_URL'),
        'prefix'    => null,
    ],
    ...
]
```

So, you can apply some value for them in `.env` file:

```
...
FILESYSTEM_CLOUD=azure
...
AZURE_STORAGE_NAME=<name>
AZURE_STORAGE_KEY=<key>
AZURE_STORAGE_CONTAINER=<container>
...
```
