<?php
$conexion = new mysqli("localhost", "root", "", "perros_db");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
} else {
    echo "Conexión exitosa";
};


$consulta = "SELECT * FROM razas";
$resultado = $conexion->query($consulta);
while ($raza = $resultado->fetch_assoc()){
    echo "<p>" . "Nombre: " . $raza["nombre"];
    echo "<p>" . "Descripción: " . $raza["descripcion"];
    echo "<p>" . "Peso: " . $raza["peso_kg"]. " kg";
    echo "<p>" . "Tamaño: " . $raza["estatura_cm"] . " cm";
    echo "<p>" . "Son ideales para: " .$raza["ambiente_ideal"];
    echo "<p>-------------------------------------------------";

}
?>
