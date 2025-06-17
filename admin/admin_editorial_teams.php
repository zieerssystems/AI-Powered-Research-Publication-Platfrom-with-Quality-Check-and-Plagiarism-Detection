<?php 
include("../include/db_connect.php");
// Handle team creation
if (isset($_POST['create_team'])) {
    $team_name = $_POST['team_name'];
    $team_id = createEditorialTeam($conn, $team_name);

    // Handle member addition
    if (!empty($_POST['editor_id']) && is_array($_POST['editor_id'])) {
        foreach ($_POST['editor_id'] as $index => $editor_id) {
            $role = $_POST['editor_role'][$index];
            addTeamMember($conn, $team_id, $editor_id, $role);
        }
    }
}
$message = "";
if (isset($_POST['add_editor_to_team'])) {
    $team_id = $_POST['existing_team_id'];
    $editor_id = $_POST['editor_id'];
    $role = $_POST['editor_role'];

    // Check if editor is already in the team
    if (!isEditorInTeam($conn, $team_id, $editor_id)) {
        addTeamMember($conn, $team_id, $editor_id, $role);
        $message = "Editor successfully added to the team.";
    } else {
        $message = "Editor is already a member of this team.";
    }
}

// Assign team to journal
if (isset($_POST['assign_team'])) {
    $journal_id = $_POST['journal_id'];
    $team_id = $_POST['team_id'];

    // Step 1: Update the journal's editorial_team_id
    assignTeamToJournal($conn, $journal_id, $team_id);

    // Step 2: Find the Chief Editor of this team
    $chief_editor = getChiefEditor($conn, $team_id);

    if ($chief_editor) {
        $chief_editor_id = $chief_editor['editor_id'];

        // Step 3: Update papers in this journal to set their editor_id
        updatePapersWithChiefEditor($conn, $chief_editor_id, $journal_id);
    }
}

// Deletion handlers
if (isset($_GET['delete_member_id'])) {
    $id = $_GET['delete_member_id'];
    deleteTeamMember($conn, $id);
}

if (isset($_GET['delete_team_id'])) {
    $team_id = $_GET['delete_team_id'];
    deleteTeam($conn, $team_id);
}

// Fetch data
$teams = getAllTeams($conn);
$journals = getAllJournals($conn);
$editors = getAllEditor($conn);
$journalTeamAssignments = getJournalTeamAssignments($conn);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Editorial Teams</title>
  <style>
    body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: linear-gradient(to right, #f5f7fa, #c3cfe2);
  margin: 0;
  padding: 20px;
}

/* Container */
.container {
  max-width: 1100px;
  margin: auto;
  padding: 20px;
}

/* Headers */
h2, h3 {
  text-align: center;
  color: #333;
}

/* Forms and Cards */
form, .table {
  background: #fff;
  padding: 25px;
  border-radius: 16px;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
  margin-bottom: 40px;
}

/* Labels and Inputs */
label {
  display: block;
  margin-top: 10px;
  font-weight: bold;
}

input[type="text"], select {
  width: 100%;
  padding: 10px 14px;
  margin-top: 5px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 15px;
}

/* Buttons */
input[type="submit"], .add-row-btn, button[type="button"] {
  background: #4CAF50;
  color: white;
  border: none;
  padding: 10px 24px;
  margin-top: 15px;
  cursor: pointer;
  font-size: 15px;
  border-radius: 6px;
  transition: background 0.3s;
}

input[type="submit"]:hover,
.add-row-btn:hover,
button[type="button"]:hover {
  background: #45a049;
}

/* Add Editor Button */
.add-row-btn {
  background: #2196F3;
}

.add-row-btn:hover {
  background: #1976D2;
}

/* Editor Rows */
.editor-row {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 10px;
}

.editor-row select,
.editor-row input {
  flex: 1;
}

/* Table Styling */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
}

th, td {
  padding: 12px 15px;
  border: 1px solid #ddd;
  text-align: left;
}

th {
  background-color: #f1f1f1;
  font-weight: 600;
}

/* Delete links */
a {
  text-decoration: none;
}

a[href*="delete"] {
  color: #e74c3c;
  font-weight: bold;
}

a[href*="delete"]:hover {
  text-decoration: underline;
}

/* Team Header */
.table h3 {
  background: #f8f8f8;
  padding: 12px 16px;
  border-radius: 8px;
  margin-top: 0;
  margin-bottom: 10px;
  position: relative;
}

/* Responsive */
@media (max-width: 768px) {
  .editor-row {
    flex-direction: column;
  }

  input[type="submit"], .add-row-btn {
    width: 100%;
  }
}
 .back-btn {
            margin: 20px 30px 0;
        }
  </style>
  <script>
    const editorDropdown = <?php
  mysqli_data_seek($editors, 0);
  $options = "";
  while ($editor = $editors->fetch_assoc()) {
    $name = htmlspecialchars($editor['first_name'] . ' ' . $editor['last_name']);
    $id = $editor['editor_id'];
    $options .= "<option value='$id'>$name</option>";
  }
  echo "<select name='editor_id[]' required>$options</select>";
?>;

    function addEditorRow() {
      const container = document.getElementById('editorRows');
      const div = document.createElement('div');
      div.className = 'editor-row';
      div.innerHTML = 
        ${editorDropdown}
        <input type="text" name="editor_role[]" placeholder="Role (e.g. Chief Editor)" required />
      ;
      container.appendChild(div);
    }
  </script>
</head>
<body>
<div class="container">
<div class="text-center mb-3">
  <!-- Back Button -->
<div class="back-btn">
    <a href="admin_dashboard.php" class="btn btn-outline-secondary">⬅ Back to Dashboard |</a>
       <a href="editor_contracts.php" class="btn btn-primary">← Back verify Editors</a>
</div>
</div>
  <h2>Create Editorial Team</h2>
  <form method="POST">
    <label for="team_name">Team Name:</label>
    <input type="text" name="team_name" required>

    <h4>Assign Editors and Roles:</h4>
    <div id="editorRows">
      <div class="editor-row">
      <select name="editor_id[]" required>
      <?php 
      mysqli_data_seek($editors, 0); 
      while($editor = $editors->fetch_assoc()): 
        $full_name = htmlspecialchars($editor['first_name'] . ' ' . $editor['last_name']);
      ?>
        <option value="<?= $editor['editor_id'] ?>"><?= $full_name ?></option>
      <?php endwhile; ?>
    </select>

        <input type="text" name="editor_role[]" placeholder="Role (e.g. Chief Editor)" required />
      </div>
    </div>
    <button type="button" class="add-row-btn" onclick="addEditorRow()">+ Add More</button>
    <br>
    <input type="submit" name="create_team" value="Create Team">
  </form>

  <h2>Assign Editorial Team to Journal</h2>
  <form method="POST">
    <label for="journal_id">Select Journal:</label>
    <select name="journal_id">
      <?php mysqli_data_seek($journals, 0); while($journal = $journals->fetch_assoc()): ?>
        <option value="<?= $journal['id'] ?>"> <?= htmlspecialchars($journal['journal_name']) ?> </option>
      <?php endwhile; ?>
    </select>

    <label for="team_id">Select Editorial Team:</label>
    <select name="team_id">
      <?php 
      $teams_for_dropdown = $conn->query("SELECT * FROM editorial_teams");
      while ($team = $teams_for_dropdown->fetch_assoc()): ?>
        <option value="<?= $team['team_id'] ?>"> <?= htmlspecialchars($team['team_name']) ?> </option>
      <?php endwhile; ?>
    </select>

    <input type="submit" name="assign_team" value="Assign Team">
  </form>

  <h2>Current Journal Team Assignments</h2>
  <div class="table">
    <table>
      <thead>
        <tr>
          <th>Journal Name</th>
          <th>Editorial Team</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $result = $conn->query("SELECT j.journal_name, t.team_name FROM journals j LEFT JOIN editorial_teams t ON j.editorial_team_id = t.team_id");
        while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['journal_name']) ?></td>
            <td><?= $row['team_name'] ? htmlspecialchars($row['team_name']) : '<i>Editorial Board: Not Assigned</i>' ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php if (!empty($message)): ?>
  <div style="background-color: #e0f7fa; padding: 10px; margin: 10px 0; border: 1px solid #00acc1; color: #006064;">
    <?= htmlspecialchars($message) ?>
  </div>
<?php endif; ?>
<h2>Add Editor to Existing Team</h2>
<form method="POST">
  <label for="existing_team_id">Select Editorial Team:</label>
  <select name="existing_team_id" required>
    <?php 
    mysqli_data_seek($teams, 0); 
    while ($team = $teams->fetch_assoc()): ?>
      <option value="<?= $team['team_id'] ?>"><?= htmlspecialchars($team['team_name']) ?></option>
    <?php endwhile; ?>
  </select>

  <label for="editor_id">Select Editor:</label>
  <select name="editor_id" required>
    <?php 
    mysqli_data_seek($editors, 0); 
    while ($editor = $editors->fetch_assoc()):
      $full_name = htmlspecialchars($editor['first_name'] . ' ' . $editor['last_name']);
    ?>
      <option value="<?= $editor['editor_id'] ?>"><?= $full_name ?></option>
    <?php endwhile; ?>
  </select>

  <label for="editor_role">Role:</label>
  <input type="text" name="editor_role" placeholder="Role (e.g. Reviewer)" required />

  <input type="submit" name="add_editor_to_team" value="Add Editor to Team">
</form>


  <h2>Editorial Teams and Members</h2>
  <?php 
  $teams = $conn->query("SELECT * FROM editorial_teams");
  while ($team = $teams->fetch_assoc()):
    $team_id = $team['team_id'];
    $team_name = htmlspecialchars($team['team_name']);
  ?>
    <div class='table'>
      <h3><?= $team_name ?>
        <a href='?delete_team_id=<?= $team_id ?>' onclick='return confirm("Delete this team?")' style='color:red; float:right;'>Delete Team</a>
      </h3>
      <table>
        <thead><tr><th>Editor Name</th><th>Role</th><th>Action</th></tr></thead>
        <tbody>
        <?php 
   $members = $conn->query("SELECT etm.id, u.first_name, u.last_name, u.email, etm.role 
                        FROM editorial_team_members etm
                        JOIN editors e ON e.editor_id = etm.editor_id
                        JOIN users u ON e.user_id = u.id
                        WHERE etm.team_id = $team_id");
        while ($member = $members->fetch_assoc()):
          $full_name = htmlspecialchars($member['first_name'] . ' ' . $member['last_name']);
          $role = htmlspecialchars($member['role']);
          $member_id = $member['id'];
        ?>
          <tr>
            <td><?= $full_name ?></td>
            <td><?= $role ?></td>
            <td>
              <a href='?delete_member_id=<?= $member_id ?>' style='color:red;' onclick='return confirm("Remove editor from team?")'>Remove</a>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div><br>
  <?php endwhile; ?>
</div>
</body>
</html>