<?php
require_once 'config.php';
require_once 'classes/database.php';
require_once 'classes/user.php';

function handleLoginFormSubmission($pdo)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['loginUsername']) && isset($_POST['loginPassword'])) {
        $username = $_POST['loginUsername'];
        $password = $_POST['loginPassword'];

        $stmt = $pdo->prepare("SELECT * FROM utenti WHERE username = ?");
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row['password'])) {
            // Login eseguito
            session_start();
            $_SESSION['userLogin'] = $username;
            header('Location: http://localhost/index.php');
            exit();
        } else {
            // Password errata o utente non trovato
            echo "Login failed. Incorrect username or password.";
        }
    }
}

function generateUserTable($pdo, $loggedInUserAdmin, $userDTO)
{
    $html = '<div class="table-responsive">
    <table class="table table-striped table-hover">
    <thead>
    <tr>
    <th>ID</th>
    <th>Username</th>
    <th>Admin</th>
    <th>Action</th>
    </tr></thead><tbody>';

    $sql = "SELECT id, username, admin_status FROM utenti";
    $stmt = $pdo->query($sql);

    if ($stmt) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $html .= '<tr>';
            $html .= '<td>' . $row['id'] . '</td>';
            $html .= '<td>' . $row['username'] . '</td>';
            $admin_status = ($row['admin_status'] == 1) ? 'Yes' : 'No';
            $html .= '<td>' . $admin_status . '</td>';
            $html .= '<td>';
            if ($loggedInUserAdmin || $row['username'] === $_SESSION['userLogin']) {
                // Pulsanti per CRUD
                $html .= '<form method="post" action="">';
                $html .= '<input type="hidden" name="edit_user_id" value="' . $row['id'] . '">';
                $html .= '<div class="d-flex">';
                $html .= '<button type="submit" class="btn btn-primary" name="edit_profile">Edit Profile</button>';
                $html .= '</form>';
                $html .= '<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal' . $row['id'] . '">Delete Profile</button>';
                $html .= '</div>';
                // Conferma l'eliminazione del profilo
                $html .= '<div class="modal fade" id="confirmDeleteModal' . $row['id'] . '" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">';
                $html .= '<div class="modal-dialog">';
                $html .= '<div class="modal-content">';
                $html .= '<div class="modal-header">';
                $html .= '<h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Delete</h5>';
                $html .= '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                $html .= '</div>';
                $html .= '<div class="modal-body">';
                $html .= 'Are you sure you want to delete this profile?';
                $html .= '</div>';
                $html .= '<div class="modal-footer">';
                $html .= '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>';
                $html .= '<form method="post" action="">';
                $html .= '<input type="hidden" name="delete_user_id" value="' . $row['id'] . '">';
                $html .= '<button type="submit" class="btn btn-danger" name="confirm_delete_profile">Delete</button>';
                $html .= '</form>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</td></tr>';
        }
    } else {
        $html .= '<tr><td colspan="4">No users found</td></tr>';
    }

    $html .= '</tbody></table></div>';

    echo $html;

    if (isset($_POST['edit_profile'])) {
        $userId = $_POST['edit_user_id'];
        $user = $userDTO->getUserByID($userId);
        if ($user) {
            echo '<div class="mt-3">
            <h2>Edit Profile</h2>
            <form method="post" action="">
            <input type="hidden" name="edited_user_id" value="' . $user['id'] . '">
            <label for="edit_username">Username:</label>
            <input type="text" id="edit_username" name="edit_username" value="' . $user['username'] . '">
            <label for="edit_password">Password:</label>
            <input type="password" id="edit_password" name="edit_password">
            <button type="submit" class="btn btn-primary" name="save_edit">Save Changes</button>
            </form>
            </div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">User not found.</div>';
        }
    }
}



function handleUpdateProfile($pdo, $userDTO, $loggedInUserAdmin)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['save_edit'])) {
            $userId = $_POST['edited_user_id'];
            $editedUsername = $_POST['edit_username'];
            $editedPassword = $_POST['edit_password'];
            $editedAdminStatus = isset($_POST['edit_admin']) ? 1 : 0;

            $hashedPassword = password_hash($editedPassword, PASSWORD_DEFAULT);

            $updatedUserData = [
                'id' => $userId,
                'username' => $editedUsername,
                'password' => $hashedPassword,
                'admin_status' => $editedAdminStatus
            ];

            try {
                $result = $userDTO->updateUser($updatedUserData);

                if ($result) {
                    // Update per l'admin (Solo se ha cambiato il suo stesso)
                    if (!$loggedInUserAdmin || $_SESSION['userLogin'] === $editedUsername) {
                        $_SESSION['userLogin'] = $editedUsername;
                    }
                    header("Refresh:0"); // Refresh
                    exit();
                } else {
                    echo "Failed to update user profile.";
                }
            } catch (PDOException $e) {
                echo "Error updating user profile: " . $e->getMessage();
            }
        }
        if (isset($_POST['confirm_delete_profile'])) {
            $userId = $_POST['delete_user_id'];

            $result = $userDTO->deleteUser($userId);

            if ($result) {
                header("Refresh:0");
            } else {
                echo "Failed to delete user profile.";
            }
        }
    }
}
