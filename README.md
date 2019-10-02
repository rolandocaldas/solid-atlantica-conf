# HOW TO INSTALL

## Requirements

- docker
- docker-composer

## Configure .env files

- Copy docker/.env.example to docker/.env
- Copy application/.env.dist to application/.env.dist

**Important:** You doesn't have to change the database values but if you want change it you have to modify the
MYSQL_* vars in docker/.env file and the DATABASE_URL in application/.env

## Configure Twitter API

Edit the application/.env file and fill the TWITTER_* vars with your application values of https://dev.twitter.com

## Run the application

### Up the containers

Open your terminal and go to the docker folder:

```$ cd [your-project]/docker ```

```$ docker-compose up -d```

The first time that you run the application you need to load the dependencies and execute the doctrine migrations:

```$ docker-compose exec php-fpm bash```

```# composer install```

```# php bin/console doctrine:migrations:migrate```

```# exit```

### Access to the application

Open your navigator and write the URL http://localhost:8080/