<?php

use Psr\Container\ContainerInterface;

class vlastnik2 {
	var $conn_mysql;

	var $logger;

	function __construct(ContainerInterface $container) {
		$this->conn_mysql = $container->connMysql;
		$this->logger = $container->logger;
	}

} //konec tridy vlastnik2
