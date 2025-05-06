<?php
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $nome;
    public $email;
    public $ra;
    public $senha;
    public $celular;

    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nome=:nome, email=:email, ra=:ra, senha=:senha, celular=:celular";

        $stmt = $this->conn->prepare($query);

        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->ra = htmlspecialchars(strip_tags($this->ra));
        $this->senha = password_hash($this->senha, PASSWORD_BCRYPT);
        $this->celular = htmlspecialchars(strip_tags($this->celular));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":ra", $this->ra);
        $stmt->bindParam(":senha", $this->senha);
        $stmt->bindParam(":celular", $this->celular);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    function read() {
        $query = "SELECT id, nome, email, ra, senha, celular FROM " . $this->table_name . " ORDER BY nome ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function readOne() {
        $query = "SELECT id, nome, email, ra, celular FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->nome = $row['nome'];
        $this->email = $row['email'];
        $this->ra = $row['ra'];
        $this->celular = $row['celular'];
    }

    function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET nome = :nome, email = :email, ra = :ra, celular = :celular 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->ra = htmlspecialchars(strip_tags($this->ra));
        $this->celular = htmlspecialchars(strip_tags($this->celular));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':ra', $this->ra);
        $stmt->bindParam(':celular', $this->celular);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>