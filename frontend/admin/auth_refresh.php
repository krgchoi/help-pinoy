<?php
session_start();

if (!isset($_SESSION['refresh_token'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No refresh token']);
    exit();
}

$api_url = 'http://localhost:5000/admin/refresh';

$data = json_encode([
    'refresh_token' => $_SESSION['refresh_token']
]);

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if ($result['status'] === 'success') {
    $_SESSION['access_token'] = $result['access_token'];
    echo json_encode(['success' => true]);
} else {
    session_destroy();
    echo json_encode(['success' => false]);
}
