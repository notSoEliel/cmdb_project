<?php
// Carga los archivos necesarios
require_once '../vendor/autoload.php';
require_once '../config/database.php';

use App\Core\Database;

// --- DATOS DEL NUEVO ADMINISTRADOR ---
$nombre = 'Admin';
$email = 'admin@cmdb.com';
$passwordPlano = 'clavesegura123'; // Elige una contraseña segura

// 1. Hashear la contraseña
$passwordHash = password_hash($passwordPlano, PASSWORD_DEFAULT);

echo "<h1>Creando Administrador...</h1>";

try {
    // 2. Insertar el nuevo usuario en la base de datos
    $db = Database::getInstance();
    $sql = "INSERT INTO usuarios (nombre, email, password_hash, activo) VALUES (:nombre, :email, :password_hash, 1)";
    $db->query($sql, [
        'nombre' => $nombre,
        'email' => $email,
        'password_hash' => $passwordHash
    ]);

    echo "<p style='color:green;'>¡Administrador creado con éxito!</p>";
    echo "<p><strong>Email:</strong> {$email}</p>";
    echo "<p><strong>Contraseña:</strong> {$passwordPlano}</p>";

} catch (Exception $e) {
    // Maneja el caso de que el email ya exista
    if (str_contains($e->getMessage(), 'Duplicate entry')) {
        echo "<p style='color:orange;'>El administrador con el email '{$email}' ya existe.</p>";
    } else {
        echo "<p style='color:red;'>Error al crear el administrador: " . $e->getMessage() . "</p>";
    }
}