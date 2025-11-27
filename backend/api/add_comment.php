<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../config/Database.php';
include_once '../models/Comment.php';

if(!isset($_SESSION['user_id'])) {
    echo json_encode(array('message' => 'No autorizado'));
    exit;
}

$database = new Database();
$db = $database->connect();

$comment = new Comment($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id_publicacion) && !empty($data->contenido)) {
    $comment->id_usuario = $_SESSION['user_id'];
    $comment->id_publicacion = $data->id_publicacion;
    $comment->contenido = $data->contenido;

    if($comment->create()) {
        echo json_encode(array('message' => 'Comentario agregado'));
    } else {
        echo json_encode(array('message' => 'Error al agregar comentario'));
    }
} else {
    echo json_encode(array('message' => 'Datos incompletos'));
}
?>
