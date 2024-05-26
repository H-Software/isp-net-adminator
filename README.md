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

## links
- https://hub.docker.com/_/php/tags?page=&page_size=&ordering=&name=5.6
- https://containrrr.dev/watchtower/

### devcontainers
- https://github.com/microsoft/vscode-remote-try-php
- https://docs.dapr.io/developing-applications/local-development/ides/vscode/vscode-remote-dev-containers/

### postgres
- https://medium.com/@agusmahari/docker-how-to-install-postgresql-using-docker-compose-d646c793f216
- https://hub.docker.com/_/postgres

### PHP
- https://github.com/asimlqt/docker-php/blob/master/apache/5.6-dev/etc/php.ini
- https://github.com/nette/robot-loader/tree/v3.1.4
- https://dev.to/mtk3d/how-to-configure-php-logs-for-docker-2384

#### Illuminate
- https://github.com/mattstauffer/Torch

#### forms
- https://symfony.com/doc/3.x/forms.html
- https://packagist.org/packages/symfony/form#v3.4.47
- https://laravel.com/docs/4.2/html
- https://github.com/formr/formr
- https://packagist.org/packages/formr/formr#1.4.6

#### validation
- https://respect-validation.readthedocs.io/en/1.1/feature-guide/

#### smarty
- https://smarty-php.github.io/smarty/4.x/upgrading/
- https://github.com/smarty-php/smarty
- https://smarty-php.github.io/smarty/stable/
- https://github.com/mathmarques/Smarty-View/tree/1.x
- https://smarty-php.github.io/smarty/4.x/designers/language-builtin-functions/language-function-foreach/#iteration

#### slim
- https://www.slimframework.com/docs/v3/tutorial/first-app.html
- https://github.com/slimphp/Tutorial-First-Application/blob/master/src/public/index.php
- https://odan.github.io/slim4-skeleton/configuration.html
#### slim twig
- https://packagist.org/packages/slim/twig-view
#### slim auth Old)
- https://github.com/darkalchemy/Slim-Auth
- https://discourse.slimframework.com/t/slim-framework-3-skeleton-application-has-authentication-mvc-construction/2088
- https://github.com/HavenShen/slim-born/tree/v1.0.6
#### slim-auth w zend/laminas
- https://github.com/marcelbonnet/slim-auth
- https://github.com/marcelbonnet/slim-allinone-template
- https://packagist.org/packages/marcelbonnet/slim-auth
- https://docs.laminas.dev/laminas-session/
#### auth over tokens/JWT
- https://github.com/pdscopes/slim-auth
#### doctrine
- https://filip-prochazka.com/blog/doctrine-a-service-vrstva-aneb-takto-mi-to-dava-smysl
- https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/tutorials/getting-started.html
#### Eloquent
- https://laravel.com/docs/10.x/eloquent
- https://fideloper.com/laravel-multiple-database-connections
- https://medium.com/@miladev95/how-to-use-multiple-databases-in-laravel-274df382fecd

#### Session
- https://odan.github.io/session/v6/

#### DI
- https://php-di.org/doc/php-definitions.html

#### sentinel
- https://github.com/cartalyst/sentinel/blob/2.0/src/Native/ConfigRepository.php

#### phinx
- https://siipo.la/blog/how-to-use-eloquent-orm-migrations-outside-laravel
- https://book.cakephp.org/phinx/0/en/migrations.html#custom-column-types-default-values
- https://book.cakephp.org/3/fr/phinx/commands.html#using-phinx-with-phpunit

#### testing
- https://github.com/adriansuter/Slim4-Skeleton/blob/master/tests/Controllers/HelloControllerTest.php
- https://medium.com/@selieshjksofficial/simplifying-database-unit-testing-in-php-with-phpunit-and-mock-databases-e251ab08f4eb

#### SimpleXML
- https://www.sanwebe.com/2013/08/creating-rss-feed-using-php-simplexml

#### locks / cache
- https://laravel.io/articles/preventing-duplicate-form-submissions-using-atomic-locks
- https://medium.com/@miladev95/laravel-cache-atomic-locks-3ddc10a052db
- https://laravel.com/docs/10.x/cache#managing-locks

### apache
- https://httpd.apache.org/docs/2.4/rewrite/remapping.html

### MSSQL
- https://learn.microsoft.com/en-us/sql/linux/quickstart-install-connect-docker?view=sql-server-linux-2017&preserve-view=true&tabs=cli&pivots=cs1-bash#pullandrun2017
- https://pecl.php.net/package/sqlsrv
- https://learn.microsoft.com/en-us/sql/connect/php/system-requirements-for-the-php-sql-driver?view=sql-server-ver16#odbc-driver

### Stormware Pohoda
- https://github.com/riesenia/pohoda
- https://github.com/VitexSoftware/PHP-Pohoda-Connector?tab=readme-ov-file
- https://github.com/Spoje-NET/PohodaSQL

### Mikrotik and RouterOS
- https://github.com/EvilFreelancer/routeros-api-php?tab=readme-ov-file
- https://github.com/EvilFreelancer/docker-routeros?tab=readme-ov-file
- https://github.com/ayufan/rosapi-php
  - originally used class

### Opentelemetry
- https://packagist.org/packages/open-telemetry/opentelemetry-auto-slim
- https://github.com/open-telemetry/opentelemetry-php-contrib/tree/main/src/Instrumentation/Slim
- https://github.com/open-telemetry/opentelemetry-php-contrib/tree/main/src/Logs/Monolog
- https://coralogix.com/docs/php-opentelemetry-instrumentation

### Github Actions
- https://github.com/marketplace/actions/continue-on-error-comment
- https://github.com/peter-evans/docker-compose-actions-workflow
- https://github.com/marketplace/actions/junit-report-action
- https://github.com/dorny/test-reporter
  - not compatible with java-junit
- https://github.com/EnricoMi/publish-unit-test-result-action

## Author
Patrik Majer

## Licence
MIT
