<?php

namespace User;

class UserDTO
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getAll()
    {
        $sql = 'SELECT * FROM utenti';
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getUserByID(int $id)
    {
        $sql = 'SELECT * FROM utenti WHERE id = :id';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function saveUser(array $user) {
        //Controlla che non ci sia uno username già esistente, nel caso blocca la funzione
        $checkSql = "SELECT COUNT(*) AS count FROM utenti WHERE username = :username";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->execute(['username' => $user['username']]);
        $result = $checkStmt->fetch(\PDO::FETCH_ASSOC);
        if ($result['count'] > 0) {
            return false;
        }
        $insertSql = "INSERT INTO utenti (username, password, admin_status) VALUES (:username, :password, :admin_status)";
        $insertStmt = $this->conn->prepare($insertSql);
        return $insertStmt->execute(['username' => $user['username'], 'password' => $user['password'], 'admin_status' => $user['admin_status']]);
    }
    

    public function updateUser(array $user)
    {
        $sql = "UPDATE utenti SET username = :username, password = :password, admin_status = :admin_status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['username' => $user['username'], 'password' => $user['password'], 'admin_status' => $user['admin_status'], 'id' => $user['id']]);
    }

    public function deleteUser(int $id)
    {
        $sql = "DELETE FROM utenti WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }

    public function isAdmin($username)
    {
        $sql = "SELECT admin_status FROM utenti WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$username]);
        $result = $stmt->fetchColumn();
        return $result && $result == 1;
    }

    public function getUserByUsername(string $username)
{
    $sql = "SELECT * FROM utenti WHERE username = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$username]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}
}
