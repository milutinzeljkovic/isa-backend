# Laravel JWT in Docker
Boilerplate for Laravel with JWT auth wrapped in Docker.

# What is this?

This repo will get you up and running in no time. 
Basically this is clean laravel 5.6 project with JWT authentication implemented.
Package that is used for JWT authentication is `tymon/jwt-auth`, this will save you a lot of time because JWT configuration in laravel can be pain in the ass.

Things that are added :
* `User` model with name, email and password attributes
* `AuthController` with register, login, me and logout routes
* `UserService` with register, login, me and logout methods
* `RegisterApiRequest` request with name, email and password validation
* `Jsonify` middleware that will set header `Accept: application/json`
* JWT authentication

And thats it, no unnecessary craps, just things that must be implemented to have JWT authentication working.

# What about Docker ?

Whole stack is dockerized so it can boost your development productivity and let you focus on code, instead of configuration.
Docker related files :

* `docker-compose.yml` - YAML file defining services, networks and volumes
* `Dockerfile` - file that is building our nginx-php instance
* `config/default.conf` - Laravel optimized NGINX vhost configuration file

All that you need to do first time starting app is :
```
cp src/.env.example src/.env
docker-compose up -d
docker exec -it nginx-php php /app/artisan key:generate
docker exec -it nginx-php php /app/artisan jwt:secret
```
> If everything went well, your app will be live on http://localhost (port 80)

As we are using volume to map `./src` to `/app` in container, all changes in host source files will be immediately visible in container.

# How can I access nginx or mariadb?

You can access bash shell in your nginx-php instance with `docker exec -it nginx-php /bin/bash`.  
  
MariaDB is mapped to your host so you can use client tool of your preference and point it to `127.0.0.1` with username/password from `docker-compose.yml`.
