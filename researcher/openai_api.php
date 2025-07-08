<?php
header("Content-Type: application/json");

$api_key = "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"; // Replace with your actual API key

$data = json_decode(file_get_contents("php://input"), true);
$text = $data['text'];

$prompt = "Correct the grammar and improve the clarity of the following text:\n\n" . $text;

$ch = curl_init("https://api.openai.com/v1/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $api_key,
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "model" => "gpt-3.5-turbo",  // Changed model for better compatibility
    "messages" => [
        ["role" => "system", "content" => "You are a grammar correction AI."],
        ["role" => "user", "content" => $prompt]
    ],
    "max_tokens" => 500
]));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);

// Debugging: Show full API response
if ($http_code !== 200) {
    echo json_encode(["error" => "API Error", "response" => $response]);
    exit;
}

// Check if response contains valid text
if (isset($result["choices"][0]["message"]["content"])) {
    $corrected_text = $result["choices"][0]["message"]["content"];
    echo json_encode(["corrected" => trim($corrected_text)]);
} else {
    echo json_encode(["error" => "Unexpected API response", "response" => $response]);
}
?>
