# isp-net-adminator
administration system for Internet Service Provider (ISP)

## bootstrap
- docker run docker compose up

### MS SQL

#### test connection and create database
```
sqlcmd config add-endpoint --name local --address 192.168.1.213
sqlcmd config add-user --name sa --username SA --password-encryption none
sqlcmd config add-context --name local --user sa --endpoint local
sqlcmd query "create database StwPh_26109824_2024"
sqlcmd query "SELECT Name from sys.databases;"
```

## links
- https://hub.docker.com/_/php/tags?page=&page_size=&ordering=&name=5.6

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
- https://github.com/bryanjhv/slim-session/tree/3.7.2
#### slim twig
- https://packagist.org/packages/slim/twig-view
#### slim auth
- https://discourse.slimframework.com/t/slim-framework-3-skeleton-application-has-authentication-mvc-construction/2088
- https://github.com/HavenShen/slim-born/tree/v1.0.6
#### slim-auth w zend
- https://github.com/marcelbonnet/slim-auth
- https://github.com/marcelbonnet/slim-allinone-template
- https://packagist.org/packages/marcelbonnet/slim-auth
- https://docs.laminas.dev/laminas-session/
#### doctrine
- https://filip-prochazka.com/blog/doctrine-a-service-vrstva-aneb-takto-mi-to-dava-smysl
- https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/tutorials/getting-started.html
#### Eloquent
- https://laravel.com/docs/8.x/eloquent
- https://fideloper.com/laravel-multiple-database-connections
- https://medium.com/@miladev95/how-to-use-multiple-databases-in-laravel-274df382fecd

### apache
- https://httpd.apache.org/docs/2.4/rewrite/remapping.html

### MSSQL
- https://learn.microsoft.com/en-us/sql/linux/quickstart-install-connect-docker?view=sql-server-linux-2017&preserve-view=true&tabs=cli&pivots=cs1-bash#pullandrun2017
- https://pecl.php.net/package/sqlsrv
- https://learn.microsoft.com/en-us/sql/connect/php/system-requirements-for-the-php-sql-driver?view=sql-server-ver16#odbc-driver

## Author
Patrik Majer

## Licence
MIT
