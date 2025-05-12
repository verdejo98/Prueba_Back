<?php
header('Content-Type: application/json');

$conexion = new mysqli("localhost", "root","", "ciisa_backend_v1_eva2_A");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
} else {
    echo "Conexión exitosa";
}
?>

UPDATE mantenimiento_info SET nombre = 'Nuevo nombre', texto = 'Nuevo texto' WHERE id = 0;
