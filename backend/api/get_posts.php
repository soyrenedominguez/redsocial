<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../config/Database.php';
include_once '../models/Post.php';
include_once '../models/Comment.php';

$database = new Database();
$db = $database->connect();

$post = new Post($db);
$comment = new Comment($db);

$result = $post->getAll();
$num = $result->rowCount();

if($num > 0) {
    $posts_arr = array();
    $posts_arr['data'] = array();

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        // Get comments for this post
        $comment->id_publicacion = $id_publicacion;
        $comments_result = $comment->getByPost();
        $comments = array();
        while($comment_row = $comments_result->fetch(PDO::FETCH_ASSOC)) {
            $comments[] = $comment_row;
        }

        $post_item = array(
            'id_publicacion' => $id_publicacion,
            'contenido' => $contenido,
            'imagen' => $imagen,
            'fecha_publicacion' => $fecha_publicacion,
            'autor_nombre' => $autor_nombre,
            'autor_foto' => $autor_foto,
            'comentarios' => $comments
        );

        array_push($posts_arr['data'], $post_item);
    }

    echo json_encode($posts_arr);
} else {
    echo json_encode(array('data' => []));
}
?>
