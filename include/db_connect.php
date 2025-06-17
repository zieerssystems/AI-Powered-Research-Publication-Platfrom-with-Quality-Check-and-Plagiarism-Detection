<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load credentials from INI file
$config = parse_ini_file(__DIR__ . '/config.ini', true);
$db = $config['database'];

$servername = $db['servername'];
$username = $db['username'];
$password = $db['password'];
$database = $db['database'];

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8");

function process_view_journal($conn) {
    $sql = "SELECT * FROM journals ORDER BY created_at DESC";
    return $conn->query($sql);
}

function processEditJournal($id, $postData, $fileData) {
    global $conn;

    $journal_name = $conn->real_escape_string(trim($postData['journal_name']));
    $journal_abbreviation = $conn->real_escape_string(trim($postData['journal_abbreviation']));
    $editorial_board = $conn->real_escape_string(trim($postData['editorial_board']));
    $primary_subject = $conn->real_escape_string(trim($postData['primary_subject']));
    $publisher = $conn->real_escape_string(trim($postData['publisher']));
    $issn = $conn->real_escape_string(trim($postData['issn']));
    $country = $conn->real_escape_string(trim($postData['country']));
    $publication_frequency = $conn->real_escape_string(trim($postData['publication_frequency']));
    $scope = $conn->real_escape_string(trim($postData['scope']));
    $review_process = $conn->real_escape_string(trim($postData['review_process']));
    $impact_factor = floatval($postData['impact_factor']);
    $citescore = floatval($postData['citescore']);
    $acceptance_rate = floatval($postData['acceptance_rate']);
    $access_type = $conn->real_escape_string(trim($postData['access_type']));
    $indexing_info = $conn->real_escape_string(trim($postData['indexing_info']));

    $author_payment_required = isset($postData['author_payment_required']) ? 1 : 0;
    $reader_payment_required = isset($postData['reader_payment_required']) ? 1 : 0;
    $author_apc_amount = isset($postData['author_apc_amount']) ? floatval($postData['author_apc_amount']) : 0.00;
    $reader_fee_amount = isset($postData['reader_fee_amount']) ? floatval($postData['reader_fee_amount']) : 0.00;
    $payment_currency = $conn->real_escape_string(trim($postData['payment_currency'] ?? ''));
    $payment_link = $conn->real_escape_string(trim($postData['payment_link'] ?? ''));
    $payment_notes = $conn->real_escape_string(trim($postData['payment_notes'] ?? ''));

    $imagePath = null;

    if (isset($fileData['journal_image']) && $fileData['journal_image']['error'] === 0) {
        $targetDir = "../uploads/journal_images/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $filename = basename($fileData['journal_image']['name']);
        $targetFilePath = $targetDir . time() . '_' . $filename;

        if (move_uploaded_file($fileData['journal_image']['tmp_name'], $targetFilePath)) {
            $imagePath = $conn->real_escape_string($targetFilePath);
        }
    }

    if ($imagePath) {
        $sql = "UPDATE journals SET journal_name = '$journal_name', journal_abbreviation = '$journal_abbreviation', editorial_board = '$editorial_board', primary_subject = '$primary_subject', publisher = '$publisher', issn = '$issn', country = '$country', publication_frequency = '$publication_frequency', scope = '$scope', review_process = '$review_process', impact_factor = $impact_factor, citescore = $citescore, acceptance_rate = $acceptance_rate, access_type = '$access_type', author_payment_required = $author_payment_required, reader_payment_required = $reader_payment_required, author_apc_amount = $author_apc_amount, reader_fee_amount = $reader_fee_amount, payment_currency = '$payment_currency', payment_link = '$payment_link', payment_notes = '$payment_notes', indexing_info = '$indexing_info', journal_image = '$imagePath' WHERE id = $id";
    } else {
        $sql = "UPDATE journals SET journal_name = '$journal_name', journal_abbreviation = '$journal_abbreviation', editorial_board = '$editorial_board', primary_subject = '$primary_subject', publisher = '$publisher', issn = '$issn', country = '$country', publication_frequency = '$publication_frequency', scope = '$scope', review_process = '$review_process', impact_factor = $impact_factor, citescore = $citescore, acceptance_rate = $acceptance_rate, access_type = '$access_type', author_payment_required = $author_payment_required, reader_payment_required = $reader_payment_required, author_apc_amount = $author_apc_amount, reader_fee_amount = $reader_fee_amount, payment_currency = '$payment_currency', payment_link = '$payment_link', payment_notes = '$payment_notes', indexing_info = '$indexing_info' WHERE id = $id";
    }

    return $conn->query($sql);
}

function getPublishedPaperCount($conn) {
    $sql = "SELECT COUNT(*) AS count FROM papers WHERE status = 'Published' AND volume IS NOT NULL AND volume != '' AND issue IS NOT NULL AND issue != ''";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}

function getJournalById($conn, $id) {
    $id = $conn->real_escape_string($id);
    $sql = "SELECT * FROM journals WHERE id = $id";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function deleteJournalById($conn, $journal_id) {
    $journal_id = $conn->real_escape_string($journal_id);
    $sql = "DELETE FROM journals WHERE id = $journal_id";
    return $conn->query($sql);
}
function getEditorDetails($conn, $editor_id) {
    $stmt = $conn->prepare("
        SELECT e.*, u.first_name, u.middle_name, u.last_name, u.email 
        FROM editors e 
        JOIN users u ON e.user_id = u.id 
        WHERE e.editor_id = ?
    ");
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
function getTotalManuscripts($conn) {
    return $conn->query("SELECT COUNT(*) AS total FROM papers")->fetch_assoc()['total'];
}

function getPendingReviews($conn) {
    return $conn->query("SELECT COUNT(*) AS pending FROM papers WHERE status = 'Under Review'")->fetch_assoc()['pending'];
}

function getAcceptedPapers($conn) {
    return $conn->query("SELECT COUNT(*) AS accepted FROM papers WHERE status = 'Published'")->fetch_assoc()['accepted'];
}

function getRejectedPapers($conn) {
    return $conn->query("SELECT COUNT(*) AS rejected FROM papers WHERE status = 'Rejected (Post-Review)'")->fetch_assoc()['rejected'];
}

function getTotalReviewers($conn) {
    return $conn->query("SELECT COUNT(*) AS total FROM reviewers")->fetch_assoc()['total'];
}

function getActiveReviewers($conn) {
    return $conn->query("SELECT COUNT(DISTINCT reviewer_id) AS active FROM paper_assignments WHERE status = 'In-Review'")->fetch_assoc()['active'];
}

function getTopAuthors($conn) {
    return $conn->query("
        SELECT u.first_name, u.last_name, COUNT(*) AS total_papers
        FROM papers p
        JOIN author a ON p.author_id = a.id
        JOIN users u ON a.user_id = u.id
        GROUP BY a.id
        ORDER BY total_papers DESC
        LIMIT 5
    ");
}

function getJournalStats($conn) {
    return $conn->query("
        SELECT j.journal_name, 
            SUM(CASE WHEN p.status = 'Published' THEN 1 ELSE 0 END) AS accepted,
            SUM(CASE WHEN p.status = 'Rejected (Post-Review)' THEN 1 ELSE 0 END) AS rejected
        FROM papers p 
        JOIN journals j ON p.journal_id = j.id 
        GROUP BY j.journal_name
    ");
}
function getReviewerByEmail($conn, $email) {
    $stmt = $conn->prepare("
        SELECT r.id, r.contract_status 
        FROM reviewers r
        JOIN users u ON r.user_id = u.id
        WHERE u.email = ?
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc(); // return single row or null
}
function getReviewerContractDetails($conn, $reviewer_id) {
    $stmt = $conn->prepare("SELECT contract_status, contract_file FROM reviewers WHERE id = ?");
    $stmt->bind_param("i", $reviewer_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateContractDetails($conn, $reviewer_id, $new_file_name, $upload_date) {
    $stmt = $conn->prepare("UPDATE reviewers SET contract_status = 'pending_verification', contract_file = ?, upload_date = ? WHERE id = ?");
    $stmt->bind_param("ssi", $new_file_name, $upload_date, $reviewer_id);
    return $stmt->execute();
}
function getPaperDetailsById($conn, $paper_id) {
    $stmt = $conn->prepare("SELECT title, file_path FROM papers WHERE id = ?");
    $stmt->bind_param("i", $paper_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getAssignedManuscriptsForReviewer($conn, $reviewer_id) {
    $query = "
        SELECT p.id, p.title, p.file_path, p.cover_letter_path, p.supplementary_files_path, 
               p.keywords, p.status AS paper_status, 
               j.journal_name, 
               CONCAT(u.first_name, ' ', u.last_name) AS author_name
        FROM papers p
        JOIN paper_assignments pa ON p.id = pa.paper_id
        JOIN journals j ON p.journal_id = j.id
        JOIN author a ON p.author_id = a.id
        JOIN users u ON a.user_id = u.id
        WHERE pa.reviewer_id = ? 
          AND pa.status = 'In-Review' 
          AND p.status = 'Under Review'
        ORDER BY p.submission_date DESC
    ";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("i", $reviewer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $stmt->close();
    return $result;
}
function updateReviewerPaperStatus($conn, $paper_id, $reviewer_id, $status) {
    $now = date("Y-m-d H:i:s");

    if ($status === 'Accepted') {
        $sql1 = "UPDATE paper_assignments SET status = 'Completed', completed_date = ? WHERE paper_id = ? AND reviewer_id = ?";
    } elseif ($status === 'Rejected') {
        $sql1 = "UPDATE paper_assignments SET status = 'Rejected', completed_date = ? WHERE paper_id = ? AND reviewer_id = ?";
    } else { // Revision Requested
        $sql1 = "UPDATE paper_assignments SET status = 'Revision Requested', revision_date = ? WHERE paper_id = ? AND reviewer_id = ?";
        $sql2 = "UPDATE papers SET status = 'Revision Requested' WHERE id = ?";
    }

    // Execute the first update
    $stmt1 = $conn->prepare($sql1);
    if (!$stmt1) return false;
    $stmt1->bind_param("sii", $now, $paper_id, $reviewer_id);
    $stmt1->execute();
    $stmt1->close();

    // If revision requested, update the paper table also
    if (isset($sql2)) {
        $stmt2 = $conn->prepare($sql2);
        if (!$stmt2) return false;
        $stmt2->bind_param("i", $paper_id);
        $stmt2->execute();
        $stmt2->close();
    }

    return true;
}

function updateEditorTaskStatus2($conn, $paper_id, $editor_id, $task_type, $decision) {
    $status = ($decision === 'accept') ? 'Accepted' : 'Rejected';
    
    $query = "
        UPDATE editor_tasks
        SET status = ?, response_date = NOW()
        WHERE paper_id = ? AND editor_id = ? AND task_type = ?
    ";

    $stmt = $conn->prepare($query);
    if (!$stmt) return false;

    $stmt->bind_param("siii", $status, $paper_id, $editor_id, $task_type);
    $result = $stmt->execute();
    $stmt->close();

    return $result;
}

function getUserIdByReviewerId($conn, $reviewer_id) {
    $stmt = $conn->prepare("SELECT user_id FROM reviewers WHERE id = ?");
    $stmt->bind_param("i", $reviewer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ? $row['user_id'] : null;
}
function getPrimarySubjects($conn) {
    $sql = "SELECT DISTINCT primary_subject FROM journals ORDER BY primary_subject ASC";
    $result = $conn->query($sql);
    $subjects = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $subjects[] = $row;
        }
    }
    return $subjects;
}
function hasUserPaidForPaper($conn, $author_id, $paper_id) {
    $stmt = $conn->prepare("SELECT id FROM payments WHERE author_id = ? AND paper_id = ? AND payment_status = 'paid' LIMIT 1");
    $stmt->bind_param("ii", $author_id, $paper_id);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}


function getPaperById1($conn, $paper_id) {
    $stmt = $conn->prepare("SELECT p.title, p.volume, p.issue, j.reader_fee_amount
        FROM papers p
        JOIN journals j ON p.journal_id = j.id
        WHERE p.id = ?");
    $stmt->bind_param("i", $paper_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function taskExists($conn, $paper_id, $task_type, $editor_id) {
    $check = $conn->prepare("SELECT id FROM editor_tasks WHERE paper_id = ? AND task_type = ? AND editor_id = ?");
    $check->bind_param("iii", $paper_id, $task_type, $editor_id);
    $check->execute();
    $check->store_result();
    $exists = $check->num_rows > 0;
    $check->close();
    return $exists;
}

function updateTask($conn, $paper_id, $task_type, $editor_id, $result, $status, $response_date) {
    $update = $conn->prepare("UPDATE editor_tasks SET result = ?, status = ?, response_date = ? WHERE paper_id = ? AND task_type = ? AND editor_id = ?");
    $update->bind_param("sssiii", $result, $status, $response_date, $paper_id, $task_type, $editor_id);
    $update->execute();
    $update->close();
}
function getEditorDetails3($conn, $editor_id) {
    $query = $conn->prepare("
        SELECT e.*, u.first_name, u.last_name, u.email, u.id AS user_id
        FROM editors e
        JOIN users u ON e.user_id = u.id
        WHERE e.editor_id = ?
    ");
    $query->bind_param("i", $editor_id);
    $query->execute();
    $result = $query->get_result();
    $editor = $result->num_rows > 0 ? $result->fetch_assoc() : null;
    $query->close();
    return $editor;
}
function updateAuthorProfile($conn, $data) {
    $query = $conn->prepare("UPDATE author SET 
        first_name = ?,  
        last_name = ?, 
        email = ?, 
        telephone = ?, 
        address = ?, 
        researcher_type = ?,
        institution = ?, 
        department = ?, 
        position = ?, 
        street_address = ?, 
        city = ?, 
        state = ?, 
        zip_code = ?, 
        country = ? 
        WHERE id = ?");

    $query->bind_param(
        "ssssssssssssssi",
        $data['first_name'],
        $data['last_name'],
        $data['email'],
        $data['telephone'],
        $data['address'],
        $data['researcher_type'],
        $data['institution'],
        $data['department'],
        $data['position'],
        $data['street_address'],
        $data['city'],
        $data['state'],
        $data['zip_code'],
        $data['country'],
        $data['author_id']
    );

    $success = $query->execute();
    $query->close();
    return $success;
}
function getTeamIdForChiefEditor($conn, $editorId) {
    $stmt = $conn->prepare("SELECT team_id FROM editorial_team_members WHERE editor_id = ?");
    $stmt->bind_param("i", $editorId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['team_id'] ?? null;
}

function getTeamEditors($conn, $teamId, $chiefId) {
    $stmt = $conn->prepare("
        SELECT e.editor_id, CONCAT(u.first_name, ' ', u.last_name) AS editor_name
        FROM editors e
        INNER JOIN users u ON e.user_id = u.id
        INNER JOIN editorial_team_members etm ON e.editor_id = etm.editor_id
        WHERE etm.team_id = ? AND e.editor_id != ?
    ");
    $stmt->bind_param("ii", $teamId, $chiefId);
    $stmt->execute();
    return $stmt->get_result();
}

function getPapersByChiefEditor($conn, $editorId) {
    $stmt = $conn->prepare("
        SELECT p.id AS paper_id, p.title AS paper_title, p.status,
               u.first_name AS author_first, u.last_name AS author_last
        FROM papers p
        JOIN author a ON p.author_id = a.id
        JOIN users u ON a.user_id = u.id
        WHERE p.editor_id = ? AND p.status NOT IN (
            'Rejected (Pre-Review)', 'Rejected (Post-Review)', 
            'Accepted (Final Decision)', 'Published'
        )
    ");
    $stmt->bind_param("i", $editorId);
    $stmt->execute();
    return $stmt->get_result();
}

function getAllAssignedTasks($conn) {
    $query = "
        SELECT t.paper_id, t.task_type, t.editor_id, t.deadline,
               u.first_name, u.last_name
        FROM editor_tasks t
        JOIN editors e ON t.editor_id = e.editor_id
        JOIN users u ON e.user_id = u.id
    ";
    $result = mysqli_query($conn, $query);
    $tasks = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $tasks[$row['paper_id']][] = $row;
    }
    return $tasks;
}

function hasPreviousTaskBeenProcessed($conn, $paperId, $taskType) {
    $prevTask = $taskType - 1;
    if ($prevTask < 1) return true;
    $stmt = $conn->prepare("
        SELECT result FROM editor_tasks 
        WHERE paper_id = ? AND task_type = ? 
        AND status = 'Completed' AND result = 'Processed for Next Level'
    ");
    $stmt->bind_param("ii", $paperId, $prevTask);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->num_rows > 0;
}
function hasAcceptedOrPublishedPapers($conn) {
    $sql = "SELECT COUNT(*) as count FROM papers WHERE status IN ('Accepted (Final Decision)', 'Published')";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }
    return false;
}

// Fetch completed editor tasks
function getCompletedEditorTasks($conn) {
    $sql = "
        SELECT 
            t.id, 
            t.paper_id, 
            t.editor_id, 
            p.title AS paper_title, 
            u.first_name, 
            u.last_name,
            t.task_type, 
            t.status, 
            t.deadline, 
            t.response_date, 
            t.reminder_sent, 
            t.result, 
            t.feedback
        FROM editor_tasks t
        JOIN papers p ON t.paper_id = p.id
        JOIN editors e ON t.editor_id = e.editor_id
        JOIN users u ON e.user_id = u.id
        WHERE t.status = 'Completed' AND t.task_type IN (1,2,3,4)
        ORDER BY t.deadline ASC
    ";
    return $conn->query($sql);
}

// Check if the next task exists
function isNextTaskAssigned($conn, $paper_id, $nextTaskType) {
    $sql = "SELECT 1 FROM editor_tasks WHERE paper_id = ? AND task_type = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $paper_id, $nextTaskType);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}
// function getJournalDetails($journal_id) {
//     global $conn;
//     $journal_id = $conn->real_escape_string($journal_id);
//     $result = $conn->query("SELECT * FROM journals WHERE id = $journal_id");
//     return $result->fetch_assoc();
// }

// Function to check if author is linked to journal
function isAuthorLinked($conn, $author_id, $journal_id) {
    $author_id = $conn->real_escape_string($author_id);
    $journal_id = $conn->real_escape_string($journal_id);
    $result = $conn->query("SELECT * FROM author_journal WHERE author_id = $author_id AND journal_id = $journal_id");
    return $result->num_rows > 0;
}
function fetchAuthorId($conn, $paper_id) {
    $paper_id = $conn->real_escape_string($paper_id);
    $result = $conn->query("SELECT author_id FROM papers WHERE id = $paper_id");
    $row = $result->fetch_assoc();
    return $row ? $row['author_id'] : null;
}

// Function to update paper status
function updatePaper($conn, $paper_status, $paper_id) {
    $paper_status = $conn->real_escape_string($paper_status);
    $paper_id = $conn->real_escape_string($paper_id);
    return $conn->query("UPDATE papers SET status = '$paper_status', updated_at = NOW() WHERE id = $paper_id");
}

// Function to update paper assignment status
function updatePaperAssignmentStatus($conn, $assignment_status, $paper_id, $reviewer_id) {
    $assignment_status = $conn->real_escape_string($assignment_status);
    $paper_id = $conn->real_escape_string($paper_id);
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    return $conn->query("UPDATE paper_assignments SET status = '$assignment_status' WHERE paper_id = $paper_id AND reviewer_id = $reviewer_id");
}

// Function to update completed date for paper assignment
function updateCompletedDate($conn, $paper_id, $reviewer_id) {
    $paper_id = $conn->real_escape_string($paper_id);
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    return $conn->query("UPDATE paper_assignments SET completed_date = NOW() WHERE paper_id = $paper_id AND reviewer_id = $reviewer_id");
}

// Function to insert or update feedback
function updateFeedback($conn, $paper_id, $reviewer_id, $feedback, $author_id, $journal_name) {
    $paper_id = $conn->real_escape_string($paper_id);
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    $feedback = $conn->real_escape_string($feedback);
    $author_id = $conn->real_escape_string($author_id);
    $journal_name = $conn->real_escape_string($journal_name);

    $check_result = $conn->query("SELECT id FROM feedback WHERE paper_id = $paper_id AND reviewer_id = $reviewer_id");

    if ($check_result->num_rows > 0) {
        return $conn->query("UPDATE feedback SET feedback = '$feedback', review_date = NOW() WHERE paper_id = $paper_id AND reviewer_id = $reviewer_id");
    } else {
        return $conn->query("INSERT INTO feedback (paper_id, reviewer_id, author_id, journal_name, feedback, review_date) VALUES ($paper_id, $reviewer_id, $author_id, '$journal_name', '$feedback', NOW())");
    }
}
function updatePaperStatusAndFeedback($conn, $decision, $comments, $paper_id) {
    $decision = $conn->real_escape_string($decision);
    $comments = $conn->real_escape_string($comments);
    $paper_id = $conn->real_escape_string($paper_id);
    return $conn->query("UPDATE papers SET status = '$decision', feedback = '$comments' WHERE id = $paper_id");
}
// Function to fetch papers for an author with feedback, editorial team info, and journal details
function fetchAuthorPapersWithFeedback($conn, $author_id) {
    $papers = [];
    $has_comments = false;

    $stmt = $conn->prepare("
        SELECT
            p.id,
            p.title,
            p.status,
            p.feedback,
            j.journal_name AS journal_name,
            et.team_name AS editorial_team_name
        FROM papers p
        JOIN journals j ON p.journal_id = j.id
        JOIN editorial_teams et ON j.editorial_team_id = et.team_id
        WHERE p.author_id = ? AND p.status = 'Accepted (Final Decision)'
    ");
    $stmt->bind_param("i", $author_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        if (trim($row['feedback']) !== '') {
            $has_comments = true;
        }
        $papers[] = $row;
    }
    $stmt->close();

    return ['papers' => $papers, 'has_comments' => $has_comments];
}
// Get count of accepted papers
function getAcceptedCount($conn) {
    $stmt = $conn->query("SELECT COUNT(*) AS count FROM papers WHERE status = 'Accepted (Final Decision)'");
    return $stmt->fetch_assoc()['count'] ?? 0;
}

// Get count of rejected papers
function getRejectedCount($conn) {
    $stmt = $conn->query("SELECT COUNT(*) AS count FROM papers WHERE status = 'Rejected (Post-Review)'");
    return $stmt->fetch_assoc()['count'] ?? 0;
}

// Get pending papers with all 4 editor tasks processed
function getPendingPapers($conn) {
    $query = "
        SELECT 
            p.id AS paper_id,
            p.title,
            p.status,
            CONCAT(u.first_name, ' ', COALESCE(u.middle_name, ''), ' ', u.last_name) AS author_name
        FROM papers p
        JOIN author a ON p.author_id = a.id
        JOIN users u ON a.user_id = u.id
        WHERE p.status = 'Under Review'
          AND p.id IN (
              SELECT paper_id
              FROM editor_tasks
              WHERE status = 'Completed'
                AND result = 'Processed for Next Level'
                AND task_type IN (1, 2, 3, 4)
              GROUP BY paper_id
              HAVING COUNT(DISTINCT task_type) = 4
          )
        ORDER BY p.submission_date DESC
    ";
    return $conn->query($query);
}

// Get editor tasks for a specific paper
function getEditorTasksByPaperId($conn, $paper_id) {
    $query = "
        SELECT 
            et.task_type, 
            et.feedback AS editor_feedback, 
            CONCAT(u.first_name, ' ', u.last_name) AS editor_name 
        FROM editor_tasks et
        JOIN editors e ON et.editor_id = e.editor_id
        JOIN users u ON e.user_id = u.id
        WHERE et.paper_id = $paper_id
          AND et.status = 'Completed'
          AND et.result = 'Processed for Next Level'
          AND et.task_type IN (1, 2, 3, 4)
    ";
    return $conn->query($query);
}

// Get reviewer feedback for a specific paper
function getReviewerFeedbackByPaperId($conn, $paper_id) {
    $query = "
        SELECT 
            f.feedback, 
            f.review_date, 
            CONCAT(u.first_name, ' ', u.last_name) AS reviewer_name
        FROM feedback f
        JOIN reviewers r ON f.reviewer_id = r.id
        JOIN users u ON r.user_id = u.id
        WHERE f.paper_id = $paper_id
    ";
    return $conn->query($query);
}
function getEditorDetails_1($conn, $editor_id) {
    $stmt = $conn->prepare("
        SELECT u.email, u.first_name, u.last_name, e.last_login
        FROM editors e
        JOIN users u ON e.user_id = u.id
        WHERE e.editor_id = ?
    ");
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getTotalManuscripts_1($conn, $editor_id) {
    return $conn->query("SELECT COUNT(*) AS total FROM papers WHERE editor_id = $editor_id")->fetch_assoc()['total'];
}

function getPendingReviews_1($conn, $editor_id) {
    return $conn->query("SELECT COUNT(*) AS pending FROM papers WHERE status = 'Under Review' AND editor_id = $editor_id")->fetch_assoc()['pending'];
}

function getDecisionsMade_1($conn, $editor_id) {
    return $conn->query("SELECT COUNT(*) AS decisions FROM papers WHERE status IN ('Accepted', 'Rejected') AND editor_id = $editor_id")->fetch_assoc()['decisions'];
}

function getNewManuscriptsCount_1($conn, $editor_id) {
    return $conn->query("SELECT COUNT(*) AS new_count FROM papers WHERE status = 'pending' AND editor_id = $editor_id")->fetch_assoc()['new_count'];
}

function getManuscripts_2($conn, $editor_id) {
    return $conn->query("
        SELECT 
            p.id, 
            p.title, 
            p.status, 
            CONCAT(u.first_name, ' ', COALESCE(u.middle_name, ''), ' ', u.last_name) AS primary_author
        FROM papers p
        LEFT JOIN author a ON p.author_id = a.id
        LEFT JOIN users u ON a.user_id = u.id
        WHERE p.editor_id = $editor_id
        ORDER BY p.submission_date DESC
    ");
}

function getCoAuthors_1($conn) {
    $result = $conn->query("SELECT paper_id, name AS co_author FROM paper_authors");
    $co_authors = [];
    while ($row = $result->fetch_assoc()) {
        $co_authors[$row['paper_id']][] = $row['co_author'];
    }
    return $co_authors;
}

function hasDecisionBadge_1($conn) {
    $sql = "
        SELECT et.paper_id
        FROM editor_tasks et
        INNER JOIN papers p ON et.paper_id = p.id
        WHERE et.task_type IN (1, 2, 3, 4)
          AND et.result = 'Processed for Next Level'
          AND p.status NOT IN ('Accepted (Final Decision)', 'Published')
        GROUP BY et.paper_id
        HAVING COUNT(DISTINCT et.task_type) = 4
    ";
    $res = $conn->query($sql);
    return ($res && $res->num_rows > 0);
}

function getNewEditorsCount_1($conn, $editor_id) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS new_editors_count 
        FROM editorial_team_members 
        WHERE team_id IN (SELECT team_id FROM editorial_team_members WHERE editor_id = ?) 
        AND is_new = 1
    ");
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['new_editors_count'];
}

function markEditorsAsOld_1($conn, $editor_id) {
    $sql = "
        UPDATE editorial_team_members etm
        JOIN (
            SELECT team_id 
            FROM (
                SELECT team_id 
                FROM editorial_team_members 
                WHERE editor_id = ?
            ) AS temp
        ) AS t ON etm.team_id = t.team_id
        SET etm.is_new = 0
        WHERE etm.is_new = 1
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
}

function getTaskAlerts_1($conn) {
    $query = "
        SELECT 
            t.task_type, 
            t.status, 
            t.deadline, 
            t.id, 
            p.title AS paper_title,
            u.first_name, 
            u.last_name,
            u.email
        FROM editor_tasks t
        JOIN papers p ON t.paper_id = p.id
        JOIN editors e ON t.editor_id = e.editor_id
        JOIN users u ON e.user_id = u.id
        WHERE t.status = 'Rejected' 
            OR (t.status = 'Accepted' AND t.deadline < CURDATE())
            OR (t.status = 'Pending' AND t.deadline < CURDATE())
            OR t.status = 'Completed'
        ORDER BY t.deadline DESC
    ";
    return $conn->query($query);
}

function checkAuthorEmailExists($conn, $email) {
    $id = null;
    $username = null;

    $stmt = $conn->prepare("SELECT id, username FROM author WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username);
        $stmt->fetch();
    }

    $stmt->close();

    if ($id && $username) {
        return ['id' => $id, 'username' => $username];
    } else {
        return false;
    }
}

// Function to fetch paper details
function fetchPaper($conn, $paper_id) {
    $paper_id = $conn->real_escape_string($paper_id);
    $result = $conn->query("SELECT title, journal_id FROM papers WHERE id = $paper_id");
    return $result->fetch_assoc();
}

// Function to fetch journal name
function fetchJournalName($conn, $journal_id) {
    $journal_id = $conn->real_escape_string($journal_id);
    $result = $conn->query("SELECT journal_name FROM journals WHERE id = $journal_id");
    $row = $result->fetch_assoc();
    return $row ? $row['journal_name'] : null;
}
// Function to link author to journal
function linkAuthorToJournal($conn, $author_id, $journal_id) {
    $author_id = $conn->real_escape_string($author_id);
    $journal_id = $conn->real_escape_string($journal_id);
    return $conn->query("INSERT INTO author_journal (author_id, journal_id) VALUES ($author_id, $journal_id)");
}

function fetchPaperDetails1($conn, $paper_id) {
    $query = "
        SELECT 
            p.title, 
            p.status, 
            p.submission_date, 
            p.updated_at,
            CONCAT(u.first_name, ' ', COALESCE(u.middle_name, ''), ' ', u.last_name) AS author_name
        FROM papers p
        LEFT JOIN author a ON p.author_id = a.id
        LEFT JOIN users u ON a.user_id = u.id
        WHERE p.id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $paper_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $paper = $result->fetch_assoc();
    $stmt->close();

    return $paper;
}

function fetchEditorTasksForPaper($conn, $paper_id) {
    $query = "
        SELECT task_type, status, result, response_date 
        FROM editor_tasks 
        WHERE paper_id = ? 
        AND task_type IN (1, 2, 3, 4)
        ORDER BY task_type ASC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $paper_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tasks = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $tasks;
}

function fetchReviewAssignments($conn, $paper_id) {
    $query = "
        SELECT 
            pa.reviewer_id, 
            CONCAT(u.first_name, ' ', COALESCE(u.middle_name, ''), ' ', u.last_name) AS reviewer_name, 
            pa.assigned_date, 
            pa.status AS review_status
        FROM paper_assignments pa
        LEFT JOIN reviewers r ON pa.reviewer_id = r.id
        LEFT JOIN users u ON r.user_id = u.id
        WHERE pa.paper_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $paper_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reviews = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $reviews;
}
function fetchAuthorPapersWithStatus($conn, $author_id) {
    $query = "
        SELECT 
            p.id AS paper_id,
            p.title AS paper_title, 
            j.journal_name AS journal_name, 
            p.submission_date,
            p.status AS current_status,
            GROUP_CONCAT(pa.status ORDER BY pa.updated_at SEPARATOR ' → ') AS status_timeline
        FROM papers p
        LEFT JOIN journals j ON p.journal_id = j.id
        LEFT JOIN paper_assignments pa ON p.id = pa.paper_id
        WHERE p.author_id = ?
        GROUP BY p.id
        ORDER BY p.submission_date DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $author_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $papers = [];
    $paper_ids = [];
    while ($row = $result->fetch_assoc()) {
        $papers[] = $row;
        $paper_ids[] = $row['paper_id'];
    }
    $stmt->close();

    return [$papers, $paper_ids];
}

function fetchEditorTasks($conn, $paper_ids) {
    if (empty($paper_ids)) return [];

    $placeholders = implode(',', array_fill(0, count($paper_ids), '?'));
    $types = str_repeat('i', count($paper_ids));
    $query = "SELECT paper_id, task_type, status FROM editor_tasks WHERE paper_id IN ($placeholders)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$paper_ids);
    $stmt->execute();
    $result = $stmt->get_result();

    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[$row['paper_id']][$row['task_type']] = $row['status'];
    }
    $stmt->close();

    return $tasks;
}
function getAuthorDetails_profile($conn, $author_id) {
    $query = $conn->prepare("
        SELECT a.id AS author_id, a.telephone, a.address, a.researcher_type, 
               u.first_name, u.last_name, u.email 
        FROM author a
        JOIN users u ON a.user_id = u.id
        WHERE a.id = ?
    ");
    $query->bind_param("i", $author_id);
    $query->execute();
    $result = $query->get_result();
    $author = $result->fetch_assoc();
    $query->close();
    return $author;
}
function getEditorUserId3($conn, $editor_id) {
    $query = $conn->prepare("SELECT user_id FROM editors WHERE editor_id = ?");
    $query->bind_param("i", $editor_id);
    $query->execute();
    $res = $query->get_result();
    $user_id = ($res->num_rows > 0) ? $res->fetch_assoc()['user_id'] : null;
    $query->close();
    return $user_id;
}

function updateUserDetails3($conn, $user_id, $first_name, $last_name, $email) {
    $updateUser = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?");
    $updateUser->bind_param("sssi", $first_name, $last_name, $email, $user_id);
    $updateUser->execute();
    $updateUser->close();
}

function updateEditorDetails3(
    $conn, $editor_id, $telephone, $address, $editor_type,
    $institution, $department, $position,
    $street_address, $city, $state, $zip_code, $country
) {
    $updateEditor = $conn->prepare("
        UPDATE editors 
        SET telephone = ?, address = ?, editor_type = ?, institution = ?, department = ?, position = ?, street_address = ?, city = ?, state = ?, zip_code = ?, country = ? 
        WHERE editor_id = ?
    ");
    $updateEditor->bind_param(
        "sssssssssssi",
        $telephone, $address, $editor_type,
        $institution, $department, $position,
        $street_address, $city, $state, $zip_code, $country,
        $editor_id
    );
    $updateEditor->execute();
    $updateEditor->close();
}

function insertTask2($conn, $paper_id, $editor_id, $result, $status, $task_type, $response_date) {
    $insert = $conn->prepare("INSERT INTO editor_tasks (paper_id, editor_id, result, status, task_type, response_date) VALUES (?, ?, ?, ?, ?, ?)");
    $insert->bind_param("iissis", $paper_id, $editor_id, $result, $status, $task_type, $response_date);
    $insert->execute();
    $insert->close();
}
function getEditorById($conn, $editor_id) {
    $stmt = $conn->prepare("
        SELECT e.*, u.first_name, u.last_name, u.email
        FROM editors e
        JOIN users u ON e.user_id = u.id
        WHERE e.editor_id = ?
    ");
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $editor = $result->fetch_assoc();
    $stmt->close();
    return $editor;
}

function getUserIdByEditorId1($conn, $editor_id) {
    $stmt = $conn->prepare("SELECT user_id FROM editors WHERE editor_id = ?");
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ? $row['user_id'] : null;
}

function updateUser($conn, $user_id, $first_name, $last_name, $email) {
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("sssi", $first_name, $last_name, $email, $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function updateEditorDetails($conn, $editor_id, $telephone, $address, $editor_type, $institution, $department, $position, $street_address, $city, $state, $zip_code, $country) {
    $stmt = $conn->prepare("
        UPDATE editors
        SET telephone = ?, address = ?, editor_type = ?, institution = ?, department = ?, position = ?, street_address = ?, city = ?, state = ?, zip_code = ?, country = ?
        WHERE editor_id = ?
    ");
    $stmt->bind_param("sssssssssssi", $telephone, $address, $editor_type, $institution, $department, $position, $street_address, $city, $state, $zip_code, $country, $editor_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}
function updateEditorTaskStatus3($conn, $paper_id, $editor_id, $task_type, $status) {
    $stmt = $conn->prepare("
        UPDATE editor_tasks
        SET status = ?, response_date = NOW()
        WHERE paper_id = ? AND editor_id = ? AND task_type = ?
    ");
    $stmt->bind_param("siii", $status, $paper_id, $editor_id, $task_type);
    return $stmt->execute();
}
function getReviewer($conn, $reviewer_id) {
    $stmt = $conn->prepare("
        SELECT 
            r.*, 
            u.first_name AS first_name, 
            u.last_name AS last_name, 
            u.email AS email 
        FROM reviewers r
        INNER JOIN users u ON r.user_id = u.id
        WHERE r.id = ?
    ");
    $stmt->bind_param("i", $reviewer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reviewer = $result->fetch_assoc();
    $stmt->close();
    return $reviewer;
}

function getDistinctPrimarySubjects($conn) {
    $result = $conn->query("SELECT DISTINCT primary_subject FROM journals");
    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row['primary_subject'];
    }
    return $subjects;
}
function updateUserDetails($conn, $user_id, $first_name, $last_name, $email) {
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("sssi", $first_name, $last_name, $email, $user_id);
    $stmt->execute();
    $stmt->close();
}

function updateReviewerDetails(
    $conn, $reviewer_id, $telephone, $personal_address, $reviewer_type,
    $institution, $department, $position, $street_address,
    $city, $state, $zip_code, $country
) {
    $stmt = $conn->prepare("UPDATE reviewers SET 
        telephone = ?, address = ?, reviewer_type = ?, 
        institution = ?, department = ?, position = ?, 
        street_address = ?, city = ?, state = ?, zip_code = ?, country = ?
        WHERE id = ?");
    $stmt->bind_param(
        "sssssssssssi",
        $telephone, $personal_address, $reviewer_type,
        $institution, $department, $position,
        $street_address, $city, $state, $zip_code, $country,
        $reviewer_id
    );
    $stmt->execute();
    $stmt->close();
}

function registerUser($first, $middle, $last, $email, $password) {
    global $conn;

    $first = $conn->real_escape_string($first);
    $middle = $conn->real_escape_string($middle);
    $last = $conn->real_escape_string($last);
    $email = $conn->real_escape_string($email);

    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        return ['status' => false, 'message' => 'Email already exists.'];
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (first_name, middle_name, last_name, email, password) VALUES ('$first', '$middle', '$last', '$email', '$hashedPassword')";
    if ($conn->query($sql)) {
        return ['status' => true, 'message' => 'Registration successful! Please log in.'];
    } else {
        return ['status' => false, 'message' => 'Registration failed: ' . $conn->error];
    }
}

function loginUser($email, $password) {
    global $conn;

    $email = $conn->real_escape_string($email);
    $result = $conn->query("SELECT id, first_name, password FROM users WHERE email = '$email'");
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        return ['status' => true, 'id' => $user['id'], 'first_name' => $user['first_name']];
    } else {
        return ['status' => false, 'message' => 'Email or password is incorrect.'];
    }
}

// function createTeam($team_name, $conn) {
//     $team_name = $conn->real_escape_string($team_name);
//     $conn->query("INSERT INTO editorial_teams (team_name) VALUES ('$team_name')");
//     return $conn->insert_id;
// }

// function addEditorToTeam($team_id, $editor_id, $role, $conn) {
//     $team_id = $conn->real_escape_string($team_id);
//     $editor_id = $conn->real_escape_string($editor_id);
//     $role = $conn->real_escape_string($role);
//     $conn->query("INSERT INTO editorial_team_members (team_id, editor_id, role) VALUES ($team_id, $editor_id, '$role')");
// }

// function checkIfEditorExistsInTeam($team_id, $editor_id, $conn) {
//     $team_id = $conn->real_escape_string($team_id);
//     $editor_id = $conn->real_escape_string($editor_id);
//     $result = $conn->query("SELECT id FROM editorial_team_members WHERE team_id = $team_id AND editor_id = $editor_id");
//     return $result->num_rows > 0;
// }

// function assignTeamToJournal($journal_id, $team_id, $conn) {
//     $journal_id = $conn->real_escape_string($journal_id);
//     $team_id = $conn->real_escape_string($team_id);
//     $conn->query("UPDATE journals SET editorial_team_id = $team_id WHERE id = $journal_id");

//     $result_chief = $conn->query("SELECT editor_id FROM editorial_team_members WHERE team_id = $team_id AND role = 'Chief Editor' LIMIT 1");
//     $chief_editor = $result_chief->fetch_assoc();

//     if ($chief_editor) {
//         $chief_editor_id = $chief_editor['editor_id'];
//         $conn->query("UPDATE papers SET editor_id = $chief_editor_id WHERE journal_id = $journal_id");
//     }
// }

// function deleteMember($id, $conn) {
//     $id = $conn->real_escape_string($id);
//     $conn->query("DELETE FROM editorial_team_members WHERE id = $id");
// }

// function deleteTeam($team_id, $conn) {
//     $team_id = $conn->real_escape_string($team_id);
//     $conn->query("DELETE FROM editorial_team_members WHERE team_id = $team_id");
//     $conn->query("UPDATE journals SET editorial_team_id = NULL WHERE editorial_team_id = $team_id");
//     $conn->query("DELETE FROM editorial_teams WHERE team_id = $team_id");
// }

// function fetchTeams($conn) {
//     return $conn->query("SELECT * FROM editorial_teams");
// }

// function fetchJournal($conn) {
//     return $conn->query("SELECT id, journal_name, editorial_team_id FROM journals");
// }

function fetchEditors($conn) {
    return $conn->query("SELECT editors.*, u.first_name, u.last_name, u.email FROM editors JOIN users u ON editors.user_id = u.id WHERE LOWER(editors.contract_status) = 'pending verification' OR LOWER(editors.registration_status) = 'approved'");
}

function getPaperStatus($conn, $paper_id) {
    $paper_id = $conn->real_escape_string($paper_id);
    return $conn->query("SELECT status FROM papers WHERE id = $paper_id");
}

function updatePaperDOI($conn, $paper_id, $doi) {
    $paper_id = $conn->real_escape_string($paper_id);
    $doi = $conn->real_escape_string($doi);
    return $conn->query("UPDATE papers SET doi = '$doi', updated_at = NOW() WHERE id = $paper_id");
}

function getAllEditorialTeams($conn) {
    $teams = [];
    $result = $conn->query("SELECT team_id, team_name FROM editorial_teams");
    while ($row = $result->fetch_assoc()) {
        $teams[] = $row;
    }
    return $teams;
}
// Function to create a new editorial team
function createEditorialTeam($conn, $team_name) {
    $stmt = $conn->prepare("INSERT INTO editorial_teams (team_name) VALUES (?)");
    $stmt->bind_param("s", $team_name);
    $stmt->execute();
    return $stmt->insert_id;
}

// Function to add a member to an editorial team
function addTeamMember($conn, $team_id, $editor_id, $role) {
    $stmt = $conn->prepare("INSERT INTO editorial_team_members (team_id, editor_id, role) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $team_id, $editor_id, $role);
    return $stmt->execute();
}

// Function to check if an editor is already in a team
function isEditorInTeam($conn, $team_id, $editor_id) {
    $stmt = $conn->prepare("SELECT id FROM editorial_team_members WHERE team_id = ? AND editor_id = ?");
    $stmt->bind_param("ii", $team_id, $editor_id);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

// Function to assign a team to a journal
function assignTeamToJournal($conn, $journal_id, $team_id) {
    $stmt = $conn->prepare("UPDATE journals SET editorial_team_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $team_id, $journal_id);
    return $stmt->execute();
}

// Function to get the chief editor of a team
function getChiefEditor($conn, $team_id) {
    $stmt = $conn->prepare("SELECT editor_id FROM editorial_team_members WHERE team_id = ? AND role = 'Chief Editor' LIMIT 1");
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to update papers with the chief editor's ID
function updatePapersWithChiefEditor($conn, $chief_editor_id, $journal_id) {
    $stmt = $conn->prepare("UPDATE papers SET editor_id = ? WHERE journal_id = ?");
    $stmt->bind_param("ii", $chief_editor_id, $journal_id);
    return $stmt->execute();
}

// Function to delete a member from a team
function deleteTeamMember($conn, $id) {
    return $conn->query("DELETE FROM editorial_team_members WHERE id = $id");
}

// Function to delete a team and its related data
function deleteTeam($conn, $team_id) {
    $conn->query("DELETE FROM editorial_team_members WHERE team_id = $team_id");
    $conn->query("UPDATE journals SET editorial_team_id = NULL WHERE editorial_team_id = $team_id");
    return $conn->query("DELETE FROM editorial_teams WHERE team_id = $team_id");
}

// Function to fetch all editorial teams
function getAllTeams($conn) {
    return $conn->query("SELECT * FROM editorial_teams");
}

// Function to fetch all journals
function getAllJournals($conn) {
    return $conn->query("SELECT id, journal_name, editorial_team_id FROM journals");
}

// Function to fetch all editors
function getAllEditor($conn) {
    return $conn->query("SELECT e.editor_id, u.first_name, u.last_name, u.email FROM editors e JOIN users u ON e.user_id = u.id WHERE e.contract_status = 'pending_verification'");
}

// Function to fetch journal team assignments
function getJournalTeamAssignments($conn) {
    return $conn->query("SELECT j.journal_name, t.team_name FROM journals j LEFT JOIN editorial_teams t ON j.editorial_team_id = t.team_id");
}

// Function to fetch team members
function getTeamMembers($conn, $team_id) {
    return $conn->query("SELECT etm.id, u.first_name, u.last_name, u.email, etm.role FROM editorial_team_members etm JOIN editors e ON e.editor_id = etm.editor_id JOIN users u ON e.user_id = u.id WHERE etm.team_id = $team_id");
}
function getUserProfile($user_id, $conn) {
    $user_id = $conn->real_escape_string($user_id);
    $result = $conn->query("SELECT first_name, middle_name, last_name, email FROM users WHERE id = $user_id");
    return $result->fetch_assoc();
}

function getUserById($id) {
    global $conn;
    $id = $conn->real_escape_string($id);
    $result = $conn->query("SELECT first_name, middle_name, last_name FROM users WHERE id = $id");
    return $result->fetch_assoc();
}

function getAdminUserByEmail($email) {
    global $conn;
    $email = $conn->real_escape_string($email);
    $result = $conn->query("SELECT * FROM users WHERE email = '$email' AND admin = 1");
    return $result->num_rows > 0 ? $result->fetch_assoc() : false;
}
// Function to check if a paper is already paid by the author
function isPaperPaid($conn, $paper_id, $author_id) {
    $stmt = $conn->prepare("SELECT 1 FROM payments WHERE paper_id = ? AND author_id = ? AND payment_status = 'Paid' LIMIT 1");
    $stmt->bind_param("ii", $paper_id, $author_id);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}
function approveReviewer($conn, $reviewer_id) {
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    return $conn->query("UPDATE reviewers SET registration_status = 'approved' WHERE id = $reviewer_id");
}

function getReviewerDetail($conn, $reviewer_id) {
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    $result = $conn->query("SELECT first_name, last_name, email FROM reviewers WHERE id = $reviewer_id");
    return $result->fetch_assoc();
}

function checkForDuplicates($conn, $journal_name, $journal_abbreviation, $issn = null) {
    $journal_name = $conn->real_escape_string($journal_name);
    $journal_abbreviation = $conn->real_escape_string($journal_abbreviation);
    $check_sql = "SELECT * FROM journals WHERE journal_name = '$journal_name' OR journal_abbreviation = '$journal_abbreviation'";
    if (!empty($issn)) {
        $issn = $conn->real_escape_string($issn);
        $check_sql .= " OR issn = '$issn'";
    }
    return $conn->query($check_sql);
}

function InsertJournal($conn, $journal_name, $journal_abbreviation, $editorial_team_id, $primary_subject, $secondary_subject, $description, $publisher, $issn, $country, $publication_frequency, $indexing_info, $scope, $review_process, $impact_factor, $citescore, $acceptance_rate, $access_type, $submission_status, $author_guidelines, $journal_image, $author_payment_required, $reader_payment_required, $author_apc_amount, $reader_fee_amount, $payment_currency, $payment_link, $payment_notes, $keywords) {
    $sql = "INSERT INTO journals (journal_name, journal_abbreviation, editorial_team_id, primary_subject, secondary_subject, description, publisher, issn, country, publication_frequency, indexing_info, scope, review_process, impact_factor, citescore, acceptance_rate, access_type, submission_status, author_guidelines, journal_image, author_payment_required, reader_payment_required, author_apc_amount, reader_fee_amount, payment_currency, payment_link, payment_notes, keywords)
    VALUES ('$journal_name', '$journal_abbreviation', '$editorial_team_id', '$primary_subject', '$secondary_subject', '$description', '$publisher', '$issn', '$country', '$publication_frequency', '$indexing_info', '$scope', '$review_process', $impact_factor, $citescore, $acceptance_rate, '$access_type', '$submission_status', '$author_guidelines', '$journal_image', $author_payment_required, $reader_payment_required, $author_apc_amount, $reader_fee_amount, '$payment_currency', '$payment_link', '$payment_notes', '$keywords')";
    return $conn->query($sql);
}

function getUserDetailsByRole($conn, $role_table, $id_column, $role_id) {
    $role_table = $conn->real_escape_string($role_table);
    $id_column = $conn->real_escape_string($id_column);
    $role_id = $conn->real_escape_string($role_id);
    $query = "SELECT u.first_name, u.last_name, u.email FROM $role_table r JOIN users u ON r.user_id = u.id WHERE r.$id_column = $role_id";
    return $conn->query($query);
}

function updateContractStatus($conn, $table, $id_column, $user_id) {
    $table = $conn->real_escape_string($table);
    $id_column = $conn->real_escape_string($id_column);
    $user_id = $conn->real_escape_string($user_id);
    return $conn->query("UPDATE $table SET contract_status = 'reupload' WHERE $id_column = $user_id");
}

function updatePaperStatus($conn, $paper_id) {
    $paper_id = $conn->real_escape_string($paper_id);
    $result = $conn->query("UPDATE papers SET status = 'Published', completed_date = NOW() WHERE id = $paper_id");
    return $result;
}

function getPublishedPapers($conn) {
    $sql = "SELECT p.title, p.doi, p.volume, p.issue, p.completed_date, au.first_name AS author_first_name, au.last_name AS author_last_name, ed.first_name AS editor_first_name, ed.last_name AS editor_last_name, rev.first_name AS reviewer_first_name, rev.last_name AS reviewer_last_name, j.journal_name FROM papers p JOIN author a ON p.author_id = a.id JOIN users au ON a.user_id = au.id JOIN editors e ON p.editor_id = e.editor_id JOIN users ed ON e.user_id = ed.id JOIN paper_assignments pa ON p.id = pa.paper_id JOIN reviewers r ON pa.reviewer_id = r.id JOIN users rev ON r.user_id = rev.id JOIN journals j ON p.journal_id = j.id WHERE p.status = 'Published' AND p.volume IS NOT NULL AND p.volume != '' AND p.issue IS NOT NULL AND p.issue != ''";
    return $conn->query($sql);
}

// function getReviewerDetails($conn, $reviewer_id) {
//     $reviewer_id = $conn->real_escape_string($reviewer_id);
//     return $conn->query("SELECT u.first_name, u.last_name, u.email FROM reviewers r JOIN users u ON r.user_id = u.user_id WHERE r.reviewer_id = $reviewer_id");
// }
function getEditorUserId($editor_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT user_id FROM editors WHERE editor_id = ?");
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $editor = $result->fetch_assoc();
    $stmt->close();
    return $editor ? $editor['user_id'] : null;
}

// Get user credentials (email, password) using user_id
function getEditorUserDetails($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT email, password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

// Update editor password
function updateEditorPassword($user_id, $new_password_hashed) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $new_password_hashed, $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function deleteReviewer($conn, $reviewer_id) {
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    return $conn->query("DELETE FROM reviewers WHERE id = $reviewer_id");
}

// function getEditorDetails($conn, $editor_id) {
//     $editor_id = $conn->real_escape_string($editor_id);
//     return $conn->query("SELECT first_name, last_name, email FROM editors WHERE editor_id = $editor_id");
// }
function getAuthorIdByUserId($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM author WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($author_id);
        $stmt->fetch();
        $stmt->close();
        return $author_id;
    }

    $stmt->close();
    return null;
}

// Check if Author is linked to a Journal
function AuthorLinkedToJournal($author_id, $journal_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT 1 FROM author_journal WHERE author_id = ? AND journal_id = ?");
    $stmt->bind_param("ii", $author_id, $journal_id);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}

function deleteEditor($conn, $editor_id) {
    $editor_id = $conn->real_escape_string($editor_id);
    return $conn->query("DELETE FROM editors WHERE editor_id = $editor_id");
}

function getReviewers() {
    global $conn;
    return $conn->query("SELECT r.id, u.first_name, u.last_name, u.email, r.address, r.contract_file, r.upload_date, r.contract_status FROM reviewers r JOIN users u ON r.user_id = u.id WHERE r.contract_status = 'pending_verification' OR r.contract_status = 'reupload'");
}

function insertAuthor($conn, $data) {
    $sql = "INSERT INTO author (user_id, title, telephone, degree, address, gender, researcher_type, position, institution, department, street_address, city, state, zip_code, country) VALUES ('{$data['user_id']}', '{$data['title']}', '{$data['telephone']}', '{$data['degree']}', '{$data['address']}', '{$data['gender']}', '{$data['researcher_type']}', '{$data['position']}', '{$data['institution']}', '{$data['department']}', '{$data['street_address']}', '{$data['city']}', '{$data['state']}', '{$data['zip_code']}', '{$data['country']}')";
    if ($conn->query($sql)) {
        return $conn->insert_id;
    } else {
        return false;
    }
}

function assignJournalsToAuthor($conn, $author_id, $journal_ids) {
    if (!is_array($journal_ids) || empty($journal_ids)) {
        return false;
    }
    foreach ($journal_ids as $journal_id) {
        $conn->query("INSERT INTO author_journal (author_id, journal_id) VALUES ($author_id, $journal_id)");
    }
    return true;
}

function verifyLogin($email, $password, $journal_id, $conn) {
    $email = $conn->real_escape_string($email);
    $journal_id = $conn->real_escape_string($journal_id);
    $result = $conn->query("SELECT a.id AS author_id, a.user_id, u.email, u.password AS password_hash, u.first_name, u.last_name FROM author a JOIN users u ON a.user_id = u.id JOIN author_journal aj ON a.id = aj.author_id WHERE u.email = '$email' AND aj.journal_id = $journal_id");
    $user = $result->fetch_assoc();
    if ($user && password_verify($password, $user['password_hash'])) {
        return $user;
    } else {
        return false;
    }
}

function getJournalDetails($journal_id) {
    global $conn;
    $journal_id = $conn->real_escape_string($journal_id);
    $result = $conn->query("SELECT journal_name, journal_image FROM journals WHERE id = $journal_id");
    return $result->fetch_assoc();
}
function fetchReviewHistory($conn, $editor_id, $task_type, $status_filter) {
    $query = $conn->prepare("
        SELECT p.id AS paper_id, p.title, p.journal_id, j.journal_name, et.status, et.result, et.deadline
        FROM editor_tasks et
        JOIN papers p ON et.paper_id = p.id
        JOIN journals j ON p.journal_id = j.id
        WHERE et.editor_id = ? AND et.task_type = ? AND et.status = ?
    ");
    $query->bind_param("iis", $editor_id, $task_type, $status_filter);
    $query->execute();
    return $query->get_result();
}
// Function to fetch initial review tasks
function fetchInitialReviewTasks($conn, $editor_id, $task_type) {
    $query = $conn->prepare("
        SELECT p.id AS paper_id, p.title, p.journal_id, j.journal_name, et.status, et.deadline
        FROM editor_tasks et
        JOIN papers p ON et.paper_id = p.id
        JOIN journals j ON p.journal_id = j.id
        WHERE et.editor_id = ? AND et.task_type = ?
    ");
    $query->bind_param("ii", $editor_id, $task_type);
    $query->execute();
    return $query->get_result();
}
// Function to fetch reviewer details
function fetchReviewerDetails($conn, $reviewer_id) {
    $reviewer_query = $conn->prepare("
        SELECT r.*, u.first_name, u.last_name
        FROM reviewers r
        JOIN users u ON r.user_id = u.id
        WHERE r.id = ?
    ");
    $reviewer_query->bind_param("i", $reviewer_id);
    $reviewer_query->execute();
    return $reviewer_query->get_result()->fetch_assoc();
}
// Function to check if a task is already assigned
function isTaskAssigned($conn, $paper_id, $task_type, $editor_id) {
    $checkQuery = "SELECT * FROM editor_tasks WHERE paper_id = ? AND task_type = ? AND editor_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("iii", $paper_id, $task_type, $editor_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    return $checkResult->num_rows > 0;
}

// Function to insert a new task
function insertTask($conn, $paper_id, $editor_id, $task_type, $assigned_by, $deadline) {
    $insertQuery = "INSERT INTO editor_tasks (paper_id, editor_id, task_type, assigned_by, deadline, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("iiiss", $paper_id, $editor_id, $task_type, $assigned_by, $deadline);
    return $stmt->execute();
}

// Function to fetch editor's email and paper title
function fetchEditorAndPaperDetails($conn, $paper_id, $editor_id) {
    $emailQuery = "
        SELECT u.email, u.first_name, u.last_name, p.title
        FROM editors e
        JOIN users u ON e.user_id = u.id
        JOIN papers p ON p.id = ?
        WHERE e.editor_id = ?
    ";
    $emailStmt = $conn->prepare($emailQuery);
    $emailStmt->bind_param("ii", $paper_id, $editor_id);
    $emailStmt->execute();
    $emailResult = $emailStmt->get_result();
    return $emailResult->fetch_assoc();
}// Get journal_id for a given paper_id
function getJournalIdByPaper($conn, $paper_id) {
    $journal_id = null; // Initialize it
    $stmt = $conn->prepare("SELECT journal_id FROM papers WHERE id = ?");
    $stmt->bind_param("i", $paper_id);
    $stmt->execute();
    $stmt->bind_result($journal_id);
    $stmt->fetch();
    $stmt->close();
    return $journal_id;
}
function getUserByEmail($conn, $email) {
    $stmt = $conn->prepare("SELECT id, first_name, last_name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getAuthorByUserId($conn, $user_id) {
    $stmt = $conn->prepare("SELECT id FROM author WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function updateAuthorLastLogin($conn, $author_id) {
    $stmt = $conn->prepare("UPDATE author SET last_login = NOW() WHERE id = ?");
    $stmt->bind_param("i", $author_id);
    return $stmt->execute();
}
// Fetch journal details by ID
function getJournalDetail($conn, $journal_id) {
    $stmt = $conn->prepare("SELECT * FROM journals WHERE id = ?");
    $stmt->bind_param("i", $journal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}
function getEditorDetail($editor_id) {
    global $conn;
    $query = $conn->prepare("SELECT u.email, u.password FROM editors e JOIN users u ON e.user_id = u.id WHERE e.editor_id = ?");
    $query->bind_param("i", $editor_id);
    $query->execute();
    $result = $query->get_result();
    $editor = $result->fetch_assoc();
    $query->close();
    return $editor;
}
function getReviewerDetails($reviewer_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT u.id AS user_id, u.password, u.email 
        FROM reviewers r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.id = ?
    ");
    $stmt->bind_param("i", $reviewer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $details = $result->fetch_assoc();
    $stmt->close();
    return $details;
}

// Update password in users table
function updateReviewerPassword($user_id, $hashed_password) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

// Get user_id from editor_id
function getUserIdByEditorId($editor_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT user_id FROM editors WHERE editor_id = ?");
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user_row = $user_result->fetch_assoc();
    $stmt->close();
    return $user_row['user_id'] ?? null;
}

// Update password for user
function updateUserPassword($user_id, $new_password) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $new_password, $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}
// Fetch author guidelines for a journal by ID
function getAuthorGuidelines($conn, $journal_id) {
    $stmt = $conn->prepare("SELECT author_guidelines FROM journals WHERE id = ?");
    $stmt->bind_param("i", $journal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['author_guidelines'];
    }
    return null;
}


// Update editor task status
function updateEditorTaskStatus($conn, $paper_id, $editor_id, $task_type, $status) {
    $stmt = $conn->prepare("
        UPDATE editor_tasks
        SET status = ?
        WHERE paper_id = ? AND editor_id = ? AND task_type = ?
    ");
    $stmt->bind_param("siii", $status, $paper_id, $editor_id, $task_type);
    $stmt->execute();
    $stmt->close();
}

// Function to get the count of reviewed papers for the current reviewer
function fetchReviewedCount($conn, $reviewer_id) {
    $reviewed_query = $conn->prepare("
        SELECT COUNT(*) AS reviewed_count
        FROM paper_assignments
        WHERE reviewer_id = ? AND status = 'Reviewed'
    ");
    $reviewed_query->bind_param("i", $reviewer_id);
    $reviewed_query->execute();
    $reviewed_result = $reviewed_query->get_result()->fetch_assoc();
    return $reviewed_result['reviewed_count'];
}

// Function to fetch paper title and journal name
function fetchPaperDetails($conn, $paper_id) {
    $paper_query = $conn->prepare("
        SELECT p.title, j.journal_name
        FROM papers p
        LEFT JOIN journals j ON p.journal_id = j.id
        WHERE p.id = ?
    ");
    $paper_query->bind_param("i", $paper_id);
    $paper_query->execute();
    return $paper_query->get_result()->fetch_assoc();
}

function fetchPrimarySubjectsAndJournals() {
    global $conn;
    $data = [];
    $subjectResult = $conn->query("SELECT DISTINCT primary_subject FROM journals");
    while ($row = $subjectResult->fetch_assoc()) {
        $primary_subject = $row['primary_subject'];
        $journalResult = $conn->query("SELECT id, journal_name FROM journals WHERE primary_subject = '$primary_subject'");
        $journals = [];
        while ($jRow = $journalResult->fetch_assoc()) {
            $journals[] = $jRow;
        }
        $data[] = ['primary_subject' => $primary_subject, 'journals' => $journals];
    }
    return $data;
}

function fetchPapersWithAuthorsAndEditors() {
    global $conn, $reviewer_id;
    $query = "SELECT p.id AS paper_id, p.title AS paper_title, ua.first_name AS author_first_name, ua.last_name AS author_last_name, j.journal_name, et.editor_id AS chief_editor_id, ue.first_name AS chief_first_name, ue.last_name AS chief_last_name FROM reviewer_journals rj INNER JOIN journals j ON rj.journal_id = j.id INNER JOIN papers p ON p.journal_id = j.id INNER JOIN author a ON p.author_id = a.id INNER JOIN users ua ON a.user_id = ua.id INNER JOIN editorial_team_members et ON j.editorial_team_id = et.team_id INNER JOIN editors ed ON et.editor_id = ed.editor_id INNER JOIN users ue ON ed.user_id = ue.id WHERE rj.reviewer_id = $reviewer_id AND et.role = 'Chief Editor'";
    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}
// Function to fetch editor's details
function fetchEditorDetails($conn, $editorId) {
    $stmt_editor = $conn->prepare("SELECT u.email, u.first_name, u.last_name, et.team_id, et.role
                                   FROM editors e
                                   JOIN users u ON e.user_id = u.id
                                   JOIN editorial_team_members et ON e.editor_id = et.editor_id
                                   WHERE e.editor_id = ?");
    $stmt_editor->bind_param("i", $editorId);
    $stmt_editor->execute();
    return $stmt_editor->get_result()->fetch_assoc();
}

// Function to fetch team details
function fetchTeamDetails($conn, $team_id) {
    $stmt_team = $conn->prepare("SELECT team_name FROM editorial_teams WHERE team_id = ?");
    $stmt_team->bind_param("i", $team_id);
    $stmt_team->execute();
    return $stmt_team->get_result()->fetch_assoc();
}


// Function to fetch papers associated with a Chief Editor
function fetchPapersForChiefEditor($conn, $editorId) {
    $papers = [];
    $stmt_papers = $conn->prepare("SELECT p.title, p.submission_date, p.journal_id
                                   FROM papers p
                                   WHERE p.editor_id = ?");
    $stmt_papers->bind_param("i", $editorId);
    $stmt_papers->execute();
    $papers_result = $stmt_papers->get_result();

    while ($paper = $papers_result->fetch_assoc()) {
        $journal = fetchJournalDetails($conn, $paper['journal_id']);
        $papers[] = [
            'journal_name' => $journal['journal_name'],
            'title' => $paper['title'],
            'date' => $paper['submission_date']
        ];
    }
    return $papers;
}
// Function to fetch total manuscripts
function fetchTotalManuscripts($conn) {
    $result = $conn->query("SELECT COUNT(*) AS total FROM papers");
    return $result->fetch_assoc()['total'];
}

// Function to fetch pending reviews
function fetchPendingReviews($conn) {
    $result = $conn->query("SELECT COUNT(*) AS pending FROM papers WHERE status = 'Under Review'");
    return $result->fetch_assoc()['pending'];
}

// Function to fetch accepted papers
function fetchAcceptedPapers($conn) {
    $result = $conn->query("SELECT COUNT(*) AS accepted FROM papers WHERE status = 'Published'");
    return $result->fetch_assoc()['accepted'];
}

// Function to fetch rejected papers
function fetchRejectedPapers($conn) {
    $result = $conn->query("SELECT COUNT(*) AS rejected FROM papers WHERE status = 'Rejected (Post-Review)'");
    return $result->fetch_assoc()['rejected'];
}

// Function to fetch total reviewers
function fetchTotalReviewers($conn) {
    $result = $conn->query("SELECT COUNT(*) AS total FROM reviewers");
    return $result->fetch_assoc()['total'];
}

// Function to fetch active reviewers
function fetchActiveReviewers($conn) {
    $result = $conn->query("SELECT COUNT(DISTINCT reviewer_id) AS active FROM paper_assignments WHERE status = 'In-Review'");
    return $result->fetch_assoc()['active'];
}

// Function to fetch completed reviews
function fetchCompletedReviews($conn) {
    return $conn->query("SELECT reviewer_id, COUNT(*) AS completed FROM paper_assignments WHERE status = 'Completed' GROUP BY reviewer_id");
}

// Function to fetch top authors
function fetchTopAuthors($conn) {
    return $conn->query("
        SELECT u.first_name, u.last_name, COUNT(*) AS total_papers
        FROM papers p
        JOIN author a ON p.author_id = a.id
        JOIN users u ON a.user_id = u.id
        GROUP BY a.user_id
        ORDER BY total_papers DESC
        LIMIT 5
    ");
}

// Function to fetch journal data
function fetchJournalData($conn) {
    return $conn->query("
        SELECT j.journal_name,
            SUM(CASE WHEN p.status = 'Accepted (Final Decision)' THEN 1 ELSE 0 END) AS accepted,
            SUM(CASE WHEN p.status = 'Rejected (Post-Review)' THEN 1 ELSE 0 END) AS rejected
        FROM papers p
        JOIN journals j ON p.journal_id = j.id
        GROUP BY j.journal_name
    ");
}
// Function to get logged-in user details
function getLoggedInUserDetails($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT first_name, middle_name, last_name, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Function to check if user is already registered as a reviewer
function isUserRegisteredAsReviewer($conn, $user_id) {
    $stmt = $conn->prepare("SELECT id FROM reviewers WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

// Function to check if user is already registered as an editor
function isUserRegisteredAsEditor($conn, $user_id) {
    $stmt = $conn->prepare("SELECT editor_id FROM editors WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

// Function to check if user is already registered as an author
function isUserRegisteredAsAuthor($conn, $user_id) {
    $stmt = $conn->prepare("SELECT id FROM author WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

// Function to check if author is linked to a journal
function isAuthorLinkedToJournal($conn, $author_id, $journal_id) {
    $stmt = $conn->prepare("SELECT * FROM author_journal WHERE author_id = ? AND journal_id = ?");
    $stmt->bind_param("ii", $author_id, $journal_id);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}
// Function to fetch journal details
function fetchJournalDetails($conn, $journal_id) {
    $stmt_journal = $conn->prepare("SELECT journal_name FROM journals WHERE id = ?");
    $stmt_journal->bind_param("i", $journal_id);
    $stmt_journal->execute();
    return $stmt_journal->get_result()->fetch_assoc();
}

// Function to fetch scope details
function fetchScopeDetails($conn, $journal_id) {
    $query = "SELECT scope FROM journals WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $journal_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
// Function to fetch journals for a team
function fetchJournalsForTeam($conn, $team_id) {
    $journals = [];
    $stmt_journals = $conn->prepare("SELECT journal_name FROM journals WHERE editorial_team_id = ?");
    $stmt_journals->bind_param("i", $team_id);
    $stmt_journals->execute();
    $journals_result = $stmt_journals->get_result();

    while ($journal = $journals_result->fetch_assoc()) {
        $journals[] = $journal;
    }
    return $journals;
}
function updatePaperDetails($conn, $paper_id, $doi, $volume, $issue, $year) {
    $update = $conn->prepare("UPDATE papers SET doi = ?, volume = ?, issue = ?, year = ?, updated_at = NOW() WHERE id = ?");
    $update->bind_param("ssssi", $doi, $volume, $issue, $year, $paper_id);
    $result = $update->execute();
    $update->close();
    return $result;
}
function updateJournalField($conn, $id, $field, $value) {
    $allowed_fields = ["access_type", "submission_status"];
    if (!in_array($field, $allowed_fields)) {
        return false;
    }

    $sql = "UPDATE journals SET $field = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $value, $id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}
function updateContractStatusRev($conn, $reviewer_id) {
    $update_query = $conn->prepare("UPDATE reviewers SET contract_status = 'signed' WHERE id = ?");
    $update_query->bind_param("i", $reviewer_id);
    return $update_query->execute();
}
// function updateReviewerStatus($conn, $reviewer_id) {
//     $query = "UPDATE reviewers SET contract_status = 'signed', registration_status = 'approved' WHERE id = ?";
//     $stmt = $conn->prepare($query);
//     $stmt->bind_param("i", $reviewer_id);
//     return $stmt->execute();
// }

// Function to fetch user details
function fetchUserDetails($conn, $reviewer_id) {
    $query = $conn->prepare("
        SELECT u.first_name, u.last_name, u.email
        FROM reviewers r
        JOIN users u ON r.user_id = u.id
        WHERE r.id = ?
    ");
    $query->bind_param("i", $reviewer_id);
    $query->execute();
    return $query->get_result()->fetch_assoc();
}
// Function to update contract and registration status
function updateReviewerStatus($conn, $reviewer_id) {
    $query = "UPDATE reviewers SET contract_status = 'signed', registration_status = 'approved' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $reviewer_id);
    return $stmt->execute();
}
// function insertReviewer($pdo, $data) {
//     $fields = ['username', 'password', 'title', 'first_name', 'middle_name', 'last_name', 'email', 'telephone', 'degree', 'personal_address', 'gender', 'reviewer_type', 'position', 'institution', 'department', 'street_address', 'city', 'state', 'zip_code', 'country', 'experience', 'review_frequency', 'payment_type', 'account_holder_name', 'bank_name', 'account_number', 'ifsc_code', 'branch_name', 'bank_country'];
//     $values = [];
//     foreach ($fields as $field) {
//         $values[$field] = isset($data[$field]) ? $data[$field] : null;
//     }
//     $sql = "INSERT INTO reviewers (" . implode(',', $fields) . ") VALUES ('" . implode("','", $values) . "')";
//     return $pdo->query($sql) ? $pdo->lastInsertId() : false;
// }

// function assignReviewerToJournal($pdo, $reviewer_id, $journal_id) {
//     return $pdo->query("INSERT INTO reviewer_journals (reviewer_id, journal_id) VALUES ($reviewer_id, $journal_id)");
// }

function insertEditor($data) {
    global $conn;
    $sql = "INSERT INTO editors (user_id, title, telephone, degree, address, gender, editor_type, editor_board, editor_experience, editor_payment_type, cv_path, paper_name, co_author, position, institution, department, street_address, city, state, zip_code, country, editor_account_holder, editor_bank_name, editor_account_number, editor_ifsc, editor_branch_name, editor_bank_country) VALUES ('{$data['user_id']}', '{$data['title']}', '{$data['telephone']}', '{$data['degree']}', '{$data['address']}', '{$data['gender']}', '{$data['editor_type']}', '{$data['editor_board']}', '{$data['editor_experience']}', '{$data['editor_payment_type']}', '{$data['cv_path']}', '{$data['paper_name']}', '{$data['co_author']}', '{$data['position']}', '{$data['institution']}', '{$data['department']}', '{$data['street_address']}', '{$data['city']}', '{$data['state']}', '{$data['zip_code']}', '{$data['country']}', '{$data['editor_account_holder']}', '{$data['editor_bank_name']}', '{$data['editor_account_number']}', '{$data['editor_ifsc']}', '{$data['editor_branch_name']}', '{$data['editor_bank_country']}')";
    return $conn->query($sql);
}

function assignEditorToPaper($conn, $editor_id, $paper_id) {
    $editor_id = $conn->real_escape_string($editor_id);
    $paper_id = $conn->real_escape_string($paper_id);
    return $conn->query("UPDATE papers SET editor_id = $editor_id WHERE id = $paper_id");
}

function isEditorAssigned($conn, $editor_id) {
    $editor_id = $conn->real_escape_string($editor_id);
    $result = $conn->query("SELECT * FROM editorial_team_members WHERE editor_id = $editor_id");
    return $result->num_rows > 0;
}

function getPendingEditorContracts($conn) {
    return $conn->query("SELECT e.editor_id, u.first_name, u.last_name, u.email, e.address, e.contract_file, e.upload_date, e.contract_status FROM editors e JOIN users u ON e.user_id = u.id WHERE e.contract_status = 'pending_verification' OR e.contract_status = 'reupload'");
}

function getTotalEditorCount($conn) {
    $result = $conn->query("SELECT COUNT(*) as count FROM editors");
    $row = $result->fetch_assoc();
    return $row['count'];
}

function insertPaper($conn, $journal_id, $author_id, $title, $abstract, $keywords, $paper_file_path, $file_hash, $cover_letter_path, $copyright_agreement_path, $supplementary_files_str) {
    $sql = "INSERT INTO papers (journal_id, author_id, title, abstract, keywords, file_path, file_hash, cover_letter_path, copyright_agreement_path, supplementary_files_path) VALUES ($journal_id, $author_id, '$title', '$abstract', '$keywords', '$paper_file_path', '$file_hash', '$cover_letter_path', '$copyright_agreement_path', '$supplementary_files_str')";
    if ($conn->query($sql)) {
        return $conn->insert_id;
    } else {
        return false;
    }
}

function insertPaperAuthor($conn, $paper_id, $author) {
    $sql = "INSERT INTO paper_authors (paper_id, name, email, affiliation_type) VALUES ($paper_id, '{$author['name']}', '{$author['email']}', '{$author['affiliation_type']}')";
    return $conn->query($sql);
}

function isDuplicatePaper($conn, $file_hash) {
    $result = $conn->query("SELECT id FROM papers WHERE file_hash = '$file_hash'");
    return $result->num_rows > 0;
}

function validateReviewer($conn, $email, $password) {
    $email = $conn->real_escape_string($email);
    $result = $conn->query("SELECT id, password FROM users WHERE email = '$email'");
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $reviewerResult = $conn->query("SELECT id, registration_status FROM reviewers WHERE user_id = {$user['id']}");
            if ($reviewerResult->num_rows === 1) {
                $reviewer = $reviewerResult->fetch_assoc();
                switch ($reviewer['registration_status']) {
                    case 'approved': return $reviewer['id'];
                    case 'pending': return "Your application is under review.";
                    case 'rejected': return "Your application has been rejected.";
                    default: return "Unknown registration status.";
                }
            } else {
                return "Reviewer record not found.";
            }
        } else {
            return "Invalid email or password.";
        }
    } else {
        return "User not found.";
    }
}

function getReviewerJournals($conn, $reviewer_id) {
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    $result = $conn->query("SELECT j.id, j.journal_name, j.primary_subject FROM journals j INNER JOIN reviewer_journals rj ON j.id = rj.journal_id WHERE rj.reviewer_id = $reviewer_id");
    $journals = [];
    while ($row = $result->fetch_assoc()) {
        $journals[] = $row;
    }
    return $journals;
}

function getReviewerNotifications($conn, $reviewer_id) {
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    $result = $conn->query("SELECT message FROM notifications WHERE reviewer_id = $reviewer_id ORDER BY created_at DESC");
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row['message'];
    }
    return $notifications;
}

function getReviewerCount($conn, $status) {
    $status = $conn->real_escape_string($status);
    $result = $conn->query("SELECT COUNT(*) AS total FROM reviewers WHERE registration_status = '$status'");
    $row = $result->fetch_assoc();
    return $row['total'];
}
function getPendingReviewerApplications($conn) {
    $result = $conn->query("SELECT COUNT(*) AS total FROM reviewers WHERE registration_status = 'pending'");
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Function to get the count of pending editor applications
function getPendingEditorApplications($conn) {
    $result = $conn->query("SELECT COUNT(*) AS total FROM editors WHERE registration_status = 'pending'");
    $row = $result->fetch_assoc();
    return $row['total'];
}
function fetchPapersByAuthorId($conn, $author_id) {
    $author_id = $conn->real_escape_string($author_id);
    $result = $conn->query("SELECT * FROM papers WHERE author_id = $author_id");
    $papers = [];
    while ($row = $result->fetch_assoc()) {
        // Replace NULL values with empty strings
        foreach ($row as $key => $value) {
            $row[$key] = $value ?? '';
        }
        $papers[] = $row;
    }
    return $papers;
}
function fetchPaperDetailsWithReviewInfo($conn, $author_id) {
    $author_id = $conn->real_escape_string($author_id);
    $query = "
        SELECT
            p.title AS paper_title,
            j.journal_name AS journal_name,
            p.status AS paper_status,
            j.review_process AS review_process,
            COALESCE(
                GROUP_CONCAT(CONCAT(u.first_name, ' ', u.last_name) SEPARATOR ', '),
                'Not Assigned Yet'
            ) AS reviewers,
            j.editorial_team_id
        FROM papers p
        LEFT JOIN journals j ON p.journal_id = j.id
        LEFT JOIN paper_assignments pa ON p.id = pa.paper_id
        LEFT JOIN reviewers r ON pa.reviewer_id = r.id
        LEFT JOIN users u ON r.user_id = u.id
        WHERE p.author_id = $author_id
        GROUP BY p.id;
    ";
    return $conn->query($query);
}
function fetchEditorAndTaskInfo($conn, $task_id) {
    $task_id = $conn->real_escape_string($task_id);
    $query = "
        SELECT e.email, e.first_name, t.task_type, p.title AS paper_title, t.deadline
        FROM editor_tasks t
        JOIN editors e ON t.editor_id = e.editor_id
        JOIN papers p ON t.paper_id = p.id
        WHERE t.id = $task_id
    ";
    $result = $conn->query($query);
    return $result->fetch_assoc();
}
function fetchReviewerFeedbackRevisions($conn, $author_id) {
    $author_id = $conn->real_escape_string($author_id);
    $query = "SELECT f.*, p.title, p.journal_id, p.file_path, p.status, j.journal_name AS journal_name
              FROM feedback f
              JOIN papers p ON f.paper_id = p.id
              JOIN journals j ON p.journal_id = j.id
              JOIN paper_assignments pa ON p.id = pa.paper_id
              WHERE f.author_id = $author_id AND pa.status = 'Revision Requested' AND p.status = 'Revision Requested'";
    return $conn->query($query);
}

// Function to fetch editor task-based revision requests
function fetchEditorTaskRevisions($conn, $author_id) {
    $author_id = $conn->real_escape_string($author_id);
    $query = "SELECT et.*, p.title, p.journal_id, p.file_path, p.status, j.journal_name AS journal_name, u.first_name, u.last_name
              FROM editor_tasks et
              JOIN papers p ON et.paper_id = p.id
              JOIN journals j ON p.journal_id = j.id
              JOIN editors e ON et.editor_id = e.editor_id
              JOIN users u ON e.user_id = u.id
              WHERE p.status = 'Revision Requested'
                AND et.result = 'Revision Request'
                AND et.task_type IN (2, 4)
                AND p.author_id = $author_id";
    return $conn->query($query);
}

// Function to fetch feedback based on task type
function fetchFeedbackByTaskType($conn, $task_type, $paper_id) {
    $task_type = $conn->real_escape_string($task_type);
    $paper_id = $conn->real_escape_string($paper_id);

    if ($task_type == 2) {
        $query = "SELECT feedback FROM plagiarism_reports WHERE paper_id = $paper_id";
    } elseif ($task_type == 4) {
        $query = "SELECT feedback FROM editor_tasks WHERE paper_id = $paper_id";
    } else {
        return "No relevant feedback found.";
    }

    $result = $conn->query($query);
    return $result->num_rows > 0 ? $result->fetch_assoc()['feedback'] : "No feedback available.";
}
// Function to fetch revised submitted papers
function fetchRevisedSubmittedPapers($conn, $editor_id) {
    $query = "
        SELECT DISTINCT
            p.id AS paper_id,
            p.title,
            CONCAT(u.first_name, ' ', u.last_name) AS author_name
        FROM papers p
        JOIN editor_tasks et ON p.id = et.paper_id
        JOIN author a ON p.author_id = a.id
        JOIN users u ON a.user_id = u.id
        WHERE p.status = 'Revised Submitted'
          AND et.result = 'Revised Submitted'
          AND et.task_type IN (2, 4)
    ";
    return $conn->query($query);
}
// Function to fetch distinct journal names
function fetchDistinctJournalNames($conn) {
    return $conn->query("SELECT DISTINCT journal_name FROM journals ORDER BY journal_name ASC");
}
// Function to fetch review history
function ReviewHistory($conn, $reviewer_id) {
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    $query = "SELECT
        p.title,
        p.status AS paper_status,
        pa.status AS review_status,
        pa.assigned_date,
        pa.completed_date,
        f.review_date,
        f.journal_name,
        f.feedback
    FROM paper_assignments pa
    JOIN papers p ON pa.paper_id = p.id
    LEFT JOIN feedback f ON f.paper_id = p.id AND f.reviewer_id = pa.reviewer_id
    WHERE pa.reviewer_id = $reviewer_id";

    return $conn->query($query);
}
// Function to fetch feedback and paper details
function fetchFeedbackAndPaperDetails($conn, $editor_id) {
    $editor_id = $conn->real_escape_string($editor_id);
    $query = "
        SELECT f.*,
            ru.first_name AS reviewer_first, ru.last_name AS reviewer_last,
            au.first_name AS author_first, au.last_name AS author_last,
            p.title, p.id AS paper_id, j.journal_name
        FROM feedback f
        JOIN reviewers r ON f.reviewer_id = r.id
        JOIN users ru ON r.user_id = ru.id
        JOIN author a ON f.author_id = a.id
        JOIN users au ON a.user_id = au.id
        JOIN papers p ON f.paper_id = p.id
        JOIN journals j ON p.journal_id = j.id
        WHERE p.editor_id = $editor_id
        ORDER BY f.review_date DESC
    ";
    return $conn->query($query);
}
// Function to fetch resubmitted papers
function fetchResubmittedPapers($conn, $reviewer_id) {
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    $query = "SELECT
        p.id AS paper_id,
        p.title,
        p.status AS paper_status,
        p.file_path,
        p.cover_letter_path,
        p.supplementary_files_path,
        p.submission_date,
        j.journal_name AS journal_name,
        pa.status AS assignment_status,
        pa.assigned_date
    FROM paper_assignments pa
    JOIN papers p ON pa.paper_id = p.id
    JOIN journals j ON p.journal_id = j.id
    WHERE pa.reviewer_id = $reviewer_id AND p.status = 'Revised Submitted' AND pa.status = 'Revised Submitted'";

    return $conn->query($query);
}
function ReviewerPassword($conn, $new_password, $email) {
    $new_password = $conn->real_escape_string($new_password);
    $email = $conn->real_escape_string($email);
    $query = "UPDATE reviewers SET password='$new_password' WHERE email='$email'";
    return $conn->query($query);
}
// Function to update user password
function UserPassword($conn, $passwordHash, $email) {
    $passwordHash = $conn->real_escape_string($passwordHash);
    $email = $conn->real_escape_string($email);
    $query = "UPDATE users SET password = '$passwordHash' WHERE email = '$email'";
    return $conn->query($query);
}
// Function to check if a request already exists
function checkExistingRequest($conn, $reviewer_id, $journal_id) {
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    $journal_id = $conn->real_escape_string($journal_id);
    $query = "SELECT id FROM reviewer_requests WHERE reviewer_id = $reviewer_id AND journal_id = $journal_id";
    $result = $conn->query($query);
    return $result->num_rows > 0;
}
// Function to fetch reminders for paper reviews
function fetchReviewReminders($conn, $two_days_before, $one_day_before, $today) {
    $two_days_before = $conn->real_escape_string($two_days_before);
    $one_day_before = $conn->real_escape_string($one_day_before);
    $today = $conn->real_escape_string($today);

    $query = "SELECT pa.reviewer_id, p.title, pa.deadline, r.email
              FROM paper_assignments pa
              JOIN papers p ON pa.paper_id = p.id
              JOIN reviewers r ON pa.reviewer_id = r.id
              WHERE pa.status = 'Under Review' AND (pa.deadline = '$two_days_before' OR pa.deadline = '$one_day_before' OR pa.deadline < '$today')";

    return $conn->query($query);
}
// Function to get task type
function getTaskType($conn, $task_id) {
    $task_id = $conn->real_escape_string($task_id);
    $query = "SELECT task_type FROM editor_tasks WHERE paper_id = $task_id";
    $result = $conn->query($query);
    return $result->fetch_assoc()['task_type'];
}

// Function to update paper status and comment
function updatePaperStatusAndComment($conn, $status, $comment, $paper_id) {
    $status = $conn->real_escape_string($status);
    $comment = $conn->real_escape_string($comment);
    $paper_id = $conn->real_escape_string($paper_id);
    $query = "UPDATE papers SET status = '$status', comments = '$comment' WHERE id = $paper_id";
    return $conn->query($query);
}

// Function to update editor task result
function updateEditorResult($conn, $task_id) {
    $task_id = $conn->real_escape_string($task_id);
    $query = "UPDATE editor_tasks SET result = 'Rejected' WHERE paper_id = $task_id";
    return $conn->query($query);
}
// Function to fetch papers with DOI and "Published" status
function fetchPublishedPapersWithDOI($conn, $author_id) {
    $author_id = $conn->real_escape_string($author_id);
    $query = "SELECT p.id AS paper_id,
                     p.title,
                     p.doi,
                     p.status,
                     j.journal_name,
                     pa.name AS co_author_name,
                     pa.email AS co_author_email,
                     pa.affiliation_type
              FROM papers p
              JOIN journals j ON p.journal_id = j.id
              LEFT JOIN paper_authors pa ON p.id = pa.paper_id
              WHERE p.doi IS NOT NULL AND p.status = 'Published' AND p.author_id = $author_id";

    return $conn->query($query);
}
// Function to get team ID
function getTeamId($conn, $editor_id) {
    $editor_id = $conn->real_escape_string($editor_id);
    $query = "SELECT team_id FROM editorial_team_members WHERE editor_id = $editor_id";
    $result = $conn->query($query);
    return $result->fetch_assoc()['team_id'] ?? null;
}

// Function to get team name
function getTeamName($conn, $team_id) {
    $team_id = $conn->real_escape_string($team_id);
    $query = "SELECT team_name FROM editorial_teams WHERE team_id = $team_id";
    $result = $conn->query($query);
    return $result->fetch_assoc()['team_name'] ?? '';
}

// Function to fetch published papers by team
function fetchPublishedPapersByTeam($conn, $team_id) {
    $team_id = $conn->real_escape_string($team_id);
    $query = "
        SELECT
            p.title,
            j.journal_name AS journal_name,
            p.volume,
            p.issue,
            p.doi
        FROM papers p
        JOIN journals j ON p.journal_id = j.id
        WHERE p.status = 'Published'
        AND j.editorial_team_id = $team_id
    ";
    return $conn->query($query);
}
// Function to insert reviewer data
function insertReviewer($conn, $data) {
    $query = "INSERT INTO reviewers (
        user_id, title, telephone, degree, address, gender, reviewer_type, position, institution, department,
        street_address, city, state, zip_code, country, experience, review_frequency,
        payment_type, account_holder_name, bank_name, account_number, ifsc_code,
        branch_name, bank_country, cv_path
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "ssssssssssssssiiissssssss",
        $data['user_id'], $data['title'], $data['telephone'],
        $data['degree'], $data['address'], $data['gender'], $data['reviewer_type'],
        $data['position'], $data['institution'], $data['department'],
        $data['street_address'], $data['city'], $data['state'], $data['zip_code'],
        $data['country'], $data['experience'], $data['review_frequency'],
        $data['payment_type'], $data['account_holder_name'], $data['bank_name'],
        $data['account_number'], $data['ifsc_code'], $data['branch_name'],
        $data['bank_country'], $data['cv_path']
    );

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    $reviewer_id = $conn->insert_id;
    $stmt->close();
    return $reviewer_id;
}

// Function to assign journals to a reviewer
function assignJournalsToReviewer($conn, $reviewer_id, $journal_ids) {
    $query = "INSERT INTO reviewer_journals (reviewer_id, journal_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Prepare failed (journals): " . $conn->error);
    }

    foreach ($journal_ids as $journal_id) {
        $stmt->bind_param("ii", $reviewer_id, $journal_id);
        $stmt->execute();
    }
    $stmt->close();
}

// Function to fetch author details
function fetchAuthorDetails($conn, $paper_id) {
    $paper_id = $conn->real_escape_string($paper_id);
    $query = "SELECT a.email, a.first_name, a.last_name, p.title FROM author a JOIN papers p ON a.id = p.author_id WHERE p.id = $paper_id";
    $result = $conn->query($query);
    return $result->fetch_assoc();
}

function updateReviewerLastLogin($conn, $reviewerId) {
    $query = "UPDATE reviewers SET last_login = NOW() WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $reviewerId);
        $stmt->execute();
        $stmt->close();
    }
}
function getEditorialTeamId(mysqli $conn, int $journal_id): ?int {
    $query = "
        SELECT et.team_id
        FROM journals j
        LEFT JOIN editorial_teams et ON j.editorial_team_id = et.team_id
        WHERE j.id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $journal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['team_id'] ?? null;
}

function getChiefEditorInfo(mysqli $conn, int $team_id): ?array {
    $query = "
        SELECT u.first_name, u.last_name, u.email
        FROM editorial_team_members etm
        LEFT JOIN editors e ON etm.editor_id = e.editor_id
        LEFT JOIN users u ON e.user_id = u.id
        WHERE etm.team_id = ? AND etm.role = 'Chief Editor'
        LIMIT 1
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ?: null;
}
function updatePaperFile(mysqli $conn, int $paper_id, string $file_path): bool {
    $stmt = $conn->prepare("UPDATE papers SET file_path = ? WHERE id = ?");
    $stmt->bind_param("si", $file_path, $paper_id);
    return $stmt->execute();
}

function updateEditorTaskWithFeedback(mysqli $conn, int $paper_id, int $task_type, string $result, string $feedback): bool {
    $stmt = $conn->prepare("
        UPDATE editor_tasks 
        SET result = ?, feedback = ?, response_date = NOW() 
        WHERE paper_id = ? AND task_type = ?
    ");
    $stmt->bind_param("ssii", $result, $feedback, $paper_id, $task_type);
    return $stmt->execute();
}

function updateEditorTaskStatusOnly(mysqli $conn, int $paper_id, int $task_type, string $result): bool {
    $stmt = $conn->prepare("
        UPDATE editor_tasks 
        SET result = ?, status = 'Completed', response_date = NOW() 
        WHERE paper_id = ? AND task_type = ?
    ");
    $stmt->bind_param("sii", $result, $paper_id, $task_type);
    return $stmt->execute();
}
function getEditorByEmail(mysqli $conn, string $email) {
    $stmt = $conn->prepare("SELECT u.id, u.email, u.password, e.editor_id, e.registration_status 
                            FROM users u 
                            JOIN editors e ON e.user_id = u.id 
                            WHERE u.email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $db_email, $hashed_password, $editor_id, $registration_status);
        $stmt->fetch();
        $stmt->close();
        return [
            'user_id' => $user_id,
            'email' => $db_email,
            'hashed_password' => $hashed_password,
            'editor_id' => $editor_id,
            'registration_status' => $registration_status
        ];
    }

    $stmt->close();
    return null;
}

function updateEditorLastLogin(mysqli $conn, int $editor_id): void {
    $stmt = $conn->prepare("UPDATE editors SET last_login = NOW() WHERE editor_id = ?");
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $stmt->close();
}

function getEditorRole(mysqli $conn, int $editor_id): string {
    $stmt = $conn->prepare("SELECT role FROM editorial_team_members WHERE editor_id = ?");
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $role = '';
    if ($result && $row = $result->fetch_assoc()) {
        $role = strtolower(trim($row['role']));
    }
    $stmt->close();
    return $role;
}
function sendEditorMessages(mysqli $conn, int $sender_id, array $recipients, string $subject, string $message): void {
    $stmt = $conn->prepare("
        INSERT INTO editor_reviewer_messages 
        (sender_id, recipient_id, recipient_role, subject, message, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    foreach ($recipients as $recipient_input) {
        list($recipient_role, $recipient_id) = explode('_', $recipient_input);
        $recipient_id = intval($recipient_id);
        $stmt->bind_param("iisss", $sender_id, $recipient_id, $recipient_role, $subject, $message);
        $stmt->execute();
    }

    $stmt->close();
}
function sendReviewerToEditorMessage(mysqli $conn, int $reviewer_id, int $editor_id, int $paper_id, string $subject, string $message): bool {
    $stmt = $conn->prepare("
        INSERT INTO messages 
        (sender_id, sender_role, recipient_id, paper_id, subject, message, created_at) 
        VALUES (?, 'reviewer', ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("iiiss", $reviewer_id, $editor_id, $paper_id, $subject, $message);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}
function checkReviewerAlreadyAssigned($conn, int $paper_id, int $reviewer_id): bool {
    $stmt = $conn->prepare("SELECT 1 FROM paper_assignments WHERE paper_id = ? AND reviewer_id = ?");
    $stmt->bind_param("ii", $paper_id, $reviewer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function assignPaperToReviewer($conn, int $paper_id, int $reviewer_id, string $deadline): bool {
    $stmt = $conn->prepare("INSERT INTO paper_assignments (reviewer_id, paper_id, deadline, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("iis", $reviewer_id, $paper_id, $deadline);
    return $stmt->execute();
}

function getPlagiarismTasks($conn, int $editor_id): mysqli_result|false {
    $task_type = 2; // Plagiarism check task type
    $stmt = $conn->prepare("
        SELECT p.id AS paper_id, p.title, j.journal_name, et.status
        FROM editor_tasks et
        JOIN papers p ON et.paper_id = p.id
        JOIN journals j ON p.journal_id = j.id
        WHERE et.editor_id = ? AND et.task_type = ?
    ");
    $stmt->bind_param("ii", $editor_id, $task_type);
    $stmt->execute();
    return $stmt->get_result();
}
function getAssignedManuscripts(mysqli $conn, int $reviewer_id): mysqli_result|false {
    $sql = "
        SELECT p.id, p.title, p.status, p.submission_date, p.abstract,
               pa.deadline AS review_deadline, pa.status AS review_status,
               j.journal_name,
               CONCAT(u.first_name, ' ', u.last_name) AS primary_author
        FROM papers p
        LEFT JOIN paper_assignments pa ON p.id = pa.paper_id AND pa.reviewer_id = ?
        LEFT JOIN journals j ON p.journal_id = j.id
        LEFT JOIN author a ON p.author_id = a.id
        LEFT JOIN users u ON a.user_id = u.id
        WHERE pa.status = 'Pending'
        ORDER BY p.submission_date DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reviewer_id);
    $stmt->execute();
    return $stmt->get_result();
}
function getCoAuthors(mysqli $conn, int $reviewer_id): array {
    $co_authors = [];

    $sql = "
        SELECT paper_id, name AS co_author
        FROM paper_authors
        WHERE paper_id IN (
            SELECT paper_id FROM paper_assignments
            WHERE reviewer_id = ? AND status = 'Pending'
        )
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reviewer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $co_authors[$row['paper_id']][] = $row['co_author'];
    }
    return $co_authors;
}
function acceptReviewTask(mysqli $conn, int $paper_id, int $reviewer_id): void {
    $conn->begin_transaction();

    $stmt1 = $conn->prepare("UPDATE paper_assignments SET status = 'In-Review' WHERE paper_id = ? AND reviewer_id = ?");
    $stmt1->bind_param("ii", $paper_id, $reviewer_id);
    $stmt1->execute();
    $stmt1->close();

    $stmt2 = $conn->prepare("UPDATE papers SET status = 'Under Review' WHERE id = ?");
    $stmt2->bind_param("i", $paper_id);
    $stmt2->execute();
    $stmt2->close();

    $conn->commit();
}
function rejectReviewTask(mysqli $conn, int $paper_id, int $reviewer_id): void {
    $stmt = $conn->prepare("UPDATE paper_assignments SET status = 'Rejected' WHERE paper_id = ? AND reviewer_id = ?");
    $stmt->bind_param("ii", $paper_id, $reviewer_id);
    $stmt->execute();
    $stmt->close();
}
function fetchAcceptedPapersWithAPC($conn, $author_id) {
    $sql = "SELECT 
                p.id AS paper_id, 
                p.title AS paper_title, 
                j.journal_name, 
                j.author_apc_amount,
                COALESCE(pay.payment_status, 'Not Paid') AS payment_status
            FROM papers p 
            JOIN journals j ON p.journal_id = j.id 
            LEFT JOIN (
                SELECT paper_id, payment_status 
                FROM payments 
                WHERE payment_status = 'Paid'
            ) pay ON pay.paper_id = p.id
            WHERE p.status = 'Accepted (Final Decision)' AND p.author_id = ?
            LIMIT 25";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $author_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}
// Function to get editor details
function getEditorDetails_2($conn, $editor_id) {
    $query = $conn->prepare("
        SELECT
            CONCAT(u.first_name, ' ', COALESCE(u.middle_name, ''), ' ', u.last_name) AS full_name,
            u.email,
            e.last_login
        FROM editors e
        JOIN users u ON e.user_id = u.id
        WHERE e.editor_id = ?
    ");
    $query->bind_param("i", $editor_id);
    $query->execute();
    $result = $query->get_result();
    return $result->fetch_assoc();
}

// Function to get assigned tasks for an editor
function getAssignedTasks($conn, $editor_id) {
    $assigned_tasks = [];
    $assigned_tasks_query = $conn->prepare("
        SELECT et.task_type, et.paper_id
        FROM editor_tasks et
        WHERE et.editor_id = ?
        AND NOT EXISTS (
            SELECT 1 FROM editor_tasks et2
            WHERE et2.editor_id = et.editor_id
            AND et2.paper_id = et.paper_id
            AND et2.id = et.id
            AND et2.result = 'Not Processed'
        )
        AND NOT (et.status = 'completed' AND et.result = 'Processed for Next Level')
    ");
    $assigned_tasks_query->bind_param("i", $editor_id);
    $assigned_tasks_query->execute();
    $assigned_tasks_result = $assigned_tasks_query->get_result();

    while ($row = $assigned_tasks_result->fetch_assoc()) {
        $task_type = $row['task_type'];
        $paper_id = $row['paper_id'];

        // Group by task_type
        if (!isset($assigned_tasks[$task_type])) {
            $assigned_tasks[$task_type] = [];
        }
        $assigned_tasks[$task_type][] = $paper_id;
    }

    return $assigned_tasks;
}

// Function to get pending reviews count
function getPendingReviews_2($conn, $editor_id) {
    $result = $conn->query("SELECT COUNT(*) AS pending FROM papers WHERE status = 'Under Review' AND editor_id = $editor_id");
    return $result->fetch_assoc()['pending'];
}

// Function to get new manuscripts count
function getNewManuscriptsCount($conn, $editor_id) {
    $result = $conn->query("SELECT COUNT(*) AS new_count FROM papers WHERE status = 'pending' AND editor_id = $editor_id");
    return $result->fetch_assoc()['new_count'];
}

// Function to fetch manuscripts
function fetchManuscripts($conn, $editor_id) {
    return $conn->query("
        SELECT
            p.id,
            p.title,
            p.status,
            CONCAT(u.first_name, ' ', COALESCE(u.middle_name, ''), ' ', u.last_name) AS primary_author
        FROM papers p
        LEFT JOIN author a ON p.author_id = a.id
        LEFT JOIN users u ON a.user_id = u.id
        WHERE p.editor_id = $editor_id
        ORDER BY p.submission_date DESC
    ");
}

// Function to fetch co-authors list
function fetchCoAuthorsList($conn) {
    $co_authors_list = [];
    $co_authors_query = $conn->query("
        SELECT paper_id, name AS co_author
        FROM paper_authors
    ");
    while ($row = $co_authors_query->fetch_assoc()) {
        $co_authors_list[$row['paper_id']][] = $row['co_author'];
    }
    return $co_authors_list;
}

// Function to get pending count for notifications
function getPendingCount_2($conn, $editor_id, $task_type) {
    $notify_query = $conn->prepare("
        SELECT COUNT(*) as pending_count
        FROM editor_tasks
        WHERE editor_id = ? AND task_type = ? AND status = 'Accepted' AND (result IS NULL OR result = '')
    ");
    $notify_query->bind_param("ii", $editor_id, $task_type);
    $notify_query->execute();
    $notify_result = $notify_query->get_result();
    return $notify_result->fetch_assoc()['pending_count'];
}

// Function to check if review notification should be shown
function shouldShowReviewNotification($conn, $editor_id) {
    $sql = "
        SELECT COUNT(*) AS count
        FROM paper_assignments pa
        JOIN papers p ON pa.paper_id = p.id
        WHERE (
            pa.status IN ('In-Review', 'Rejected', 'Complete')
            OR (pa.status = 'Pending' AND pa.deadline < NOW())
        )
        AND p.editor_id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}

// Function to get the first paper and reviewer ID
function getFirstPaperAndReviewerId($conn, $editor_id) {
    $review_query = $conn->query("
        SELECT pa.paper_id, pa.reviewer_id
        FROM paper_assignments pa
        JOIN papers p ON pa.paper_id = p.id
        WHERE (
            pa.status IN ('In-Review','Rejected','Revision Requested','Revised Submitted','Completed')
            OR (pa.status = 'Pending' AND pa.deadline < NOW())
        )
        AND p.editor_id = $editor_id
        LIMIT 1
    ");
    $review_row = $review_query->fetch_assoc();
    return [
        'paper_id' => $review_row['paper_id'] ?? null,
        'reviewer_id' => $review_row['reviewer_id'] ?? null
    ];
}
function markPaymentAsPaid($conn, $razorpay_payment_id, $paper_id) {
    $stmt = $conn->prepare("UPDATE payments SET payment_status = 'Paid', razorpay_payment_id = ? WHERE paper_id = ?");
    $stmt->bind_param("si", $razorpay_payment_id, $paper_id);
    return $stmt->execute();
}

function storePaymentDetails($conn, $razorpay_order_id, $amount, $paper_id, $author_id) {
    $payment_status = 'Not Paid';
    $created_at = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO payments (paper_id, author_id, payment_amount, razorpay_order_id, payment_status, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iidsss", $paper_id, $author_id, $amount, $razorpay_order_id, $payment_status, $created_at);
    $stmt->execute();
    return $conn->insert_id;
}
function getDistinctJournalNames($conn) {
    $journals_query = "SELECT DISTINCT journal_name FROM journals";
    return $conn->query($journals_query);
}

function getManuscripts($conn, $editor_id, $selected_journal, $selected_status, $selected_reviewer, $paper_id = null) {
    $manuscripts_query = "
        SELECT p.id, p.journal_id, p.author_id, p.title, p.abstract, p.file_path, p.submission_date,
           p.keywords, p.cover_letter_path, p.copyright_agreement_path,
           p.supplementary_files_path, p.status,
           j.journal_name,
           CONCAT(u_author.first_name, ' ', COALESCE(u_author.middle_name, ''), ' ', u_author.last_name) AS author_name,
           u_review.first_name AS reviewer_first_name,
           u_review.last_name AS reviewer_last_name,
           u_review.email AS reviewer_email
        FROM papers p
        LEFT JOIN journals j ON p.journal_id = j.id
        LEFT JOIN author a ON p.author_id = a.id
        LEFT JOIN users u_author ON a.user_id = u_author.id
        LEFT JOIN paper_assignments pa ON p.id = pa.paper_id
        LEFT JOIN reviewers r ON pa.reviewer_id = r.id
        LEFT JOIN users u_review ON r.user_id = u_review.id
        WHERE 1";

    if (!empty($paper_id)) {
        $manuscripts_query .= " AND p.id = " . intval($paper_id);
    } else {
        $manuscripts_query .= " AND p.editor_id = " . intval($editor_id);
    }

    if (!empty($selected_journal)) {
        $manuscripts_query .= " AND j.journal_name = '" . $conn->real_escape_string($selected_journal) . "'";
    }
    if (!empty($selected_status)) {
        $manuscripts_query .= " AND p.status = '" . $conn->real_escape_string($selected_status) . "'";
    }
    if ($selected_reviewer == "Assigned") {
        $manuscripts_query .= " AND pa.reviewer_id IS NOT NULL";
    } elseif ($selected_reviewer == "Not Assigned") {
        $manuscripts_query .= " AND pa.reviewer_id IS NULL";
    }

    $manuscripts_query .= " ORDER BY p.submission_date DESC";
    return $conn->query($manuscripts_query);
}

function getEditorialBoard($conn, $journal_id) {
    $query = "SELECT editorial_board FROM journals WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $journal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getChiefEditorName($conn, $editorial_team_id) {
    $chiefEditorName = '';

    if ($editorial_team_id) {
        $queryChief = "SELECT editor_id FROM editorial_team_members WHERE team_id = ? AND role = 'Chief Editor' LIMIT 1";
        $stmtChief = $conn->prepare($queryChief);
        $stmtChief->bind_param("i", $editorial_team_id);
        $stmtChief->execute();
        $resultChief = $stmtChief->get_result();

        if ($resultChief->num_rows > 0) {
            $chiefEditor = $resultChief->fetch_assoc();
            $editor_id = $chiefEditor['editor_id'];

            $queryEditor = "SELECT e.title, u.first_name, u.last_name
                            FROM editors e
                            JOIN users u ON e.user_id = u.id
                            WHERE e.editor_id = ? LIMIT 1";
            $stmtEditor = $conn->prepare($queryEditor);
            $stmtEditor->bind_param("i", $editor_id);
            $stmtEditor->execute();
            $resultEditor = $stmtEditor->get_result();

            if ($resultEditor->num_rows > 0) {
                $editor = $resultEditor->fetch_assoc();
                $chiefEditorName = htmlspecialchars($editor['title'] . ' ' . $editor['first_name'] . ' ' . $editor['last_name']);
            }
        }
    }

    return $chiefEditorName;
}

function getPapersByJournalId($conn, $journal_id, $limit, $offset) {
    $paper_sql = "
        SELECT p.id, p.title, p.completed_date, p.journal_id, p.author_id,
               p.status, j.journal_name, j.access_type, u.first_name, u.last_name
        FROM papers p
        JOIN journals j ON p.journal_id = j.id
        JOIN author a ON p.author_id = a.id
        JOIN users u ON a.user_id = u.id
        WHERE p.journal_id = ? AND p.status = 'Published'
        LIMIT ? OFFSET ?";

    $paper_stmt = $conn->prepare($paper_sql);
    $paper_stmt->bind_param("iii", $journal_id, $limit, $offset);
    $paper_stmt->execute();
    return $paper_stmt->get_result();
}

function getTotalPapers_Count($conn, $journal_id) {
    $count_sql = "SELECT COUNT(*) AS total FROM papers WHERE journal_id = ? AND status = 'Published'";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("i", $journal_id);
    $count_stmt->execute();
    $total = $count_stmt->get_result()->fetch_assoc();
    return $total['total'];
}
function getIssuesByJournalId($conn, $journal_id) {
    $issueQuery = "SELECT year, volume, issue FROM papers WHERE status= 'Published' AND journal_id = ? ORDER BY year DESC, volume DESC, issue DESC";
    $issueStmt = $conn->prepare($issueQuery);
    $issueStmt->bind_param("i", $journal_id);
    $issueStmt->execute();
    $issueResult = $issueStmt->get_result();

    $issuesByYearVolume = [];
    while ($row = $issueResult->fetch_assoc()) {
        $y = $row['year'];
        $v = $row['volume'];
        $i = $row['issue'];

        $issuesByYearVolume[$y][$v][] = $i;
    }

    return $issuesByYearVolume;
}
function getCo_Authors3($conn, $paper_id) {
    $coauthor_stmt = $conn->prepare("SELECT name FROM paper_authors WHERE paper_id = ?");
    $coauthor_stmt->bind_param("i", $paper_id);
    $coauthor_stmt->execute();
    $coauthors = $coauthor_stmt->get_result();
    $coauthor_names = [];
    while ($co = $coauthors->fetch_assoc()) {
        $coauthor_names[] = $co['name'];
    }
    return implode(', ', $coauthor_names);
}
function getLatestJournalIssue($conn, $journal_id) {
    $latestJournalSql = "SELECT * FROM papers WHERE journal_id = ? AND status = 'Published' ORDER BY completed_date DESC LIMIT 1";
    $latestJournalStmt = $conn->prepare($latestJournalSql);
    $latestJournalStmt->bind_param("i", $journal_id);
    $latestJournalStmt->execute();
    $latestJournalResult = $latestJournalStmt->get_result();
    return $latestJournalResult->fetch_assoc();
}

function getVolumeIssues($conn, $volume, $journalId) {
    $volumeIssuesSql = "SELECT DISTINCT issue FROM papers WHERE volume = ? AND journal_id = ? AND status = 'Published' ORDER BY issue DESC";
    $issueStmt = $conn->prepare($volumeIssuesSql);
    $issueStmt->bind_param("ii", $volume, $journalId);
    $issueStmt->execute();
    return $issueStmt->get_result();
}

function getPapersByVolumeAndIssue($conn, $volume, $issue, $journalId, $papersPerPage, $offset) {
    $paperSql = "SELECT p.*, u.first_name AS author_first_name, u.last_name AS author_last_name
                 FROM papers p
                 JOIN paper_authors pa ON p.id = pa.paper_id
                 JOIN author a ON p.author_id = a.id
                 JOIN users u ON a.user_id = u.id
                 WHERE p.volume = ? AND p.issue = ? AND p.journal_id = ? AND p.status = 'Published'
                 LIMIT ? OFFSET ?";
    $paperStmt = $conn->prepare($paperSql);
    $paperStmt->bind_param("iiiii", $volume, $issue, $journalId, $papersPerPage, $offset);
    $paperStmt->execute();
    return $paperStmt->get_result();
}

function getTotalPapersCount($conn, $volume, $issue, $journalId) {
    $totalPapersSql = "SELECT COUNT(*) FROM papers WHERE volume = ? AND issue = ? AND journal_id = ? AND status = 'Published'";
    $totalPapersStmt = $conn->prepare($totalPapersSql);
    $totalPapersStmt->bind_param("iii", $volume, $issue, $journalId);
    $totalPapersStmt->execute();
    $totalPapersResult = $totalPapersStmt->get_result();
    return $totalPapersResult->fetch_row()[0];
}

function getCo_Authors1($conn, $paper_id, $author_id) {
    $coAuthorsSql = "SELECT pa.name FROM paper_authors pa
                     JOIN papers p ON pa.paper_id = p.id
                     WHERE pa.paper_id = ? AND p.id != ?";
    $coAuthorsStmt = $conn->prepare($coAuthorsSql);
    $coAuthorsStmt->bind_param("ii", $paper_id, $author_id);
    $coAuthorsStmt->execute();
    $coAuthorsResult = $coAuthorsStmt->get_result();
    $coAuthors = [];
    while ($coAuthor = $coAuthorsResult->fetch_assoc()) {
        $coAuthors[] = $coAuthor['name'];
    }
    return $coAuthors;
}

function getPendingManuscriptCount($conn, $editor_id) {
    $pending_count_query = "SELECT COUNT(*) AS pending_count FROM papers WHERE status = 'Pending' AND editor_id = ?";
    $stmt = $conn->prepare($pending_count_query);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['pending_count'];
}
function getPaperTitleById($conn, $paper_id) {
    $stmt = $conn->prepare("SELECT title FROM papers WHERE id = ?");
    $stmt->bind_param("i", $paper_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
function getPaperById($conn, $paper_id, $author_id) {
    $query = "SELECT p.*, j.journal_name, u.email
              FROM papers p
              JOIN journals j ON p.journal_id = j.id
              JOIN author a ON p.author_id = a.id
              JOIN users u ON a.user_id = u.id
              WHERE p.id = ? AND p.author_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $paper_id, $author_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function checkDuplicateFileHash($conn, $file_hash, $paper_id) {
    $checkQuery = "SELECT id FROM papers WHERE file_hash = ? AND id != ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("si", $file_hash, $paper_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function update_Paper($conn, $title, $abstract, $keywords, $file_path, $supplementary_files_path, $file_hash, $paper_id, $author_id) {
    $update_query = "UPDATE papers SET title = ?, abstract = ?, keywords = ?, file_path = ?, supplementary_files_path = ?, file_hash = ?, updated_at = NOW(), status = 'Revised Submitted'
                     WHERE id = ? AND author_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssssii", $title, $abstract, $keywords, $file_path, $supplementary_files_path, $file_hash, $paper_id, $author_id);
    return $stmt->execute();
}

function updatePaperAssignmentsStatus($conn, $paper_id) {
    $assignmentUpdate = "UPDATE paper_assignments SET status = 'Revised Submitted'
                         WHERE paper_id = ? AND status = 'Revision Requested'";
    $stmt = $conn->prepare($assignmentUpdate);
    $stmt->bind_param("i", $paper_id);
    return $stmt->execute();
}

function updateEditorTasks($conn, $paper_id) {
    $editorUpdate = "UPDATE editor_tasks SET result = 'Revised Submitted'
                     WHERE paper_id = ? AND result = 'Revision Request' AND task_type IN (2,4)";
    $stmt = $conn->prepare($editorUpdate);
    $stmt->bind_param("i", $paper_id);
    return $stmt->execute();
}
function getPaperDetails($conn, $paper_id) {
    $query = "
        SELECT
            p.*,
            j.access_type,
            j.reader_fee_amount,
            u.first_name AS author_fname,
            u.last_name AS author_lname,
            p.doi
        FROM papers p
        JOIN journals j ON p.journal_id = j.id
        JOIN author a ON p.author_id = a.id
        JOIN users u ON a.user_id = u.id
        WHERE p.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $paper_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
function getEditorReviewHistory($conn, $editor_id, $task_type, $status_filter) {
    $query = $conn->prepare("
        SELECT p.id AS paper_id, p.title, j.journal_name, et.status, et.result, et.id AS task_id
        FROM editor_tasks et
        JOIN papers p ON et.paper_id = p.id
        JOIN journals j ON p.journal_id = j.id
        WHERE et.editor_id = ?
        AND et.task_type = ?
        AND et.status = ?
        AND (et.result IS NULL OR et.result = '' OR et.result != 'Not Processed')
    ");
    $query->bind_param("iis", $editor_id, $task_type, $status_filter);
    $query->execute();
    return $query->get_result();
}
function getAcceptedPapersForReview($conn, $editor_id, $task_type) {
    $query = $conn->prepare("
        SELECT p.title, p.file_path, j.journal_name, et.paper_id, et.deadline, et.result, et.status, et.feedback
        FROM editor_tasks et
        JOIN papers p ON et.paper_id = p.id
        JOIN journals j ON p.journal_id = j.id
        WHERE et.editor_id = ? AND et.task_type = ? AND et.status = 'Accepted' AND (et.result IS NULL OR et.result = '')
    ");
    $query->bind_param("ii", $editor_id, $task_type);
    $query->execute();
    return $query->get_result();
}

function getPapersByEditorId($conn, $editor_id) {
    $paperQuery = "
        SELECT
            p.id AS paper_id,
            p.title AS paper_title,
            p.status,
            p.completed_date,
            j.journal_name AS journal_name,
            u.first_name AS author_first,
            u.last_name AS author_last
        FROM papers p
        JOIN author a ON p.author_id = a.id
        JOIN users u ON a.user_id = u.id
        JOIN journals j ON p.journal_id = j.id
        LEFT JOIN editor_tasks et ON et.paper_id = p.id
        WHERE p.editor_id = ?
        AND (
            (et.status = 'Completed' AND et.task_type IN (1, 2, 3, 4))
            OR p.status IN ('Rejected (Pre-Review)', 'Rejected (Post-Review)')
        )
    ";

    $paperStmt = $conn->prepare($paperQuery);
    $paperStmt->bind_param("i", $editor_id);
    $paperStmt->execute();
    return $paperStmt->get_result();
}

function getCoAuthorsByPaperId($conn, $paper_id) {
    $coAuthorQuery = "
        SELECT pa.name AS co_author_name
        FROM paper_authors pa
        WHERE pa.paper_id = ?
    ";

    $coAuthorStmt = $conn->prepare($coAuthorQuery);
    $coAuthorStmt->bind_param("i", $paper_id);
    $coAuthorStmt->execute();
    $coAuthorResult = $coAuthorStmt->get_result();

    $coAuthors = [];
    while ($coAuthor = $coAuthorResult->fetch_assoc()) {
        $coAuthors[] = $coAuthor['co_author_name'];
    }

    return $coAuthors;
}
function getUserDetails_1($conn, $user_id, $role) {
    $query = "SELECT * FROM $role WHERE user_id = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $details = $result->fetch_assoc();
    $stmt->close();
    return $details;
}
// Function to fetch paper details
function getPaperDetails_4($conn, $paper_id) {
    $query = $conn->prepare("SELECT title, file_path FROM papers WHERE id = ?");
    $query->bind_param("i", $paper_id);
    $query->execute();
    $result = $query->get_result();
    return $result->fetch_assoc();
}
function acceptEditorTask($conn, $paper_id, $editor_id, $task_type) {
    $update_query = $conn->prepare("
        UPDATE editor_tasks
        SET status = 'Accepted', response_date = NOW()
        WHERE paper_id = ? AND editor_id = ? AND task_type = ?
    ");
    $update_query->bind_param("iis", $paper_id, $editor_id, $task_type);
    $result = $update_query->execute();
    $update_query->close();
    return $result;
}
function rejectEditorTask($conn, $paper_id, $editor_id, $task_type) {
    $update_query = $conn->prepare("
        UPDATE editor_tasks
        SET status = 'Rejected', response_date = NOW()
        WHERE paper_id = ? AND editor_id = ? AND task_type = ?
    ");
    $update_query->bind_param("iis", $paper_id, $editor_id, $task_type);
    $result = $update_query->execute();
    $update_query->close();
    return $result;
}
function getPublishedPapers_1($conn, $volume, $issue, $journal_id) {
    $stmt = $conn->prepare("
        SELECT p.id, p.title, a.first_name, a.last_name, p.completed_date 
        FROM papers p
        JOIN author a ON p.author_id = a.id
        WHERE p.volume = ? AND p.issue = ? AND p.journal_id = ? AND p.status = 'Published'
    ");
    $stmt->bind_param("iii", $volume, $issue, $journal_id);
    $stmt->execute();
    return $stmt->get_result();
}
function getJournalsBySubject($conn, $primary_subject) {
    $stmt = $conn->prepare("SELECT id, journal_name FROM journals WHERE primary_subject = ?");
    if ($stmt) {
        $stmt->bind_param("s", $primary_subject);
        $stmt->execute();
        return $stmt->get_result();
    }
    return false;
}
function getTeamIdByEditor($conn, $chief_editor_id) {
    $stmt = $conn->prepare("SELECT team_id FROM editorial_team_members WHERE editor_id = ?");
    $stmt->bind_param("i", $chief_editor_id);
    $stmt->execute();
    return $stmt->get_result();
}
function getEditorialBoardByJournalId($conn, $journal_id) {
    $stmt = $conn->prepare("SELECT editorial_board FROM journals WHERE id = ?");
    $stmt->bind_param("i", $journal_id);
    $stmt->execute();
    return $stmt->get_result();
}
// function getUserByEmail_1($conn, $email) {
//     $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
//     $stmt->bind_param("s", $email);
//     $stmt->execute();
//     return $stmt->get_result();
// }
function getDistinctJournals($conn) {
    $query = "SELECT DISTINCT journal_name FROM journals";
    return $conn->query($query);
}

function getManuscripts_1($conn, $editor_id, $paper_id, $selected_journal, $selected_status, $selected_reviewer) {
    $query = "
        SELECT 
            p.id, p.journal_id, p.author_id, p.title, p.abstract, p.file_path, p.submission_date, 
            p.keywords, p.cover_letter_path, p.copyright_agreement_path, 
            p.supplementary_files_path, p.status, 
            j.journal_name, 
            CONCAT(ua.first_name, ' ', COALESCE(ua.middle_name, ''), ' ', ua.last_name) AS author_name,
            CONCAT(ur.first_name, ' ', COALESCE(ur.middle_name, ''), ' ', ur.last_name) AS reviewer_name
        FROM papers p
        LEFT JOIN journals j ON p.journal_id = j.id
        LEFT JOIN author a ON p.author_id = a.id
        LEFT JOIN users ua ON a.user_id = ua.id
        LEFT JOIN paper_assignments pa ON p.id = pa.paper_id
        LEFT JOIN reviewers r ON pa.reviewer_id = r.id
        LEFT JOIN users ur ON r.user_id = ur.id
        WHERE 1
    ";

    if (!empty($paper_id)) {
        $query .= " AND p.id = " . intval($paper_id);
    } else {
        $query .= " AND p.editor_id = " . intval($editor_id);
    }

    if (!empty($selected_journal)) {
        $query .= " AND j.journal_name = '" . $conn->real_escape_string($selected_journal) . "'";
    }
    if (!empty($selected_status)) {
        $query .= " AND p.status = '" . $conn->real_escape_string($selected_status) . "'";
    }
    if ($selected_reviewer == "Assigned") {
        $query .= " AND pa.reviewer_id IS NOT NULL";
    } elseif ($selected_reviewer == "Not Assigned") {
        $query .= " AND pa.reviewer_id IS NULL";
    }

    $query .= " ORDER BY p.submission_date DESC";
    return $conn->query($query);
}

function getPendingCount($conn, $editor_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS pending_count FROM papers WHERE status = 'Pending' AND editor_id = ?");
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['pending_count'];
}

function updateUserPassword_3($conn, $hashed_password, $user_id) {
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);
    return $stmt->execute();
}

function updateUserPassword_1($conn, $hashed_password, $user_id) {
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);
    return $stmt->execute();
}

function getTeamEditors_1($conn, $team_id, $chief_editor_id) {
    $stmt = $conn->prepare("
        SELECT e.editor_id, 
               u.first_name, u.middle_name, u.last_name, u.email,
               e.telephone, e.degree, e.gender, e.position, e.institution,
               e.department, e.city, e.state, e.country, e.last_login,
               e.editor_type, e.editor_experience,
               etm.role
        FROM editors e
        INNER JOIN editorial_team_members etm ON e.editor_id = etm.editor_id
        INNER JOIN users u ON e.user_id = u.id
        WHERE etm.team_id = ? AND e.editor_id != ?
    ");
    $stmt->bind_param("ii", $team_id, $chief_editor_id);
    $stmt->execute();
    return $stmt->get_result();
}

function getPublishedIssues($conn, $volume, $journal_id) {
    $stmt = $conn->prepare("SELECT DISTINCT issue FROM papers WHERE volume = ? AND journal_id = ? AND status = 'Published'");
    $stmt->bind_param("ii", $volume, $journal_id);
    $stmt->execute();
    return $stmt->get_result();
}
function getEditorTasks_2($conn, $editor_id, $task_type) {
    $query = $conn->prepare("
        SELECT p.id AS paper_id, p.title, j.journal_name, et.status
        FROM editor_tasks et
        JOIN papers p ON et.paper_id = p.id
        JOIN journals j ON p.journal_id = j.id
        WHERE et.editor_id = ? AND et.task_type = ?
    ");
    $query->bind_param("ii", $editor_id, $task_type);
    $query->execute();
    return $query->get_result();
}
function getEditorTasks_1($conn, $editor_id, $task_type, $status_filter) {
    $query = $conn->prepare("
        SELECT p.id AS paper_id, p.title, j.journal_name, et.status, et.result
        FROM editor_tasks et
        JOIN papers p ON et.paper_id = p.id
        JOIN journals j ON p.journal_id = j.id
        WHERE et.editor_id = ? AND et.task_type = ? AND et.status = ?
    ");
    $query->bind_param("iis", $editor_id, $task_type, $status_filter);
    $query->execute();
    return $query->get_result();
}
function getReviewerBankDetails_1($conn, $user_id) {
    $query = "SELECT bank_name, account_no, ifsc_code, bank_branch FROM reviewers WHERE user_id = ? AND LOWER(payment_type) = 'paid' LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $details = $result->fetch_assoc();
    $stmt->close();
    return $details;
}
function updateEditorTask($conn, $feedback, $result, $paper_id, $editor_id, $task_type) {
    $update_query = $conn->prepare("
        UPDATE editor_tasks
        SET feedback = ?, result = ?, status = 'Completed', response_date = NOW()
        WHERE paper_id = ? AND editor_id = ? AND task_type = ?
    ");
    $update_query->bind_param("ssiii", $feedback, $result, $paper_id, $editor_id, $task_type);
    return $update_query->execute();
}
function getEditorTasks($conn, $editor_id, $task_type) {
    $query = $conn->prepare("
        SELECT DISTINCT p.id AS paper_id, p.title, j.journal_name, et.status, et.result
        FROM editor_tasks et
        JOIN papers p ON et.paper_id = p.id
        JOIN journals j ON p.journal_id = j.id
        WHERE et.editor_id = ?
          AND et.task_type = ?
          AND (et.result IS NULL OR et.result = '' OR et.result != 'Not Processed')
          AND et.status NOT IN ('Accepted', 'Rejected')
    ");
    $query->bind_param("ii", $editor_id, $task_type);
    $query->execute();
    return $query->get_result();
}
function getCo_Authors($conn, $paper_id) {
    $co_authors = [];
    $co_stmt = $conn->prepare("SELECT name FROM paper_authors WHERE paper_id = ?");
    $co_stmt->bind_param("i", $paper_id);
    $co_stmt->execute();
    $co_result = $co_stmt->get_result();
    while ($row = $co_result->fetch_assoc()) {
        $co_authors[] = $row['name'];
    }
    return $co_authors;
}

function getVolumeIssueOptions($conn, $journal_id) {
    $vol_issue_stmt = $conn->prepare("SELECT DISTINCT volume FROM papers WHERE journal_id = ? AND status = 'Published' ORDER BY volume DESC");
    $vol_issue_stmt->bind_param("i", $journal_id);
    $vol_issue_stmt->execute();
    $vol_issue_result = $vol_issue_stmt->get_result();
    return $vol_issue_result->fetch_all(MYSQLI_ASSOC);
}

function getRelatedPapers($conn, $journal_id, $paper_id) {
    $stmt = $conn->prepare("
        SELECT
            p.id,
            p.title,
            p.completed_date,
            j.access_type,
            u.first_name,
            u.last_name
        FROM papers p
        JOIN author a ON p.author_id = a.id
        JOIN users u ON a.user_id = u.id
        JOIN journals j ON p.journal_id = j.id
        WHERE p.journal_id = ?
          AND p.id != ?
          AND p.status = 'Published'");
    $stmt->bind_param("ii", $journal_id, $paper_id);
    $stmt->execute();
    return $stmt->get_result();
}

function getCoAuthorsForPaper($conn, $paper_id) {
    $co_authors = [];
    $co_stmt = $conn->prepare("SELECT name FROM paper_authors WHERE paper_id = ?");
    $co_stmt->bind_param("i", $paper_id);
    $co_stmt->execute();
    $co_result = $co_stmt->get_result();
    while ($row = $co_result->fetch_assoc()) {
        $co_authors[] = $row['name'];
    }
    return $co_authors;
}

function getNextPaper($conn, $journal_id, $current_volume, $current_issue) {
    $next_stmt = $conn->prepare("
        SELECT id FROM papers
        WHERE journal_id = ?
          AND (volume > ? OR (volume = ? AND issue > ?)) AND status = 'Published'
        ORDER BY volume ASC, issue ASC LIMIT 1");
    $next_stmt->bind_param("iiii", $journal_id, $current_volume, $current_volume, $current_issue);
    $next_stmt->execute();
    $next_result = $next_stmt->get_result();
    return $next_result->fetch_assoc();
}

function getPreviousPaper($conn, $journal_id, $current_volume, $current_issue) {
    $prev_stmt = $conn->prepare("
        SELECT id FROM papers
        WHERE journal_id = ?
          AND (volume < ? OR (volume = ? AND issue < ?)) AND status = 'Published'
        ORDER BY volume DESC, issue DESC LIMIT 1");
    $prev_stmt->bind_param("iiii", $journal_id, $current_volume, $current_volume, $current_issue);
    $prev_stmt->execute();
    $prev_result = $prev_stmt->get_result();
    return $prev_result->fetch_assoc();
}
function getPaperTitle($conn, int $paper_id): ?string {
    $stmt = $conn->prepare("SELECT title FROM papers WHERE id = ?");
    $stmt->bind_param("i", $paper_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result ? $result['title'] : null;
}
function insert_process_author(mysqli $conn, array $data): ?int {
    $stmt = $conn->prepare("
        INSERT INTO author (
            user_id, title, telephone, degree, address, gender, researcher_type, 
            position, institution, department, street_address, city, state, zip_code, country
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssssssssssssss",
        $data['user_id'], $data['title'], $data['telephone'], $data['degree'], $data['address'],
        $data['gender'], $data['researcher_type'], $data['position'], $data['institution'],
        $data['department'], $data['street_address'], $data['city'], $data['state'], 
        $data['zip_code'], $data['country']
    );

    if ($stmt->execute()) {
        $author_id = $stmt->insert_id;
        $stmt->close();
        return $author_id;
    }

    $stmt->close();
    return null;
}

function assignToAuthor(mysqli $conn, int $author_id, array $journal_ids): void {
    $stmt = $conn->prepare("INSERT INTO author_journal (author_id, journal_id) VALUES (?, ?)");
    foreach ($journal_ids as $jid) {
        $jid = intval($jid);
        $stmt->bind_param("ii", $author_id, $jid);
        $stmt->execute();
    }
    $stmt->close();
}
function updateformateStatus(mysqli $conn, int $paper_id, string $status, string $timestamp): bool {
    $stmt = $conn->prepare("UPDATE papers SET status = ?, updated_at = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $timestamp, $paper_id);
    return $stmt->execute();
}
function getJournalName(mysqli $conn, int $journal_id): string {
    $query = "SELECT journal_name FROM journals WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $journal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['journal_name'] ?? 'Unknown Journal';
}
// Function to fetch co-author details
function fetchCoAuthorDetails($conn, $paper_id) {
    $paper_id = $conn->real_escape_string($paper_id);
    $query = "SELECT name, email FROM paper_authors WHERE paper_id = $paper_id";
    $result = $conn->query($query);
    $co_authors = [];
    while ($row = $result->fetch_assoc()) {
        $co_authors[] = $row;
    }
    return $co_authors;
}
// Function to insert a new journal access request
function insertJournalAccessRequest($conn, $reviewer_id, $journal_id) {
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    $journal_id = $conn->real_escape_string($journal_id);
    $query = "INSERT INTO reviewer_requests (reviewer_id, journal_id, status) VALUES ($reviewer_id, $journal_id, 'Pending')";
    return $conn->query($query);
}
// Function to insert payment request
function insertPaymentRequest($conn, $reviewer_id) {
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    $query = "INSERT INTO payment_requests (reviewer_id, status) VALUES ($reviewer_id, 'Requested')";
    return $conn->query($query);
}
// Function to fetch review history
function fetchHistory($conn) {
    $query = "SELECT pa.paper_id, p.title, j.journal_name, pa.status
              FROM paper_assignments pa
              JOIN papers p ON pa.paper_id = p.id
              JOIN journals j ON p.journal_id = j.id
              WHERE pa.status IN ('Accepted', 'Rejected')";
    return $conn->query($query);
}
// Function to fetch reviewers based on filters
function fetchReviewers($conn, $journal_filter, $assignment_filter, $journal_id, $paper_id) {
    $query = "
        SELECT r.*,
               u.first_name AS user_first_name,
               u.last_name AS user_last_name,
               u.email,
               GROUP_CONCAT(DISTINCT j.journal_name SEPARATOR ', ') AS journals,
               GROUP_CONCAT(DISTINCT CONCAT(p.title, ' (', p.status, ')') SEPARATOR '<br>') AS assigned_papers
        FROM reviewers r
        INNER JOIN users u ON r.user_id = u.id
        INNER JOIN reviewer_journals rj ON r.id = rj.reviewer_id
        INNER JOIN journals j ON rj.journal_id = j.id
        LEFT JOIN paper_assignments pa ON r.id = pa.reviewer_id
        LEFT JOIN papers p ON pa.paper_id = p.id
        WHERE r.registration_status = 'Approved'
    ";

    if (!empty($journal_filter)) {
        $query .= " AND j.journal_name LIKE '%" . $conn->real_escape_string($journal_filter) . "%'";
    }

    if (!empty($journal_id)) {
        $query .= " AND j.id = " . intval($journal_id);

        if (!empty($paper_id)) {
            $query .= " AND r.id NOT IN (
                SELECT reviewer_id
                FROM paper_assignments
                WHERE paper_id = $paper_id AND status = 'Rejected'
            )";
        }
    }

    if ($assignment_filter === 'unassigned') {
        $query .= " AND pa.paper_id IS NULL";
    } elseif ($assignment_filter === 'assigned') {
        $query .= " AND pa.paper_id IS NOT NULL";
    }

    $query .= " GROUP BY r.id ORDER BY j.journal_name ASC, u.last_name ASC";

    return $conn->query($query);
}

function getUserIdByEmail($conn, $email) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user ? $user['id'] : false;
}

function isReviewer($conn, $user_id) {
    $stmt = $conn->prepare("SELECT id FROM reviewers WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function updateUserPassword_2($conn, $user_id, $hashed_password) {
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);
    return $stmt->execute();
}
function getAllEditors($conn) {
    $stmt = $conn->prepare("
        SELECT 
            e.*, 
            u.first_name, 
            u.middle_name, 
            u.last_name, 
            u.email 
        FROM 
            editors e 
        JOIN 
            users u ON e.user_id = u.id
    ");
    
    $stmt->execute();
    $result = $stmt->get_result();
    $editors = [];

    while ($row = $result->fetch_assoc()) {
        $editors[] = $row;
    }

    return $editors;
}

function getUserIdByEmail_Editor($conn, $email) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user ? $user['id'] : false;
}
function getEditorCount($conn, $status) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM editors WHERE registration_status = ?");
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

function isEditor($conn, $user_id) {
    $stmt = $conn->prepare("SELECT editor_id FROM editors WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function updateUserPassword_editor($conn, $user_id, $hashed_password) {
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);
    return $stmt->execute();
}

function updateEditorTaskResult($conn, $result, $editor_id, $paper_id) {
    $result = $conn->real_escape_string($result);
    $editor_id = $conn->real_escape_string($editor_id);
    $paper_id = $conn->real_escape_string($paper_id);
    $query = "UPDATE editor_tasks SET status = 'Completed', result = '$result', response_date = NOW() WHERE editor_id = $editor_id AND paper_id = $paper_id";
    return $conn->query($query);
}
// Function to fetch reviewer details
function fetchReviewer_dash($conn, $reviewer_id) {
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    $query = "SELECT u.first_name, u.last_name, u.email, r.last_login FROM reviewers r JOIN users u ON r.user_id = u.id WHERE r.id = $reviewer_id";
    $result = $conn->query($query);
    return $result->fetch_assoc();
}

// Function to count revised submissions
function countRevisedSubmissions($conn, $reviewer_id) {
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    $query = "SELECT COUNT(*) FROM paper_assignments WHERE reviewer_id = $reviewer_id AND status = 'Revised Submitted'";
    $result = $conn->query($query);
    $row = $result->fetch_row();
    return $row[0];
}

// Function to fetch review stats
function fetchReviewStats($conn, $reviewer_id) {
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    $query = "SELECT status, COUNT(*) as count FROM paper_assignments WHERE reviewer_id = $reviewer_id GROUP BY status";
    $result = $conn->query($query);
    $stats = ['pending' => 0, 'completed' => 0];
    while ($row = $result->fetch_assoc()) {
        if ($row['status'] == 'Pending') $stats['pending'] = $row['count'];
        if ($row['status'] == 'Completed') $stats['completed'] = $row['count'];
    }
    return $stats;
}

// Function to count overdue reviews
function countOverdueReviews($conn, $reviewer_id, $today) {
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    $today = $conn->real_escape_string($today);
    $query = "SELECT COUNT(*) FROM paper_assignments WHERE reviewer_id = $reviewer_id AND status = 'Under Review' AND deadline < '$today'";
    $result = $conn->query($query);
    $row = $result->fetch_row();
    return $row[0];
}

// Function to fetch pending assignments
function fetchPendingAssignments($conn, $reviewer_id) {
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    $query = "SELECT p.id, p.title, pa.deadline FROM paper_assignments pa JOIN papers p ON pa.paper_id = p.id WHERE pa.reviewer_id = $reviewer_id AND pa.status = 'Pending'";
    return $conn->query($query);
}

// Function to fetch accepted papers for review submission
function fetchAcceptedPapersForReview($conn, $reviewer_id) {
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    $query = "SELECT p.id, p.title FROM paper_assignments pa JOIN papers p ON pa.paper_id = p.id WHERE pa.reviewer_id = $reviewer_id AND pa.status = 'Under Review'";
    return $conn->query($query);
}

// Function to fetch deadlines for color-coded notifications
function fetchDeadlines($conn, $reviewer_id) {
    $reviewer_id = $conn->real_escape_string($reviewer_id);
    $query = "SELECT deadline FROM paper_assignments WHERE reviewer_id = $reviewer_id AND status = 'Under Review'";
    return $conn->query($query);
}
// Function to fetch completed reviews
function fetchCompleted($conn, $editor_id) {
    $editor_id = $conn->real_escape_string($editor_id);
    $query = "
        SELECT
            p.id AS paper_id,
            p.title,
            j.journal_name,
            pa.status AS assignment_status,
            pa.deadline,
            pa.completed_date,
            f.feedback
        FROM paper_assignments pa
        INNER JOIN papers p ON pa.paper_id = p.id
        INNER JOIN journals j ON p.journal_id = j.id
        LEFT JOIN feedback f ON pa.paper_id = f.paper_id AND pa.reviewer_id = f.reviewer_id
        WHERE pa.status = 'Completed' OR pa.status = 'In-Review' OR pa.status = 'Revision Requested' OR pa.status ='Revised Submitted'
          AND EXISTS (
              SELECT 1 FROM editor_tasks et
              WHERE et.editor_id = $editor_id AND et.paper_id = pa.paper_id
          )
    ";
    return $conn->query($query);
}
function fetchJournals_1($limit, $offset, $sort = '', $search = '') {
    global $conn;
    $query = "SELECT * FROM journals";

    if (!empty($search)) {
        $search = mysqli_real_escape_string($conn, $search);
        $query .= " WHERE journal_name LIKE '%$search%'";
    }

    if ($sort === 'asc') {
        $query .= " ORDER BY journal_name ASC";
    } elseif ($sort === 'desc') {
        $query .= " ORDER BY journal_name DESC";
    }

    $query .= " LIMIT $limit OFFSET $offset";

    return mysqli_query($conn, $query);
}

// Fetch total journal count for pagination
function fetchTotalJournals($search = '') {
    global $conn;
    $query = "SELECT COUNT(*) as total FROM journals";

    if (!empty($search)) {
        $search = mysqli_real_escape_string($conn, $search);
        $query .= " WHERE journal_name LIKE '%$search%'";
    }

    $res = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($res)['total'];
}
// Function to update user password
function updateUserPass($conn, $hashed_password, $user_id) {
    $hashed_password = $conn->real_escape_string($hashed_password);
    $user_id = $conn->real_escape_string($user_id);
    return $conn->query("UPDATE users SET password = '$hashed_password' WHERE id = $user_id");
}

function fetchJournals($conn, $type, $input, $minAcceptance, $minCiteScore, $minImpact) {
    $sql = "SELECT * FROM journals WHERE acceptance_rate >= ? AND citescore >= ? AND impact_factor >= ?";
    $params = [$minAcceptance, $minCiteScore, $minImpact];
    $types = "ddd";

    if (!empty($input)) {
        if ($type === 'keyword') {
            $sql .= " AND (FIND_IN_SET(?, keywords) > 0 OR journal_name LIKE ? OR scope LIKE ?)";
            $params[] = $input;
            $params[] = "%$input%";
            $params[] = "%$input%";
            $types .= "sss";
        } elseif ($type === 'abstract') {
            $sql .= " AND (scope LIKE ?)";
            $params[] = "%$input%";
            $types .= "s";
        }
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $journals = [];
    while ($row = $result->fetch_assoc()) {
        $journals[] = $row;
    }

    return $journals;
}
function isEmailRegistered($conn, $email) {
    $email = $conn->real_escape_string($email);
    $result = $conn->query("SELECT id FROM author WHERE email = '$email'");
    return $result->num_rows > 0 ? $result->fetch_assoc() : false;
}
// Function to update reminder_sent field
function updateReminderSent($conn, $task_id) {
    $task_id = $conn->real_escape_string($task_id);
    return $conn->query("UPDATE editor_tasks SET reminder_sent = 1 WHERE id = $task_id");
}
// Function to fetch editorial team name
function fetchEditorialTeamName($conn, $editorial_team_id) {
    $editorial_team_id = $conn->real_escape_string($editorial_team_id);
    $query = "SELECT team_name FROM editorial_teams WHERE team_id = $editorial_team_id";
    $result = $conn->query($query);
    return $result->num_rows > 0 ? $result->fetch_assoc()['team_name'] : 'Not Assigned Yet';
}
function getUserDetails($conn, $user_id) {
    $tables = ['author', 'reviewers', 'editors'];
    foreach ($tables as $table) {
        $result = $conn->query("SELECT *, '$table' as source_table FROM $table WHERE user_id = $user_id");
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
    }
    return null;
}

function getAllReviewersWithJournals($conn) {
    return $conn->query("SELECT r.*, u.first_name, u.middle_name, u.last_name, u.email, GROUP_CONCAT(j.journal_name SEPARATOR ', ') AS journal_names, GROUP_CONCAT(j.primary_subject SEPARATOR ', ') AS journal_subjects FROM reviewers r JOIN users u ON r.user_id = u.id LEFT JOIN reviewer_journals rj ON r.id = rj.reviewer_id LEFT JOIN journals j ON rj.journal_id = j.id GROUP BY r.id ORDER BY r.registration_status DESC, r.id DESC");
}

function getApprovedReviewers($conn) {
    return getReviewerCount($conn, 'approved');
}

function getRejectedReviewers($conn) {
    return getReviewerCount($conn, 'rejected');
}

function getPendingReviewers($conn) {
    return getReviewerCount($conn, 'pending');
}

function getTotalJournals($conn) {
    $result = $conn->query("SELECT COUNT(*) AS count FROM journals");
    $row = $result->fetch_assoc();
    return $row['count'];
}

function getPersonById($conn, $role, $person_id) {
    if ($role === "editor") {
        $sql = "SELECT u.first_name, u.middle_name, u.last_name, u.email, e.editor_payment_type FROM editors e JOIN users u ON e.user_id = u.id WHERE e.editor_id = $person_id";
    } else if ($role === "reviewer") {
        $sql = "SELECT u.first_name, u.middle_name, u.last_name, u.email, r.payment_type FROM reviewers r JOIN users u ON r.user_id = u.id WHERE r.id = $person_id";
    } else {
        return false;
    }
    $result = $conn->query($sql);
    return $result->num_rows > 0 ? $result->fetch_assoc() : false;
}

function updateContract($conn, $role, $person_id) {
    if ($role === 'reviewer') {
        return $conn->query("UPDATE reviewers SET contract_status = 'sent' WHERE id = $person_id");
    } elseif ($role === 'editor') {
        return $conn->query("UPDATE editors SET contract_status = 'sent' WHERE editor_id = $person_id");
    } else {
        return false;
    }
}

function getPapersForReview($conn, $filter_condition = "") {
    return $conn->query("SELECT papers.*, payments.payment_status FROM papers LEFT JOIN payments ON papers.id = payments.paper_id $filter_condition");
}

function getPendingPaperCount($conn) {
    $result = $conn->query("SELECT COUNT(*) as count FROM papers WHERE status = 'Pending'");
    $row = $result->fetch_assoc();
    return $row['count'];
}

function getAcceptedPaperCount($conn) {
    $result = $conn->query("SELECT COUNT(*) as count FROM papers WHERE status = 'Accepted (Final Decision)'");
    $row = $result->fetch_assoc();
    return $row['count'];
}

function getRejectedPaperCount($conn) {
    $result = $conn->query("SELECT COUNT(*) as count FROM papers WHERE status = 'Rejected (Post-Review)'");
    $row = $result->fetch_assoc();
    return $row['count'];
}

function getReviewerRequestById($conn, $id) {
    $id = $conn->real_escape_string($id);
    return $conn->query("SELECT rr.*, u.first_name, u.last_name, u.email, j.journal_name FROM reviewer_requests rr JOIN reviewers r ON rr.reviewer_id = r.id JOIN users u ON r.user_id = u.id JOIN journals j ON rr.journal_id = j.id WHERE rr.id = $id");
}

function getPendingPapersWithAuthorAndJournal($conn) {
    return $conn->query("SELECT p.id AS paper_id, p.title, CONCAT(u.first_name, ' ', u.last_name) AS author_name, j.journal_name, p.editor_id FROM papers p LEFT JOIN author a ON p.author_id = a.id LEFT JOIN users u ON a.user_id = u.id LEFT JOIN journals j ON p.journal_id = j.id WHERE p.status = 'pending'");
}

function getEditorContractCount($conn) {
    $result = $conn->query("SELECT COUNT(*) FROM editors WHERE contract_status IN ('pending_verification', 'reupload')");
    return $result->fetch_row()[0];
}

function getReviewerContractCount($conn) {
    $result = $conn->query("SELECT COUNT(*) FROM reviewers WHERE contract_status IN ('pending_verification', 'reupload')");
    return $result->fetch_row()[0];
}

function fetchNewReviewerRequests($conn) {
    return $conn->query("SELECT rr.id, u.first_name, u.last_name, u.email, j.journal_name, rr.status, rr.created_at FROM reviewer_requests rr JOIN reviewers r ON rr.reviewer_id = r.id JOIN users u ON r.user_id = u.id JOIN journals j ON rr.journal_id = j.id WHERE rr.status = 'pending'");
}

function getAuthorDetails($author_id) {
    global $conn;
    $author_id = $conn->real_escape_string($author_id);
    $result = $conn->query("SELECT u.first_name, u.last_name, u.email, a.last_login FROM author a JOIN users u ON a.user_id = u.id WHERE a.id = $author_id");
    return $result->fetch_assoc();
}

function getAuthorStats($author_id) {
    global $conn;
    $stats = [];
    $result = $conn->query("SELECT COUNT(*) AS total FROM papers WHERE author_id = $author_id");
    $stats['total_submissions'] = $result->fetch_assoc()['total'];
    $result = $conn->query("SELECT COUNT(*) AS under_review FROM papers WHERE author_id = $author_id AND status = 'Under Review'");
    $stats['under_review'] = $result->fetch_assoc()['under_review'];
    $result = $conn->query("SELECT COUNT(*) AS accepted FROM papers WHERE author_id = $author_id AND status = 'Accepted'");
    $stats['accepted'] = $result->fetch_assoc()['accepted'];
    $result = $conn->query("SELECT COUNT(*) AS rejected FROM papers WHERE author_id = $author_id AND status = 'Rejected'");
    $stats['rejected'] = $result->fetch_assoc()['rejected'];
    return $stats;
}

function getUserCommonDetails($user_id) {
    global $conn;
    $fields = ['position', 'institution', 'department', 'street_address', 'city', 'state', 'zip_code', 'country', 'telephone', 'degree', 'gender', 'address'];
    $result = array_fill_keys($fields, '');
    $typeFieldMap = ['author' => 'researcher_type', 'reviewers' => 'reviewer_type', 'editors' => 'editor_type'];
    $allData = [];
    $userTypes = [];
    $tables = ['author', 'reviewers', 'editors'];
    foreach ($tables as $table) {
        $typeField = $typeFieldMap[$table];
        $sql = "SELECT " . implode(", ", $fields) . ", $typeField FROM $table WHERE user_id = $user_id";
        $data = $conn->query($sql)->fetch_assoc();
        if ($data) {
            $allData[$table] = $data;
            $userTypes[$table] = strtolower($data[$typeField]);
        } else {
            $allData[$table] = null;
            $userTypes[$table] = null;
        }
    }
    if ($allData['editors'] && $userTypes['editors']) {
        $primaryTable = 'editors';
    } elseif ($allData['author'] && $userTypes['author']) {
        $primaryTable = 'author';
    } elseif ($allData['reviewers'] && $userTypes['reviewers']) {
        $primaryTable = 'reviewers';
    } else {
        return $result;
    }
    if ($userTypes[$primaryTable] === 'individual') {
        foreach ($fields as $f) {
            if (!empty($allData[$primaryTable][$f])) {
                $result[$f] = $allData[$primaryTable][$f];
            }
        }
        return $result;
    }
    foreach ($fields as $f) {
        if (!empty($allData[$primaryTable][$f])) {
            $result[$f] = $allData[$primaryTable][$f];
        } else {
            foreach ($tables as $t) {
                if ($t !== $primaryTable && $userTypes[$t] === 'affiliated' && !empty($allData[$t]) && !empty($allData[$t][$f])) {
                    $result[$f] = $allData[$t][$f];
                    break;
                }
            }
        }
    }
    return $result;
}

function getReviewerBankDetails($user_id) {
    global $conn;
    $result = $conn->query("SELECT payment_type, account_holder_name, bank_name, account_number, ifsc_code, branch_name, bank_country FROM reviewers WHERE user_id = $user_id");
    return $result->fetch_assoc();
}

function getEditorBankDetails($user_id) {
    global $conn;
    $result = $conn->query("SELECT editor_payment_type, editor_account_holder, editor_bank_name, editor_account_number, editor_ifsc, editor_branch_name, editor_bank_country FROM editors WHERE user_id = $user_id");
    return $result->fetch_assoc();
}
?>
