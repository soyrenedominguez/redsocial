<?php
class User {
    private $conn;
    private $table = 'usuarios';

    public $id_usuario;
    public $nombre;
    public $email;
    public $password;
    public $fecha_nacimiento;
    public $biografia;
    public $foto_perfil;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Registrar usuario
    public function register() {
        $query = 'INSERT INTO ' . $this->table . ' 
                  SET nombre = :nombre, email = :email, password_hash = :password, fecha_nacimiento = :fecha_nacimiento';

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->fecha_nacimiento = htmlspecialchars(strip_tags($this->fecha_nacimiento));

        // Hash password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        // Bind params
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':fecha_nacimiento', $this->fecha_nacimiento);

        if($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Login usuario
    public function login() {
        $query = 'SELECT id_usuario, nombre, email, password_hash, foto_perfil, biografia FROM ' . $this->table . ' WHERE email = :email LIMIT 1';
        
        $stmt = $this->conn->prepare($query);
        
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        $stmt->bindParam(':email', $this->email);
        
        $stmt->execute();
        
        return $stmt;
    }

    // Obtener usuario por ID
    public function getById() {
        $query = 'SELECT id_usuario, nombre, email, fecha_nacimiento, biografia, foto_perfil, fecha_registro FROM ' . $this->table . ' WHERE id_usuario = ? LIMIT 1';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_usuario);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->nombre = $row['nombre'];
            $this->email = $row['email'];
            $this->fecha_nacimiento = $row['fecha_nacimiento'];
            $this->biografia = $row['biografia'];
            $this->foto_perfil = $row['foto_perfil'];
            return true;
        }
        return false;
    }

    // Actualizar perfil
    public function update() {
        $query = 'UPDATE ' . $this->table . '
                  SET nombre = :nombre, biografia = :biografia';
        
        if($this->foto_perfil) {
            $query .= ', foto_perfil = :foto_perfil';
        }
        
        $query .= ' WHERE id_usuario = :id_usuario';
        
        $stmt = $this->conn->prepare($query);
        
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->biografia = htmlspecialchars(strip_tags($this->biografia));
        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));
        
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':biografia', $this->biografia);
        $stmt->bindParam(':id_usuario', $this->id_usuario);
        
        if($this->foto_perfil) {
            $this->foto_perfil = htmlspecialchars(strip_tags($this->foto_perfil));
            $stmt->bindParam(':foto_perfil', $this->foto_perfil);
        }
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}
?>
