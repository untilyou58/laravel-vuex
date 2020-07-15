# laravel-vuex
Learn about vuex SPA, laravel 7.0

# Prerequisites
- Install [docker](https://docs.docker.com/engine/install/ubuntu/)
- Install [docker-compose](https://docs.docker.com/compose/install/)
- Window/Mac/ubuntu Os

# Install
- Create env file in web folder
- Fill variables inside env file

```env
DB_CONNECTION=pgsql
DB_HOST=vuesplash_database
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=secret

AWS_ACCESS_KEY_ID=[ACCESS_KEY_ID_AWS_IAM]
AWS_SECRET_ACCESS_KEY=[SECRET_KEY_AWS_IAM]
AWS_DEFAULT_REGION=[YOUR_REGION_AWS]
AWS_BUCKET=[BUCKET_NAME]
AWS_URL=https://s3-[YOUR_REGION_AWS].amazonaws.com/[BUCKET_NAME]/
```
- Build with docker
```command
docker-cmpose build
docker-compose up -d
```

- Generate APP_KEY
```command
docker-compose exec vue_web php artisan key:generate
```

- Run server with port 8081
```command
docker-compose exec vue_web php artisan serve --host 0.0.0.0 --port 8081
```

- Run npm run watch
```command
docker-compsoe exec vue-web npm run watch
```

- Migrate table for database
```command
docker-compose exec vue_web php artisan migrate
```

- Run on browser with host: `[DOCKER_IP]:3002`
