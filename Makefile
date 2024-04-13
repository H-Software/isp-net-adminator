
export MYSQL_SERVER=192.168.1.213
export MYSQL_USER=root
export MYSQL_PASSWD=isp-net-passwd

export POSTGRES_SERVER=192.168.1.213
export POSTGRES_USER=adminator
export POSTGRES_PASSWD=isp-net-passwd
export POSTGRES_DB=adminator.new

.PHONY: run-a3-php-local	
run-a3-php-local:
	cd adminator3 \
		&& php \
			 -c ../configs/php/local.ini \
			 -S localhost:8080 \
			 index-local-router.php

run-composer-local:
	cd adminator3 \
		&& php \
			composer.phar \
			install
