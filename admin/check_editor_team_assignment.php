<?php 
include("../include/db_connect.php");
if (isset($_POST['editor_id'])) {
    $editor_id = $_POST['editor_id'];

    echo isEditorAssigned($conn, $editor_id) ? 'assigned' : 'not_assigned';
}
?>
