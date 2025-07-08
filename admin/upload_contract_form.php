<form action="upload_contract.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="reviewer_id" value="<?php echo $_GET['id']; ?>">
    <label for="contract">Upload Signed Contract (PDF Only):</label>
    <input type="file" name="contract" accept="application/pdf" required>
    <button type="submit">Upload Contract</button>
</form>
