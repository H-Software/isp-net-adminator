# isp-net-adminator
administration system for Internet Service Provider (ISP)

<!--ts-->
<!--te-->

## description
- T.B.A.
### sections
### integrations

# local development

## with devcontainers
- https://containers.dev/
### Prerequisites
- devcontainers/cli or vscode + devcontainer extentsion

### run php-fpm inside devcontainer
```bash
sudo -E bash docker-php-entrypoint php-fpm
```

## without docker

### Prerequisites
- make
- brew (on MAC OS X)

### on MAC OS X
- install PHP from from https://github.com/shivammathur/homebrew-php
```bash
brew tap shivammathur/php
brew install shivammathur/php/php@8.2
brew link --overwrite --force shivammathur/php/php@8.2
```
- create folder for extension (some workaround)
```bash
mkdir -p /usr/local/lib/php/pecl
```
- install extensions
```bash
pecl install apcu
pecl install protobuf
pecl install opentelemetry
```
- run local server
```bash
make run-a3-php-local
```

## with docker / docker-compose
- just run docker-compose as usual

### Prerequisites
- make
- container engine

### rebuild fpm container
```bash
docker-compose up -d fpm --build
```

## MS SQL

#### test connection and create database
##### mssql-tools17
```bash
sqlcmd config add-endpoint --name local --address 127.0.0.1
sqlcmd config add-user --name sa --username SA --password-encryption none
sqlcmd config add-context --name local --user sa --endpoint local
sqlcmd query "create database StwPh_26109824_2024"
sqlcmd query "SELECT Name from sys.databases;"
```

## Author
Patrik Majer

## Licence
MIT
