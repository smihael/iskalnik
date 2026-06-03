# Iskalnik po podatkovnih zbirkah

A small PHP search interface for Slovenian digital-humanities collections. The
application serves a landing page for configured collections and provides search,
filtering, and detail views backed by a MariaDB database.

## Contents

- `engline/` - current PHP application, API endpoints, public assets, and config.
- `admin/` - database administration/editor tools.
- `Dockerfile` and `docker-compose.yml` - PHP, MariaDB, and phpMyAdmin setup for local testing.

## Run locally

```sh
docker compose up --build
```

Then open:

- App: `http://localhost:8080`
- phpMyAdmin: `http://localhost:8081`

Local Docker values are loaded from `.env`. Use `.env.sample` as the template
when setting up a new checkout. The PHP app reads database values from
environment variables via `engline/config/db.php`.

## Configuration

Collections are configured in `engline/config/collections.php`. Search defaults
and allowed operators are configured in `engline/config/search.php`.

## Licence

This project is licensed under the Creative Commons Attribution-ShareAlike 4.0
International licence. See `LICENSE` for details.
