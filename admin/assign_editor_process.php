<?php 
session_start();
include(__DIR__ . "/../include/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paper_id = $_POST['paper_id'];
    $editor_id = $_POST['editor_id'];

    if (assignEditorToPaper($conn, $editor_id, $paper_id)) {
        header("Location: assign_editor.php?success=1");
    } else {
        header("Location: assign_editor.php?error=1");
    }
}
?>
