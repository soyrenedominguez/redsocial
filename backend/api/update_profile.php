<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');

include_once '../config/Database.php';
include_once '../models/User.php';

if(!isset($_SESSION['user_id'])) {
    echo json_encode(array('message' => 'No autorizado'));
    exit;
}

$database = new Database();
$db = $database->connect();

$user = new User($db);

$user->id_usuario = $_SESSION['user_id'];
$user->nombre = $_POST['nombre'];
$user->biografia = $_POST['biografia'];

// Handle file upload
if(isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "../../uploads/";
    $file_extension = pathinfo($_FILES["foto_perfil"]["name"], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["foto_perfil"]["tmp_name"]);
    if($check !== false) {
        if (move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $target_file)) {
            $user->foto_perfil = 'uploads/' . $new_filename;
        } else {
            echo json_encode(array('message' => 'Error al subir la imagen'));
            exit;
        }
    } else {
        echo json_encode(array('message' => 'El archivo no es una imagen'));
        exit;
    }
}

if($user->update()) {
    // Update session data
    $_SESSION['user_name'] = $user->nombre;
    
    echo json_encode(array('message' => 'Perfil actualizado'));
} else {
    echo json_encode(array('message' => 'Error al actualizar perfil'));
}
?>
