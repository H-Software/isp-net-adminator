# export MYSQL_SERVER=192.168.1.213
export MYSQL_SERVER=127.0.0.1

export MYSQL_USER=root
export MYSQL_PASSWD=isp-net-passwd

# export POSTGRES_SERVER=192.168.1.213
export POSTGRES_SERVER=127.0.0.1
export POSTGRES_USER=adminator
export POSTGRES_PASSWD=isp-net-passwd
export POSTGRES_DB=adminator.new

# .PHONY: run-a2-php-local	
# run-a2-php-local:
# 	cp adminator3/include/main.function.shared.php adminator2/include/main.function.shared.php \
# 	&& mkdir -p adminator2/smarty \
# 	&& cp -a libs/smarty/ adminator2/smarty/ \
# 	&& cp -a adminator3/models/adminator2 adminator2/include \
# 	&& cd adminator2 \
# 		&& php \
# 			 -c ../configs/php/local.ini \
# 			 -S localhost:8088 \
# 			 index-local-router.php

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
