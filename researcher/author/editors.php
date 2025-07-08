<?php 
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_SESSION['chief_editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

$chief_editor_id = $_SESSION['chief_editor_id'];

$team_result = getTeamIdByEditor($conn, $chief_editor_id);
$team = $team_result->fetch_assoc();

if (!$team) {
    die("Team information not found.");
}

$team_id = $team['team_id'];
$result = getTeamEditors_1($conn, $team_id, $chief_editor_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Editorial Team Members</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
       body {
    background: linear-gradient(135deg, #f0f4ff, #e4f1f9);
    padding: 20px;
    font-family: 'Segoe UI', sans-serif;
}

.card {
    background: #ffffff;
    border-radius: 20px;
    border: none;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease-in-out;
}

.card:hover {
    transform: scale(1.02);
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
}

.status-dot {
    height: 12px;
    width: 12px;
    border-radius: 50%;
    display: inline-block;
}

.status-active {
    background-color: #28a745;
}

.status-inactive {
    background-color: #dc3545;
}

.title-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 30px;
}

.title-bar h2 {
    color: #2c3e50;
    font-weight: bold;
}
.btn-back {
        display: inline-block;
        margin: 20px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #4b6cb7, #182848);
        color: white;
        border: none;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 500;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background: linear-gradient(135deg, #182848, #4b6cb7);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }

    </style>
</head>
<body>
<a href="chief-dashboard.php" class="btn-back">← Back to Dashboard</a>

<div class="container">
    <div class="title-bar mb-4">
        <h2>📋 Editorial Team Members</h2>
    </div>

    <div class="row">
        <?php while ($editor = $result->fetch_assoc()) {
            $fullName = $editor['first_name'] . ' ' . $editor['middle_name'] . ' ' . $editor['last_name'];
            $lastLogin = $editor['last_login'];
            $status = 'Inactive';
            $status_class = 'status-inactive';

            if ($lastLogin && (strtotime($lastLogin) >= strtotime('-5 days'))) {
                $status = 'Active';
                $status_class = 'status-active';
            }
        ?>
        <div class="col-md-6 mb-4">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?php echo htmlspecialchars($fullName); ?></h5>
                    <span class="status-dot <?php echo $status_class; ?>" title="<?php echo $status; ?>"></span>
                </div>
                <hr>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($editor['role']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($editor['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($editor['telephone']); ?></p>
                <p><strong>Degree:</strong> <?php echo htmlspecialchars($editor['degree']); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($editor['gender']); ?></p>
                <p><strong>Editor Experience:</strong> <?php echo htmlspecialchars($editor['editor_experience']); ?></p>
                <p><strong>Last Login:</strong> <?php echo $editor['last_login'] ? $editor['last_login'] : 'Never'; ?></p>

                <?php if (strtolower($editor['editor_type']) === 'affiliated') { ?>
                    <hr>
                    <p><strong>Position:</strong> <?php echo htmlspecialchars($editor['position']); ?></p>
                    <p><strong>Institution:</strong> <?php echo htmlspecialchars($editor['institution']); ?></p>
                    <p><strong>Department:</strong> <?php echo htmlspecialchars($editor['department']); ?></p>
                    <p><strong>Location:</strong> 
                        <?php echo htmlspecialchars($editor['city']); ?>, 
                        <?php echo htmlspecialchars($editor['state']); ?>, 
                        <?php echo htmlspecialchars($editor['country']); ?>
                    </p>
                <?php } else { ?>
                    <hr>
                    <p><strong>Editor Type:</strong> Individual</p>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

</body>
</html>
