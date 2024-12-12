<?php

require_once("./core/database.php");

class User extends Database {

    public function register($username, $email, $password, $repeatPassword, $roleID) {
        $errors = [];
    
        if (empty($username)) {
            $errors[] = "Username is required.";
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Valid email is required.";
        }
        if (empty($password) || strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }
        if ($password !== $repeatPassword) {
            $errors[] = "Passwords do not match.";
        }
    
        if (!empty($errors)) {
            return ["success" => false, "errors" => $errors];
        }
    
        try {
            $dbh = $this->connect();
    
            $stmt = $dbh->prepare("SELECT * FROM Users WHERE Username = :username OR Email = :email");
            $stmt->execute([':username' => $username, ':email' => $email]);
    
            if ($stmt->rowCount() > 0) {
                return ["success" => false, "errors" => ["Username or email already exists."]];
            }
    
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
            $stmt = $dbh->prepare("INSERT INTO Users (Username, PasswordHash, Email, RoleID) VALUES (:username, :password, :email, :roleID)");
            $stmt->execute([
                ':username' => $username,
                ':password' => $hashedPassword,
                ':email' => $email,
                ':roleID' => $roleID
            ]);
    
            return ["success" => true, "message" => "User registered successfully."];
        } catch (PDOException $e) {
            return ["success" => false, "errors" => ["Database error: " . $e->getMessage()]];
        }
    }

    public function getApplicants() {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare("SELECT * FROM Users WHERE RoleID = 1");
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching applicants: " . $e->getMessage());
            return [];
        }
    }

    public function getHRUsers() {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare("SELECT * FROM Users WHERE RoleID = 2");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching HR users: " . $e->getMessage());
            return [];
        }
    }

    public function getHRs() {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare("SELECT * FROM Users WHERE RoleID = 2");
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching HR users: " . $e->getMessage());
            return [];
        }
    }

    public function login($usernameOrEmail, $password) {
        $errors = [];

        if (empty($usernameOrEmail)) {
            $errors[] = "Username or email is required.";
        }
        if (empty($password)) {
            $errors[] = "Password is required.";
        }

        if (!empty($errors)) {
            return ["success" => false, "errors" => $errors];
        }

        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare("SELECT * FROM Users WHERE Username = :usernameOrEmail OR Email = :usernameOrEmail");
            $stmt->execute([':usernameOrEmail' => $usernameOrEmail]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['PasswordHash'])) {
                return ["success" => false, "errors" => ["Invalid credentials."]];
            }

            session_start();
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['username'] = $user['Username'];
            $_SESSION['role'] = $user['RoleID'];

            return ["success" => true, "message" => "Logged in successfully."];
        } catch (PDOException $e) {
            return ["success" => false, "errors" => ["Database error: " . $e->getMessage()]];
        }
    }
}