<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
header('Content-Type: application/json; charset=UTF-8');

$conexion = new mysqli("localhost", "root","", "ciisa_backend_v1_eva2_A");
$conexion->set_charset('utf8mb4');

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
} else {
    echo "Conexión exitosa";
}

//Tomamos el metodo y el recurso solicitado en el url
$metodo = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos = explode('/', trim($uri, '/'));

$recurso = $segmentos[1] ?? null; 
$id = $segmentos[2] ?? null;
$estado = $segmentos[3]?? null;

//Tomamos los datos de la peticion y los guardamos como array.
$datos_json = file_get_contents("php://input");
$datos_json = mb_convert_encoding($datos_json, 'UTF-8', 'UTF-8');// Meto una conversión para los problemas con la tilde :c
$datosRecibidos = json_decode($datos_json, true);

//Diccionarios consultas;
$consultasRead= [
    "mantenimiento_info" => "SELECT id, nombre, texto, activo FROM mantenimiento_info",
    "categoria_servicio" => "SELECT id, nombre, imagen, texto, activo FROM categoria_servicio",
    "info_contacto"      => "SELECT id, nombre, texto, texto_adicional, activo FROM info_contacto",
    "imagen"             => "SELECT id, nombre, imagen, activo FROM imagen",
    "historia"           => "SELECT id, tipo, texto, activo FROM historia",
    "historia_imagen"    => "SELECT id, historia_id, imagen_id FROM historia_imagen",
    "equipo"             => "SELECT id, tipo, texto, activo FROM equipo",
    "equipo_imagen"      => "SELECT id, historia_id, imagen_id FROM equipo_imagen",
    "pregunta_frecuente" => "SELECT id, pregunta, respuesta, activo FROM pregunta_frecuente"
];



//Pruebas
echo "\nUri: " . $uri . "\n";
echo "Recurso: " . $recurso;
echo "\nID: " . $id."\n\n";
print_r($datosRecibidos);
echo "\n\n";
//crearID($datosRecibidos, $conexion, $recurso);
echo "\n\n";

switch ($metodo) {
    case 'GET':
        if ($id > 0) {
            http_response_code(200);
            consultaPorID($id, $conexion,$recurso, $consultasRead);
        } else {
            consultaGeneral($conexion,$recurso, $consultasRead);
        }
        break;
    case 'POST':
        crearID($datosRecibidos, $conexion, $recurso);
        break;
    case 'PUT':
        if ($id) {
            actualizarUsuario($id, $conexion, $datosRecibidos, $recurso);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Falta ID para actualizar"]);
        }
        break;
    case 'DELETE':
        if ($id) {
            eliminarId($id, $conexion, $recurso);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Falta ID para eliminar"]);
        }
        break;
    case 'PATCH':
        echo "\n\nPaso patch\n\n";

        break;
    default:
        http_response_code(405); // Método no permitido
        echo json_encode(["error" => "Método no permitido"]);
}


// === Funciones ===


function consultaGeneral($conexion, $recurso, $consultasRead) {
    $datos = array();
    echo "\n\n" . $consultasRead[$recurso] ."\n\n";
    $resultado = $conexion->query($consultasRead[$recurso]);//Hacemos la consulta a la bdd.

    if ($resultado->num_rows > 0) { //Para cada linea de la consulta, convertivos en array assoc y guardamos en array datos.
        // Mostrar los resultados
        http_response_code(200);
        while($fila = $resultado->fetch_assoc()){
            $datos[] = $fila;
        }
        echo json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    } else {
        echo json_encode(["error" => "Tabla no tiene datos"]);
    }
    
}

function consultaPorID($id, $conexion, $recurso, $consultasRead) {
    $datos = array();
    $resultado = $conexion->query($consultasRead[$recurso]." WHERE id=$id");//Hacemos la consulta a la bdd.

    if ($resultado->num_rows > 0){ //Si resultado tiene filas, las convertimos en  array assoc y agregamos a datos para devolver en json.
        http_response_code(200);
        while($fila = $resultado->fetch_assoc()){
            $datos[] = $fila;
        }
        echo json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    
    } else {
        http_response_code(404);
        echo json_encode(["error" => "ID no encontrado"]);
    }
}

function crearID($datosRecibidos, $conexion, $recurso) { 


    //todas las variables incluidas en las consultas, excepto activo, pq siempre es true
    $id = $datosRecibidos['id']??null; 
    $nombre = $datosRecibidos['nombre']??null;
    $texto = $datosRecibidos['texto']??null;
    $texto_adicional = $datosRecibidos['texto_adicional']??null;
    $imagen = $datosRecibidos['imagen']??null;
    $tipo = $datosRecibidos['tipo']??null;
    $historia_id = $datosRecibidos['historia_id']??null;
    $equipo_id = $datosRecibidos['equipo_id']??null;
    $imagen_id = $datosRecibidos['imagen_id']??null;
    $pregunta = $datosRecibidos['pregunta']??null;
    $respuesta = $datosRecibidos['respuesta']??null;

    $consultasCreate = [
    "mantenimiento_info" => "INSERT INTO mantenimiento_info (id, nombre, texto, activo) VALUES ($id, '$nombre', '$texto', true);",
    "categoria_servicio" => "INSERT INTO categoria_servicio (id, nombre, imagen, texto, activo) VALUES ($id, '$nombre', '$imagen', '$texto', true);",
    "info_contacto"      => "INSERT INTO info_contacto (id, nombre, texto, texto_adicional, activo) VALUES ($id, '$nombre', '$texto', '$texto_adicional', true);",
    "imagen"             => "INSERT INTO imagen (id, nombre, imagen, activo) VALUES ($id, '$nombre', '$imagen', true);",
    "historia"           => "INSERT INTO historia (id, tipo, texto, activo) VALUES ($id, '$tipo', '$texto', true);",
    "historia_imagen"    => "INSERT INTO historia_imagen (id, historia_id, imagen_id) VALUES ($id, $historia_id, $imagen_id);",
    "equipo"             => "INSERT INTO equipo (id, tipo, texto, activo) VALUES ($id, '$tipo', '$texto', true);",
    "equipo_imagen"      => "INSERT INTO equipo_imagen (id, equipo_id, imagen_id) VALUES ($id, $equipo_id, $imagen_id);",
    "pregunta_frecuente" => "INSERT INTO pregunta_frecuente (id, pregunta, respuesta, activo) VALUES ($id, '$pregunta', '$respuesta', true);"
    ];
    
    //echo "\n\n".$consultasCreate[$recurso]."\n\n";
    $resultado = $conexion->query($consultasCreate[$recurso]);
    echo json_encode(["mensaje" => "ID creado"]);
}

function actualizarUsuario($id, $conexion, $datosRecibidos, $recurso) {
    //todas las variables incluidas en las consultas, excepto activo, pq siempre es true
    $id = $id;
    $nombre = $datosRecibidos['nombre']??null;
    $texto = $datosRecibidos['texto']??null;
    $texto_adicional = $datosRecibidos['texto_adicional']??null;
    $imagen = $datosRecibidos['imagen']??null;
    $tipo = $datosRecibidos['tipo']??null;
    $historia_id = $datosRecibidos['historia_id']??null;
    $equipo_id = $datosRecibidos['equipo_id']??null;
    $imagen_id = $datosRecibidos['imagen_id']??null;
    $pregunta = $datosRecibidos['pregunta']??null;
    $respuesta = $datosRecibidos['respuesta']??null;

    $consultasUpdate = [
        "mantenimiento_info" => "UPDATE mantenimiento_info SET nombre = '$nombre', texto = '$texto' WHERE id = $id;",
        "categoria_servicio" => "UPDATE categoria_servicio SET nombre = '$nombre', imagen = '$imagen', texto = '$texto' WHERE id = $id;",
        "info_contacto"      => "UPDATE info_contacto SET nombre = '$nombre', texto = '$texto', texto_adicional = '$texto_adicional' WHERE id = $id;",
        "imagen"             => "UPDATE imagen SET nombre = '$nombre', imagen = '$imagen' WHERE id = $id;",
        "historia"           => "UPDATE historia SET tipo = '$tipo', texto = '$texto' WHERE id = $id;",
        "historia_imagen"    => "UPDATE historia_imagen SET historia_id = '$historia_id', imagen_id = '$imagen_id' WHERE id = $id;",
        "equipo"             => "UPDATE equipo SET tipo = '$tipo', texto = '$texto' WHERE id = $id;",
        "equip_image"        => "UPDATE equipo_imagen SET equipo_id = '$equipo_id', imagen_id = '$imagen_id' WHERE id = $id;",
        "pregunta_frecuente" => "UPDATE pregunta_frecuente SET pregunta = '$pregunta', respuesta = '$respuesta' WHERE id = $id;"
    ]; 
    try{
        $resultado = $conexion->query($consultasUpdate[$recurso]);
        if ($conexion->affected_rows === 0){ //Revisamos cuantas filas afectó la consulta, para verificar si se hizo o no
            echo json_encode(["error" => "ID $id no exite o no se entregaron nuevos datos"]);
            return;
        } else {
            http_response_code(200);
            echo json_encode(["mensaje" => "ID $id actualizado"]);
            }
    } catch(mysqli_sql_exception $e){
        echo "Ocurrió un error en la base de datos: " . $e->getMessage();
    }
}

function eliminarId($id, $conexion, $recurso) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //Para que catch pueda tomar los errores sql
    try{
        $resultado = $conexion->prepare('DELETE FROM '.$recurso.' WHERE id = ?'); //Generamos la query con el recurso (tabla) y el id
        $resultado->bind_param('i', $id);
        $resultado->execute();//Ejecutamos la query luego de filtrar un poco
        if($resultado->affected_rows === 0){ // Si no afecta a ninguna fila, nos informa que no estaba el id
            http_response_code(404);
            echo json_encode(["error" => "No se encontro el id $id"]);
            return;
        }
        http_response_code(200);
        echo json_encode(["Realizado" => "ID ".$id." eliminado"]);
        } catch (mysqli_sql_exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar id: " . $e->getMessage()]);
        }    
}


?>
