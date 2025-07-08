<?php
session_start();

// Unset only the editor-specific sessions
unset($_SESSION['editor_id']);       // if you're using this
unset($_SESSION['chief_editor_id']); // if you're using this
unset($_SESSION['user_role']);       // optional, if role-based system used

// Redirect to editor login
header("Location: editor_login.php?logged_out=1");
exit();
?>
