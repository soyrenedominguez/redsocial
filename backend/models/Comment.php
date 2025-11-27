<?php
class Comment {
    private $conn;
    private $table = 'comentarios';

    public $id_comentario;
    public $id_publicacion;
    public $id_usuario;
    public $contenido;
    public $fecha_comentario;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' SET id_publicacion = :id_publicacion, id_usuario = :id_usuario, contenido = :contenido';

        $stmt = $this->conn->prepare($query);

        $this->contenido = htmlspecialchars(strip_tags($this->contenido));
        $this->id_publicacion = htmlspecialchars(strip_tags($this->id_publicacion));
        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));

        $stmt->bindParam(':contenido', $this->contenido);
        $stmt->bindParam(':id_publicacion', $this->id_publicacion);
        $stmt->bindParam(':id_usuario', $this->id_usuario);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getByPost() {
        $query = 'SELECT c.*, u.nombre as autor_nombre, u.foto_perfil as autor_foto 
                  FROM ' . $this->table . ' c
                  JOIN usuarios u ON c.id_usuario = u.id_usuario
                  WHERE c.id_publicacion = ?
                  ORDER BY c.fecha_comentario ASC';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_publicacion);
        $stmt->execute();
        return $stmt;
    }
}
?>
