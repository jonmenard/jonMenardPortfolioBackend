<?php
// greeting.php

// ----- CORS: only allow your GitHub Pages site -----
$allowedOrigin = getenv('ALLOWED_ORIGIN') ?: 'https://jonmenard.github.io';
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== $allowedOrigin) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(["error" => "Forbidden", "message" => "Only requests from $allowedOrigin are permitted. Request came from $origin"]);
    exit;
}
header("Access-Control-Allow-Origin: $allowedOrigin");
header('Content-Type: application/json');

// ----- API key -----
$apiKey = getenv('OPEN_AI_API_KEY');
if (!$apiKey) {
    http_response_code(500);
    echo json_encode(["error" => "Missing OPEN_AI_API_KEY"]);
    exit;
}


// ----- Messages -----
$input = "Please write a one-to-two-sentence greeting for my (Jon Menard) software engineer portfolio website. "
        . "Tone: confident, warm, concise, and professional—no hype or buzzword salad. The content will be served on $allowedOrigin - feel free to use that for context ";
// ----- OpenAI call -----
$payload = [
    "model" => "gpt-5-nano",
    "input" => $input,
    "reasoning" => [
		"effort" => "minimal"
    ],
];

$ch = curl_init('https://api.openai.com/v1/responses');
$tmp = fopen('php://temp', 'w+');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        "Authorization: Bearer $apiKey",
    ],
    CURLOPT_POST => true,
    CURLOPT_VERBOSE => true,
    CURLOPT_STDERR => $tmp,
    CURLOPT_POSTFIELDS => json_encode($payload),
]);
$response = curl_exec($ch);

if ($response === false) {
    http_response_code(502);
    echo json_encode(["error" => curl_error($ch)]);
    exit;
}
curl_close($ch);

$data = json_decode($response, true);
$greeting = $data['output'][1]['content'][0]['text'] ?? "Welcome to my porfolio! —explore the projects.";

echo json_encode(["greeting" => $greeting]);
