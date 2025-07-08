<?php  
session_start();
include(__DIR__ . "/../../include/db_connect.php");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $editorData = getEditorByEmail($conn, $email);

    if ($editorData) {
        if ($editorData['registration_status'] !== "approved") {
            $_SESSION['error'] = "Your application is under review .Once it gets approved, You will recieve an email.";
            $conn->close();
            header("Location: editor_login.php");
            exit();
        }

        if (password_verify($password, $editorData['hashed_password'])) {
            updateEditorLastLogin($conn, $editorData['editor_id']);

            $role = getEditorRole($conn, $editorData['editor_id']);
            $is_chief = ($role === 'chief editor');

            $_SESSION['editor_id'] = $editorData['editor_id'];
            $_SESSION['editor_role'] = $is_chief ? 'Chief Editor' : 'Editor';

            if ($is_chief) {
                $_SESSION['chief_editor_id'] = $editorData['editor_id'];
                header("Location: chief-dashboard.php");
            } else {
                header("Location: editor_dashboard.php");
            }
            $conn->close();
            exit();
        } else {
            $_SESSION['error'] = "Incorrect password.";
        }
    } else {
        $_SESSION['error'] = "Invalid email or password.";
    }

    $conn->close();
    header("Location: editor_login.php");
    exit();
}
?>
