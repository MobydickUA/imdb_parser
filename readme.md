# IMDB Parser

#### Requirements:
  - PHP >= 7.0
  - MySQL Server and php-mysql extension
  - php-curl extension

#### Deployment:
```sh
$ composer install
$ cp .env.exampe .env
```
After you've edited your .env file

```sh
$ php artisan key:generate
```
The database is required to use this application. You can use attched `dump.sql` file, or create database structure from scratch:
```sh
$ php artisan migrate
$ php artisan db:seed
```

To start parsing send `GET`request to `/api/parse` endpoint. Amount of profiles that will be parsed you can set in `configs` table (by default - 500).
All information about parsing you can find in logs (`__DIR__/storage/logs`)
