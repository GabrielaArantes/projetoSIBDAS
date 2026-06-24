<?php

// URL base da aplicação
define('BASE_URL', '/sibdas/1241094/medstock');

// Configurações de ligação à base de dados
define('MYSQL_HOST', 'vsgate-s1.dei.isep.ipp.pt');
define('MYSQL_PORT', '10464');
define('MYSQL_DATABASE', 'db1241094');
define('MYSQL_USERNAME', '1241094');
define('MYSQL_PASSWORD', 'arantes_094');
define('MYSQL_AES_KEY', 'chave_medstock_2025');

// Informações gerais da aplicação
define('APP_NAME', 'MedStock');
define('APP_VERSION', '1.0.0');
define('APP_COPYRIGHT', '© 2025 MedStock');

// Configurações de encriptação AES para proteger IDs nos URLs
define('OPENSSL_METHOD', 'AES-256-CBC'); 
define('OPENSSL_KEY', 'H0SDRQzIGqclX2kbYBk9xspdn9U5f3Wa'); 
define('OPENSSL_IV', 'BzKAbjuREsHgnw56');