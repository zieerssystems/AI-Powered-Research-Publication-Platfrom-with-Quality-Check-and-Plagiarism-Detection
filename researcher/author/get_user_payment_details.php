<?php
include(__DIR__ . "/../../include/db_connect.php");

$user_id = intval($_GET['user_id'] ?? 0);
$role = $_GET['role'] ?? '';

$response = ['success' => false];

if ($user_id > 0 && ($role === 'reviewers' || $role === 'editors')) {
    if ($role === 'editors') {
        // Fetch editor details first
        $editorDetails = getUserDetails_1($conn, $user_id, 'editors');
        if ($editorDetails) {
            $editorPaymentType = strtolower($editorDetails['payment_type'] ?? '');
            if ($editorPaymentType === 'paid') {
                // Now check reviewer table for same user_id and payment_type=paid
                $reviewerDetails = getReviewerBankDetails_1($conn, $user_id);
                if ($reviewerDetails) {
                    $response = [
                        'success' => true,
                        'payment_type' => 'paid',
                        'bank_name' => $reviewerDetails['bank_name'] ?? '',
                        'account_no' => $reviewerDetails['account_no'] ?? '',
                        'ifsc_code' => $reviewerDetails['ifsc_code'] ?? '',
                        'bank_branch' => $reviewerDetails['bank_branch'] ?? '',
                    ];
                } else {
                    // Reviewer with paid not found, fallback to editor bank details
                    $response = [
                        'success' => true,
                        'payment_type' => $editorPaymentType,
                        'bank_name' => $editorDetails['bank_name'] ?? '',
                        'account_no' => $editorDetails['account_no'] ?? '',
                        'ifsc_code' => $editorDetails['ifsc_code'] ?? '',
                        'bank_branch' => $editorDetails['bank_branch'] ?? '',
                    ];
                }
            } else {
                // Editor payment type unpaid, no bank details needed
                $response = [
                    'success' => true,
                    'payment_type' => $editorPaymentType,
                    'bank_name' => '',
                    'account_no' => '',
                    'ifsc_code' => '',
                    'bank_branch' => '',
                ];
            }
        }
    } else if ($role === 'reviewers') {
        // For reviewer role just fetch their details normally
        $details = getUserDetails_1($conn, $user_id, 'reviewers');
        if ($details) {
            $response = [
                'success' => true,
                'payment_type' => $details['payment_type'] ?? '',
                'bank_name' => $details['bank_name'] ?? '',
                'account_no' => $details['account_no'] ?? '',
                'ifsc_code' => $details['ifsc_code'] ?? '',
                'bank_branch' => $details['bank_branch'] ?? '',
            ];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>