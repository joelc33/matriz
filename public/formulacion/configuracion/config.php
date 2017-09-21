<?php
require_once (__DIR__.'/../../../vendor/autoload.php');
Dotenv::load(__DIR__.'/../../../');

define( 'USUARIO', $_ENV['DB_USERNAME'] );
define( 'CLAVE', $_ENV['DB_PASSWORD'] );
define( 'BASEDEDATOS', $_ENV['DB_DATABASE'] );
define( 'SERVIDOR', $_ENV['DB_HOST'] );
define( 'PUERTO', $_ENV['DB_PORT'] );
define( 'GESTOR_DATABASE', 'postgres' );
define( 'DRIVER', 'org.postgresql.Driver' );
define( 'JDBC_TYPE', 'postgresql' );
define( 'JAVA_BRIDGE', 'http://localhost:8080/JavaBridge621' );
define( 'RAIZ_WEB', '/formulacion' );
