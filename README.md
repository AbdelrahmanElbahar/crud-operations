# crud-operations

Users, blogs and posts CRUD: a Laravel 12 API (`backend/`) and a PHP frontend (`frontend/`), run with Docker.

## Run

First time only:

```
cp backend/.env.example backend/.env
docker compose -f Docker/docker-compose.yml up -d --build
docker exec crud_backend php artisan key:generate --show   # paste the output into APP_KEY= in backend/.env
docker compose -f Docker/docker-compose.yml up -d backend   # reload .env
```

Then, and after every pull that adds a migration:

```
docker exec crud_backend php artisan migrate --force
```

- Frontend: http://localhost:8081
- API: http://localhost:8082/api/users, `/api/blogs`, `/api/posts`

## Tests

```
cd backend
composer install
php artisan test    # in-memory SQLite, never touches MySQL
```
