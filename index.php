<?php
header('Content-Type: application/json');

$socket1 = null;
$socket2 = "/data/data/com.termux/files/usr/var/run/mysqld.sock";
$conexion = new mysqli("localhost", "root","", "ciisa_backend_v1_eva2_A", 8080, $socket2);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
} else {
    echo "Conexión exitosa";
}

//Tomamos el metodo y la ruta solicitada en el url

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos = explode('/', trim($uri, '/'));

$recurso = $segmentos[0] ?? null; 
$id = $segmentos[1] ?? null;  

switch ($recurso) {
    case 'usuarios':
        switch ($method) {
            case 'GET':
                if ($id) {
                    getUsuarioPorId($id);
                } else {
                    getUsuarios();
                }
                break;
            case 'POST':
                crearUsuario();
                break;
            case 'PUT':
                if ($id) {
                    actualizarUsuario($id);
                } else {
                    http_response_code(400);
                    echo json_encode(["error" => "Falta ID para actualizar"]);
                }
                break;
            case 'DELETE':
                if ($id) {
                    eliminarUsuario($id);
                } else {
                    http_response_code(400);
                    echo json_encode(["error" => "Falta ID para eliminar"]);
                }
                break;
            default:
                http_response_code(405); // Método no permitido
                echo json_encode(["error" => "Método no permitido"]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Recurso no encontrado"]);
}

// === Funciones simuladas ===

function getUsuarios() {
    http_response_code(200);
    echo json_encode(["usuarios" => [["id" => 1, "nombre" => "Juan"], ["id" => 2, "nombre" => "Ana"]]]);
}

function getUsuarioPorId($id) {
    if ($id == 1) {
        http_response_code(200);
        echo json_encode(["id" => 1, "nombre" => "Juan"]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Usuario no encontrado"]);
    }
}

function crearUsuario() {
    http_response_code(201);
    echo json_encode(["mensaje" => "Usuario creado"]);
}

function actualizarUsuario($id) {
    http_response_code(200);
    echo json_encode(["mensaje" => "Usuario $id actualizado"]);
}

function eliminarUsuario($id) {
    http_response_code(204); // Sin contenido
}


?>
