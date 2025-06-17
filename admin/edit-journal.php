<?php
include(__DIR__ . '/../include/db_connect.php');  

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    echo "<script>alert('Unauthorized access!'); window.location.href='../admin_login.php';</script>";
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$id) {
    echo "<script>alert('Invalid request!'); window.location.href='view-journal.php';</script>";
    exit();
}

$journal = getJournalById($conn, $id);
if (!$journal) {
    echo "<script>alert('Journal not found!'); window.location.href='view-journal.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $updated = processEditJournal($id, $_POST, $_FILES);
    if ($updated) {
        echo "<script>alert('Journal updated successfully!'); window.location.href='view-journal.php';</script>";
    } else {
        echo "<script>alert('Error updating journal.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Journal</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #e0f7fa, #e1bee7);
            padding: 20px;
        }

        .container {
            max-width: 750px;
            margin: auto;
            background: white;
            padding: 30px 40px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 26px;
            margin-bottom: 25px;
        }

        label {
            font-weight: 600;
            margin-top: 15px;
            display: block;
            color: #444;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            background: linear-gradient(135deg,rgb(162, 164, 229),rgb(159, 121, 165));
        }

        textarea { min-height: 100px; resize: vertical; }

        .btn-group {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 25px;
            font-size: 15px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .save-btn { background: #5c6bc0; color: white; }
        .save-btn:hover { background: #3f51b5; }
        .cancel-btn { background: #ef5350; color: white; text-align: center; line-height: 36px; text-decoration: none; }
        .cancel-btn:hover { background: #d32f2f; }

        .image-preview img {
            margin-top: 12px;
            max-width: 140px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .btn-group { flex-direction: column; gap: 10px; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Journal</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Journal Name:</label>
        <input type="text" name="journal_name" value="<?= htmlspecialchars($journal['journal_name']) ?>" required>

        <label>Journal Abbreviation:</label>
        <input type="text" name="journal_abbreviation" value="<?= htmlspecialchars($journal['journal_abbreviation']) ?>" required>

        <label>Editor-in-Chief / Editorial Board:</label>
        <textarea name="editorial_board"><?= htmlspecialchars($journal['editorial_board']) ?></textarea>

        <label>Primary Subject:</label>
        <input type="text" name="primary_subject" value="<?= htmlspecialchars($journal['primary_subject']) ?>" required>

        <label>Publisher:</label>
        <input type="text" name="publisher" value="<?= htmlspecialchars($journal['publisher']) ?>" required>

        <label>ISSN (Format: XXXX-XXXX):</label>
        <input type="text" name="issn" value="<?= htmlspecialchars($journal['issn'] ?? '') ?>">

        <label>Country of Publication:</label>
        <input type="text" name="country" value="<?= htmlspecialchars($journal['country']) ?>">

        <label>Publication Frequency:</label>
        <input type="text" name="publication_frequency" value="<?= htmlspecialchars($journal['publication_frequency']) ?>">

        <label>Scope of the Journal:</label>
        <textarea name="scope"><?= htmlspecialchars($journal['scope']) ?></textarea>

        <label>Review Process Type:</label>
        <select name="review_process">
            <option value="Single-blind" <?= ($journal['review_process'] == "Single-blind" ? 'selected' : '') ?>>Single-blind</option>
            <option value="Open Review" <?= ($journal['review_process'] == "Open Review" ? 'selected' : '') ?>>Open Review</option>
        </select>

        <label>Impact Factor:</label>
        <input type="number" step="0.1" name="impact_factor" value="<?= htmlspecialchars($journal['impact_factor'] ?? '') ?>">

        <label>CiteScore:</label>
        <input type="number" step="0.1" name="citescore" value="<?= htmlspecialchars($journal['citescore'] ?? '') ?>">

        <label>Acceptance Rate:</label>
        <input type="text" name="acceptance_rate" value="<?= htmlspecialchars($journal['acceptance_rate'] ?? '') ?>">

        <label>Access Type:</label>
        <select name="access_type" id="access_type">
            <option value="Open Access" <?= ($journal['access_type'] == 'Open Access') ? 'selected' : '' ?>>Open Access</option>
            <option value="Subscription-Based" <?= ($journal['access_type'] == 'Subscription-Based') ? 'selected' : '' ?>>Subscription-Based</option>
        </select>

        <label>Indexing Information:</label>
<textarea name="indexing_info"><?= htmlspecialchars($journal['indexing_info'] ?? '') ?></textarea>


        <!-- Payment Section for Open Access -->
        <div id="open-access-fields" style="display: <?= ($journal['access_type'] == 'Open Access') ? 'block' : 'none' ?>">
            <label><input type="checkbox" name="author_payment_required" value="1" <?= $journal['author_payment_required'] ? 'checked' : '' ?>> Author Payment Required</label>
            <label>Author APC Amount (₹):</label>
            <input type="number" step="0.01" name="author_apc_amount" value="<?= htmlspecialchars($journal['author_apc_amount']) ?>">
        </div>

        <!-- Payment Section for Subscription-Based -->
        <div id="subscription-fields" style="display: <?= ($journal['access_type'] == 'Subscription-Based') ? 'block' : 'none' ?>">
            <label><input type="checkbox" name="reader_payment_required" value="1" <?= $journal['reader_payment_required'] ? 'checked' : '' ?>> Reader Payment Required</label>
            <label>Reader Fee Amount (₹):</label>
            <input type="number" step="0.01" name="reader_fee_amount" value="<?= htmlspecialchars($journal['reader_fee_amount']) ?>">
        </div>

        <!-- Common payment fields -->
        <label>Payment Currency:</label>
<input type="text" name="payment_currency" value="<?= $journal['payment_currency'] === null ? 'null' : htmlspecialchars($journal['payment_currency']) ?>">

<label>Payment Link:</label>
<input type="text" name="payment_link" value="<?= $journal['payment_link'] === null ? 'null' : htmlspecialchars($journal['payment_link']) ?>">

<label>Payment Notes:</label>
<textarea name="payment_notes"><?= $journal['payment_notes'] === null ? 'null' : htmlspecialchars($journal['payment_notes']) ?></textarea>

        <label>Upload Journal Image:</label>
        <input type="file" name="journal_image">
        <div class="image-preview">
            <?php if (!empty($journal['journal_image'])): ?>
                <img src="<?= htmlspecialchars($journal['journal_image']) ?>" alt="Journal Image">
            <?php endif; ?>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn save-btn">Update Journal</button>
            <a href="view-journal.php" class="btn cancel-btn">Cancel</a>
        </div>
    </form>
</div>

<script>
    const accessSelect = document.getElementById('access_type');
    const openAccessFields = document.getElementById('open-access-fields');
    const subscriptionFields = document.getElementById('subscription-fields');

    accessSelect.addEventListener('change', function () {
        if (this.value === 'Open Access') {
            openAccessFields.style.display = 'block';
            subscriptionFields.style.display = 'none';
        } else if (this.value === 'Subscription-Based') {
            openAccessFields.style.display = 'none';
            subscriptionFields.style.display = 'block';
        } else {
            openAccessFields.style.display = 'none';
            subscriptionFields.style.display = 'none';
        }
    });
</script>

</body>
</html>
