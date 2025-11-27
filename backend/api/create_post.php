<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../config/Database.php';
include_once '../models/Post.php';

if(!isset($_SESSION['user_id'])) {
    echo json_encode(array('message' => 'No autorizado'));
    exit;
}

$database = new Database();
$db = $database->connect();

$post = new Post($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->contenido)) {
    $post->id_usuario = $_SESSION['user_id'];
    $post->contenido = $data->contenido;
    
    // TODO: Handle image upload if implemented in frontend
    // $post->imagen = ...

    if($post->create()) {
        echo json_encode(array('message' => 'Publicación creada'));
    } else {
        echo json_encode(array('message' => 'Error al crear publicación'));
    }
} else {
    echo json_encode(array('message' => 'Contenido vacío'));
}
?>
