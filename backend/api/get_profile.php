<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

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

if($user->getById()) {
    echo json_encode(array(
        'id_usuario' => $user->id_usuario,
        'nombre' => $user->nombre,
        'email' => $user->email,
        'biografia' => $user->biografia,
        'foto_perfil' => $user->foto_perfil,
        'fecha_nacimiento' => $user->fecha_nacimiento
    ));
} else {
    echo json_encode(array('message' => 'Usuario no encontrado'));
}
?>
