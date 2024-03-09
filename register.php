<?php
require_once 'config.php';
require_once 'classes/database.php';
require_once 'classes/user.php';

use db\DB_PDO;
use User\UserDTO;

// Istanza del database
$db = DB_PDO::getInstance($config);

// Connetti al database
$pdo = $db->getConnection();

// Controlla che l'utente abbia inserito i dati correttamente
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
  $userData = [
    'username' => $_POST['username'],
    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
    'admin_status' => ($_POST['admin_status'] === 'admin_yes') ? 1 : 0
  ];

  // Nuovo oggetto User
  $userDTO = new UserDTO($pdo);

  // Salva l'utente
  $result = $userDTO->saveUser($userData);

  if ($result) {
    header('Location: http://localhost/login.php');
    exit();
  } else if ($result === false) {
    echo "Username already exists. Please choose a different one.";
  } else {
    echo "Error: Registration failed.";
  }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Navbar w/ text</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="index.php">Home</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container">
    <h1 class="text-center">Register</h1>
    <!-- Form di registrazione -->
    <form method="post">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" aria-describedby="emailHelp">
        <div id="emailHelp" class="form-text">We'll never share your data with anyone else.</div>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password">
      </div>
      <div class="mb-3">
        <label for="language" class="form-label">Register as admin</label>
        <select class="form-select" id="admin_status" name="admin_status">
          <option value="admin_no">No</option>
          <option value="admin_yes">Yes</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary" name="register">Register</button>
    </form>

    <p class="mt-3">Already registered? <a href="login.php">Click here</a> to log in.</p>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>