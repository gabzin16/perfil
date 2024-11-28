<?php
// Inclui o arquivo de configuração
require_once 'config.php';

// Encerra a sessão
session_start();
session_destroy();

// Remove todos os cookies da sessão
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time()-3600, '/');
    }
}

// Redireciona para a página de login
header("Location: login.php");
exit;
?>