<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../config/Database.php';
include_once '../models/User.php';

$database = new Database();
$db = $database->connect();

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->nombre) &&
    !empty($data->email) &&
    !empty($data->password) &&
    !empty($data->fecha_nacimiento)
) {
    $user->nombre = $data->nombre;
    $user->email = $data->email;
    $user->password = $data->password;
    $user->fecha_nacimiento = $data->fecha_nacimiento;

    if($user->register()) {
        echo json_encode(array('message' => 'Usuario registrado exitosamente'));
    } else {
        echo json_encode(array('message' => 'El usuario no pudo ser registrado'));
    }
} else {
    echo json_encode(array('message' => 'Datos incompletos'));
}
?>
