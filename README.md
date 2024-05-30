# isp-net-adminator
administration system for Internet Service Provider (ISP)

<!--ts-->
<!--te-->

## description
- T.B.A.
### sections
### integrations

# local development
## Prerequisites
- make
- PHP or container engine (docker)

## without docker
### on MAC OS X
- install PHP from from https://github.com/shivammathur/homebrew-php
```
brew tap shivammathur/php
brew install shivammathur/php/php@8.2
brew link --overwrite --force shivammathur/php/php@8.2
```
- create folder for extension (some workaround)
```
mkdir -p /usr/local/lib/php/pecl
```
- install extensions
```
pecl install apcu
pecl install protobuf
pecl install opentelemetry
```
- run local server
```
make run-a3-php-local
```

## with docker / docker-compose
- just run docker-compose as usual
### rebuild app container
```
docker compose up adminator --build
```

## MS SQL

#### test connection and create database
```
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
