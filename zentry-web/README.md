# Welcome to project. Please follow next rules during development process!

# Git and branches

> {TYPE}/{TICKET_NAME}/{DESCRIPTION}

**Example:**

> feature/ZENT-1/assets-preparation

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
127.0.0.1 zentry.local
```

*Create file src/index.js.*
*Copy src/index.example.js to src/index.js and configure environment variables.*

*For OSX users:*

```
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
docker-compose -p zentry-web up -d --build
```
*Workspace:*

You are able to run commands and work in a node container.
It allows you to run any needed command without ssh into it

First of all:

```
docker-compose -p zentry-web exec node npm install
docker-compose -p zentry-web exec node ng serve --host 0.0.0.0 --port 4200
```

**Your app will be available on 127.0.0.1:4200**

**Before pushing to code review please build app (nginx already listening dist directory) and navigate into zentry.local**

Examples:

```
docker-compose -p zentry-web exec node npm run build
docker-compose -p zentry-web exec node {service} {args}
```


*Available containers:*
```
| Container  | Port   | Version  | Credentials                          |
|------------|--------|----------|--------------------------------------|
| node       | 4200   | 12.2.0   | authless                             |
| nginx      | 80     | latest   | authless                             |

```

