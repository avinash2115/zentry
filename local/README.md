# Zentry and Portal Local Development

Install Docker and Docker compose for your platform

Checkout api, portal, zentry-api, zentry-web and local with the following folder structure

>Project Main Folder\
 |\
 |-->api\
 |\
 |-->local\
 |\
 |-->portal\
 |\
 |-->zentry-api\
 |\
 |-->zentry-web

## Portal

    yarn 

install yarn packages in both portal, api and zentry-web

All below commands should be executed in the local folder

## Run all

    docker-compose up

First time zentry run requires the following two commands to be executed for migrations

```
docker-compose exec php-fpm php artisan migrate
docker-compose exec php-fpm php artisan db:seed

```


## Run portal

    docker-compose up -d portal api 


## Run Zentry

    docker-compose up -d zentry-api zentry-web

## Stop Environment

    docker-compose stop

You can also stop and restart individual containers by name

## Destroy Environment

     docker-compose down


## Build Chat Application

In the Chat folder execute the following commands to build the application

```
meteor >/dev/null 2>&1 || curl https://install.meteor.com | sed s/--progress-bar/-sL/g | /bin/sh

meteor npm install

meteor build ./build --directory --server-only --allow-superuser --debug
```

Copy docker file to build folder, still in the chat folder

     cp ./.docker/Dockerfile ./build/Dockerfile

Run the following command in the local folder to start the chat images

     docker-compose up chat mongo mongoclient
## Build Platform Application
Rename config directory database.docker.yml to database.yml, amazon_s3.docker.yml to amazon_s3.yml and firebase.docker.yml to firebase.yml
## Run Platform

    docker-compose up -d portal api platform 
