#
# docker compose watch fpm nginx --no-up
# docker compose exec -ti fpm tail -f adminator3/logs/app.log"
# docker compose logs fpm nginx -f"
#
export MYSQL_SERVER=127.0.0.1

export MYSQL_USER=root
export MYSQL_PASSWD=isp-net-passwd

export POSTGRES_SERVER=127.0.0.1
export POSTGRES_USER=adminator
export POSTGRES_PASSWD=isp-net-passwd
export POSTGRES_DB=adminator.new

run-composer-local:
	cd adminator3 \
		&& php \
			composer.phar \
			install

.PHONY: run-a3-phpstan
run-a3-phpstan:
	cd adminator3 \
		&& php \
			vendor/phpstan/phpstan/phpstan.phar --version \
		&& php \
			vendor/phpstan/phpstan/phpstan.phar \
			analyse \
				app \
				boostrap \
				config \
				include/main.function.shared.php \
				public \
				resources \
				templates \
				ecs.php \
				ind*.php \
				--memory-limit 512M

.PHONY: a3-rebuild-and-run-fpm
a3-rebuild-and-run-fpm:
	git pull \
	&& docker compose \
		down \
		fpm \
	&& docker compose \
		up \
		fpm \
		--build \
		-d

.PHONY: a3-run-script-work-diff-auto
a3-run-script-work-diff-auto:
	php \
		adminator3/scripts/work_diff_auto.php

.PHONY: a3-run-script-work-diff
a3-run-script-work-diff:
	php \
		adminator3/scripts/work_diff.php

export MODE=development

export OTEL_PHP_AUTOLOAD_ENABLED=true
export OTEL_SERVICE_NAME=isp-adminator3
export OTEL_TRACES_EXPORTER=otlp # otlp OR console
export OTEL_METRICS_EXPORTER=none
export OTEL_LOGS_EXPORTER=none
# export OTEL_EXPORTER_OTLP_PROTOCOL=http  # grpc OR http/protobuf
export OTEL_EXPORTER_OTLP_ENDPOINT=http://192.168.1.213:4317 # 4317 for grpc, 4318 for http
export OTEL_RESOURCE_ATTRIBUTES=service.name=isp-adminator3-internal,application.name=isp-adminator3-internal

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
