# Zentry API Documentation

# Git and branches

> {TYPE}/{TICKET_NAME}/{DESCRIPTION}

**Example:**

> feature/ZENT-82/auth-package

<dl>
  <dt>Available types:</dt>
  <dd>feature</dd>
  <dd>hotfix</dd>
  <dd>bugfix</dd>
</dl>

# Installation

*Please download & install latest versions from:*

```
https://store.docker.com/search?offering=community&type=edition
https://docs.docker.com/compose/install/
```

*Add to a hosts file on your local machine:*

```
127.0.0.1 api.zentry.local
```

*For OSX users:*

```
sudo rm -rf docker/data/*
mkdir docker/data/percona
mkdir docker/data/mongo
mkdir docker/data/elasticsearch
docker volume rm $(docker volume ls -q --filter dangling=true)
sh docker/osx/nfs.sh
```

*.env file:*

```
cp docker/.env.example docker/.env
```

*Edit docker/.env and replace placeholder with real values*

*!WARNING! if you running compose outside of the docker folder, please run before docker-compose up*
```
source docker/.env
```

*Run:*
```
cd docker
docker login registry.gitlab.trisk.us (your standard credentials to a GitLab)
docker-compose up -d --build
```

*Workspace:*

You are able to run commands and work in a php-fpm container.
It allows you to run any needed command without ssh into it

First of all:

```
docker-compose exec php-fpm composer install
```

Examples:

```
docker-compose exec php-fpm php artisan migrate
docker-compose exec php-fpm php artisan db:seed
docker-compose exec php-fpm {service} {args}
```

*Available containers:*

```
| Container     | Port   | Version  | Credentials                          |
|---------------|--------|----------|--------------------------------------|
| percona       | 3306   | latest   | root/root                            |
| mongo         | 27017  | latest   | authless                             |
| redis         | 6379   | latest   | authless                             |
| php-fpm       | 9000   | 7.4      | authless                             |
| nginx         | 8080   | latest   | authless                             |
| node          | 6001   | latest   | authless                             |
| elasticsearch | 9200   | latest   | authless                             |

```

*Data stateless and logs:*

All logs and containers data could be found at *./docker/data* *./docker/logs*


