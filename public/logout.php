<?php
require_once __DIR__ . '/../private/includes/funcoes.php';

session_start();

session_unset();

session_destroy();

header('Location: login.php');
return;