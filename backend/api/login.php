<?php
session_start();
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

if(!empty($data->email) && !empty($data->password)) {
    $user->email = $data->email;
    $stmt = $user->login();
    $num = $stmt->rowCount();

    if($num > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);

        if(password_verify($data->password, $password_hash)) {
            $_SESSION['user_id'] = $id_usuario;
            $_SESSION['user_name'] = $nombre;
            
            echo json_encode(array(
                'status' => 'success',
                'message' => 'Login exitoso',
                'data' => array(
                    'id_usuario' => $id_usuario,
                    'nombre' => $nombre,
                    'email' => $email,
                    'foto_perfil' => $foto_perfil
                )
            ));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Contraseña incorrecta'));
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Email no encontrado'));
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Datos incompletos'));
}
?>
