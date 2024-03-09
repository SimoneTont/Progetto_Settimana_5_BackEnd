<?php
session_start();
require_once 'functions.php';
require_once 'config.php';
require_once 'classes/database.php';
require_once 'classes/user.php';

use db\DB_PDO;
use User\UserDTO;

// Istanza della connessione al database
$db = DB_PDO::getInstance($config);
$pdo = $db->getConnection();

// Istanzia l'oggetto UserDTO
$userDTO = new UserDTO($pdo);

// Verifica se l'utente è loggato
if (!isset($_SESSION['userLogin'])) {
  // Reindirizza alla pagina di login se non è loggato
  header('Location: http://localhost/login.php');
  exit();
}

// Ottiene il nome utente dell'utente loggato
$userName = $_SESSION['userLogin'];

// Ottiene lo stato di amministratore dell'utente loggato
$loggedInUserAdmin = $userDTO->isAdmin($userName);

$loggedInUser = $userDTO->getUserByUsername($_SESSION['userLogin']);

if ($loggedInUser) {
  // Fa il redirect quando l'utente cancella il suo profilo
} else {
  header('Location: http://localhost/logout.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Title</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="index.php">Home</a>
          </li>
        </ul>
        <span class="navbar-text">
          <a class="nav-link active" aria-current="page" href="logout.php">Logout</a>
        </span>
      </div>
    </div>
  </nav>
  <div class="container">
    <h1 class="text-center"><?php echo "Hello, " . $userName; ?></h1>
    <p class="text-center h2 mt-3">Users List</p>
    <div class="container">
      <?php
      generateUserTable($pdo, $loggedInUserAdmin, $userDTO);
      handleUpdateProfile($pdo, $userDTO, $loggedInUserAdmin);
      ?>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>