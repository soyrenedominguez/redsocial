<?php
class Post {
    private $conn;
    private $table = 'publicaciones';

    public $id_publicacion;
    public $id_usuario;
    public $contenido;
    public $imagen;
    public $fecha_publicacion;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' SET id_usuario = :id_usuario, contenido = :contenido';
        
        if($this->imagen) {
            $query .= ', imagen = :imagen';
        }

        $stmt = $this->conn->prepare($query);

        $this->contenido = htmlspecialchars(strip_tags($this->contenido));
        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));

        $stmt->bindParam(':contenido', $this->contenido);
        $stmt->bindParam(':id_usuario', $this->id_usuario);

        if($this->imagen) {
            $this->imagen = htmlspecialchars(strip_tags($this->imagen));
            $stmt->bindParam(':imagen', $this->imagen);
        }

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getAll() {
        $query = 'SELECT p.*, u.nombre as autor_nombre, u.foto_perfil as autor_foto 
                  FROM ' . $this->table . ' p
                  JOIN usuarios u ON p.id_usuario = u.id_usuario
                  ORDER BY p.fecha_publicacion DESC';
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function getByUser() {
        $query = 'SELECT p.*, u.nombre as autor_nombre, u.foto_perfil as autor_foto 
                  FROM ' . $this->table . ' p
                  JOIN usuarios u ON p.id_usuario = u.id_usuario
                  WHERE p.id_usuario = ?
                  ORDER BY p.fecha_publicacion DESC';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_usuario);
        $stmt->execute();
        return $stmt;
    }
}
?>
