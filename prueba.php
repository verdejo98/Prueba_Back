<?php
header('Content-Type: application/json');
$puerto = 8000;
$conexion = new mysqli("localhost", "root","", "ciisa_backend_v1_eva2_A");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
} else {
    echo "Conexión exitosa";
}
$consulta_sql = "SELECT * FROM mantenimiento_info";
$resultado = $conexion->query($consulta_sql);

$datos = array();

if ($resultado->num_rows > 0) {
    // Mostrar los resultados
    while($fila = $resultado->fetch_assoc()){
        $datos[] = $fila;
    }
    echo json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}

echo  "\n\n\n\nContenidos de variables:\n";
echo "\n\nResultado de una consulta a una conexion: \n\n";
print_r($conexion->query("SELECT * FROM mantenimiento_info WHERE id=1")) . "\n\n"; 

echo "\nResultado de un fetch_assoc(): \n\n";
print_r($conexion->query("SELECT * FROM mantenimiento_info WHERE id=1")->fetch_assoc()); 

echo "\nResultado de json_encode a un array asociativo (lo que suelta fetch_assoc): \n\n";
print_r(json_encode($conexion->query("SELECT * FROM mantenimiento_info WHERE id=1")->fetch_assoc(),JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . "\n\n"; 

?>

/* Es la conexión que utilizo en termux con mariadb.
<?php
header('Content-Type: application/json');
$puerto = 3366;
$socket = "/data/data/com.termux/files/usr/var/run/mysqld.sock";
$conexion = new mysqli("localhost", "root","", "ciisa_backend_v1_eva2_A", $puerto1 ?? null, $socket1 ?? null);
?>

/*
Notas:

-Usaremos el metodo PATCH para activar y desactivar
-Para completar las consultar en cada funcion usaremos una mezcla de prepare(), bind_param() y execute().
    Con estos podemos:
    - prepare(): Preparar una consulta con parametos por rellenar. Ej: $Consulta = prepare(Select nombre=? WHERE id=?).
    - bind_param(): Con esto podemos completar lo anterior indicando el tipo de dato que va en cada
    parameto respectivamente. Ej: $Consulta->bind_param("si", nombre, id).
    - execute(): Con este entregamos la consulta final, con los datos ya reemplazados.
    Ej: $Consutla->execute();


Ejemplo de los datos que entrega mantenimiento_info:

[
    {
        "id": "1",
        "nombre": "Mantenimiento",
        "texto": "Conjunto de operaciones y cuidados necesarios para que tus instalaciones continúen funcionando correctamente.",
        "activo": "1"
    },
    {
        "id": "2",
        "nombre": "Mantenimiento Preventivo",
        "texto": "Ayuda a prolongar la vida útil de tus activos y aumenta la productividad, a través de una revisión.",
        "activo": "1"
    },
    {
        "id": "3",
        "nombre": "Mantenimiento Correctivo",
        "texto": "Corrige problemas o daños en las instalaciones o equipos.",
        "activo": "1"
    }
]

Consultas curl:
create
curl -X POST http://localhost/PruebaBack/mantenimiento_info -H "Content-Type: application/json; charset=UTF-8" -d '{"id": "4","nombre": "MantenimientoGatitos","texto": "Gatitos bonitos","activo": "1"}'
curl -X POST http://localhost/PruebaBack/equipo             -H "Content-Type: application/json; charset=UTF-8" -d '{"id": 20,"tipo": "Tecnico","texto": "Responsable del area de mantenimiento","activo": "1"}'
curl -X POST http://localhost/PruebaBack/equipo             -H "Content-Type: application/json; charset=UTF-8" -d '{"id": 20,"tipo": "Técnico","texto": "Responsable del area de mantenimiento","activo": "1"}'
curl -X POST http://localhost/PruebaBack/equipo/4 -H "Content-Type: application/json" -d '{"tipo": "MantenimientoConejos", "texto": "Conejos bonitos"}'



curl -X PUT http://localhost/PruebaBack/mantenimiento_info/4 -H "Content-Type: application/json" -d '{"nombre": "MantenimientoConejos", "texto": "Conejos bonitos"}'
curl -X PUT http://localhost/PruebaBack/mantenimiento_info/4 -H "Content-Type: application/json" -d '{"nombre": "MantenimientoConejos", "texto": "Conejos bonitos"}'
curl -X PUT http://localhost/PruebaBack/equipo/20 -H "Content-Type: application/json" -d '{"tipo": "MantenimientoConejos", "texto": "Conejos bonitos"}'

UPDATE mantenimiento_info SET nombre = 'MantenimientoConejos', texto = 'Conejos bonitos' WHERE id = 4


Diccionarios:

--READ
$consultas = [
    "mantenimiento_info" => "SELECT id, nombre, texto, activo FROM mantenimiento_info;",
    "categoria_servicio" => "SELECT id, nombre, imagen, texto, activo FROM categoria_servicio;",
    "info_contacto"      => "SELECT id, nombre, texto, texto_adicional, activo FROM info_contacto;",
    "imagen"             => "SELECT id, nombre, imagen, activo FROM imagen;",
    "historia"           => "SELECT id, tipo, texto, activo FROM historia;",
    "historia_imagen"    => "SELECT id, historia_id, imagen_id FROM historia_imagen;",
    "equipo"             => "SELECT id, tipo, texto, activo FROM equipo;",
    "equipo_imagen"      => "SELECT id, historia_id, imagen_id FROM equipo_imagen;",
    "pregunta_frecuente" => "SELECT id, pregunta, respuesta, activo FROM pregunta_frecuente;"
    ];

--CREATE
$consultas = [
    "mantenimiento_info" => "INSERT INTO mantenimiento_info (id, nombre, texto, activo) VALUES (0, 'NOMBRE', 'TEXTO', true);",
    "categoria_servicio" => "INSERT INTO categoria_servicio (id, nombre, imagen, texto, activo) VALUES (0, 'NOMBRE', 'URL IMAGEN', 'TEXTO', true);",
    "info_contacto"      => "INSERT INTO info_contacto (id, nombre, texto, texto_adicional, activo) VALUES (0, 'NOMBRE', 'TEXTO', 'TEXTO ADICIONAL', true);",
    "imagen"             => "INSERT INTO imagen (id, nombre, imagen, activo) VALUES (0, 'NOMBRE', 'URL IMAGEN', true);",
    "historia"           => "INSERT INTO historia (id, tipo, texto, activo) VALUES (0, 'TIPO', 'TEXTO', true);",
    "historia_imagen"    => "INSERT INTO historia_imagen (id, historia_id, imagen_id) VALUES (0, 0, 0);",
    "equipo"             => "INSERT INTO equipo (id, tipo, texto, activo) VALUES (0, 'TIPO', 'TEXTO', true);",
    "equipo_imagen"      => "INSERT INTO equipo_imagen (id, equipo_id, imagen_id) VALUES (0, 0, 0);",
    "pregunta_frecuente" => "INSERT INTO pregunta_frecuente (id, pregunta, respuesta, activo) VALUES (0, 'PREGUNTA', 'RESPUESTA', true);"
];
--UPDATE
$consultasUpdate = [
    "mantenimiento_info" => "UPDATE mantenimiento_info SET nombre = 'Nuevo nombre', texto = 'Nuevo texto' WHERE id = 0;",
    "categoria_servicio" => "UPDATE categoria_servicio SET nombre = 'Nuevo nombre', imagen = 'Nueva url imagen', texto = 'Nuevo texto' WHERE id = 0;",
    "info_contacto"      => "UPDATE info_contacto SET nombre = 'Nuevo nombre', texto = 'Nuevo texto', texto_adicional = 'Nuevo texto adicional' WHERE id = 0;",
    "imagen"             => "UPDATE imagen SET nombre = 'Nuevo nombre', imagen = 'Nueva url imagen' WHERE id = 0;",
    "historia"           => "UPDATE historia SET tipo = 'Nuevo tipo', texto = 'Nuevo texto' WHERE id = 0;",
    "historia_imagen"    => "UPDATE historia_imagen SET historia_id = 'Nuevo id', imagen_id = 'Nuevo id' WHERE id = 0;",
    "equipo"             => "UPDATE equipo SET tipo = 'Nuevo tipo', texto = 'Nuevo texto' WHERE id = 0;",
    "equip_image   => "UPDATE equipo_imagen SET historia_id = 'Nuevo id', imagen_id = 'Nuevo id' WHERE id = 0;",
    "pregunta_frecuente" => "UPDATE pregunta_frecuente SET pregunta = 'Nueva pregunta', respuesta = 'Nueva respuesta' WHERE id = 0;"
]; 

--DESACTIVATE/ACTIVAT$consultasActivesactivar = [
    "mantenimiento_info"  => "UPDATE mantenimiento_info SET activo = false WHERE id = 0;",
    "categoria_servicio" => "UPDATE categoria_servicio SET activo = false WHERE id = 0;",
    "info_contacto"      => "UPDATE info_contacto SET activo = false WHERE id = 0;",
    "imagen"             => "UPDATE imagen SET activo = false WHERE id = 0;",
    "historia"           => "UPDATE historia SET activo = false WHERE id = 0;",
    "equipo"             => "UPDATE equipo SET activo = false WHERE id = 0;",
    "pregunta_frecuente" => "UPDATE pregunta_frecuente SET activo = false WHERE id = 0;"
];

--DELETE
$consultasDelete = [
    "mantenimiento_info" => "DELETE FROM mantenimiento_info WHERE id = 0;",
    "categoria_servicio" => "DELETE FROM categoria_servicio WHERE id = 0;",
    "info_contacto"      => "DELETE FROM info_contacto WHERE id = 0;",
    "imagen"             => "DELETE FROM imagen WHERE id = 0;",
    "historia"           => "DELETE FROM historia WHERE id = 0;",
    "historia_imagen"    => "DELETE FROM historia_imagen WHERE id = 0;",
    "equipo"             => "DELETE FROM equipo WHERE id = 0;",
    "equipo_imagen"      => "DELETE FROM equipo_imagen WHERE id = 0;",
    "pregunta_frecuente" => "DELETE FROM pregunta_frecuente WHERE id = 0;"
];

Json's para probar los create:

{
  "mantenimiento_info": {
    "id": 1,
    "nombre": "Mantenimiento General",
    "texto": "Revisión de sistemas eléctricos",
    "activo": true
  },
  "categoria_servicio": {
    "id": 1,
    "nombre": "Plomería",
    "imagen": "https://ejemplo.com/imagenes/plomeria.jpg",
    "texto": "Servicios de plomería doméstica",
    "activo": true
  },
  "info_contacto": {
    "id": 1,
    "nombre": "Oficina Central",
    "texto": "Lunes a viernes, 9am - 6pm",
    "texto_adicional": "Tel: 123456789",
    "activo": true
  },
  "imagen": {
    "id": 1,
    "nombre": "LogoEmpresa",
    "imagen": "https://ejemplo.com/imagenes/logo.png",
    "activo": true
  },
  "historia": {
    "id": 1,
    "tipo": "Inicio",
    "texto": "Nuestra empresa comenzó en 1990",
    "activo": true
  },
  "historia_imagen": {
    "id": 1,
    "historia_id": 1,
    "imagen_id": 1
  },
  "equipo": {
    "id": 1,
    "tipo": "Técnico",
    "texto": "Responsable del área de mantenimiento",
    "activo": true
  },
  "equipo_imagen": {
    "id": 1,
    "equipo_id": 1,
    "imagen_id": 1
  },
  "pregunta_frecuente": {
    "id": 1,
    "pregunta": "¿Cuánto tarda un servicio?",
    "respuesta": "Depende del tipo de trabajo, usualmente de 1 a 2 horas.",
    "activo": true
  }
}

Posiblemente util:
switch($recurso){
        case 'mantenimiento_info':
        $resultado = $conexion->query($consultasCreate[$recurso]);//Hacemos la consulta a la bdd.
        http_response_code(201);
        case 'categoria_servicio':
            break;
        case 'info_contacto':
            break;
        case 'imagen':
            break;
        case 'historia':
            break;
        case 'historia_imagen':
            break;    
        case 'equipo':
            break;    
        case 'equipo_imagen':
            break;
        case 'pregunta_frecuente':
            break;
    }
    */

*/