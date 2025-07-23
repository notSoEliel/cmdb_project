<?php

// Define la URL base de tu aplicación.
// --- USAR ESTA OPCIÓN EN DESARROLLO ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script_path = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
define('BASE_URL', "{$protocol}://{$host}{$script_path}/");

// --- USAR ESTA OPCIÓN EN CLASE o DESPLIEGUE ---
// Reemplaza 'TU_IP_LOCAL' con la dirección IP de tu computadora en la red.
// define('TU_IP_LOCAL', 'doxeado');
// define('BASE_URL', 'http://'.TU_IP_LOCAL.'/phputp/cmdb_project/public/');

// En Mac: "Preferencias del Sistema" -> "Red" y buscar dirección IP (ej: 192.168.1.15).

// En Windows: "Símbolo del sistema" (cmd), escribir ipconfig y buscar la "Dirección IPv4".