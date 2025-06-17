<?php
include("../include/db_connect.php");

if (isset($_GET['id'])) {
    $reviewer_id = intval($_GET['id']);

    // Call the function to update contract status
    if (updateContractStatusRev($conn, $reviewer_id)) {
        echo "Contract verified.";
    } else {
        echo "Error verifying contract.";
    }
} else {
    echo "No reviewer ID provided.";
}
?>

<a href="approve_reviewer.php?id=<?= htmlspecialchars($_GET['id']); ?>" class="btn approve-btn">Approve</a>
