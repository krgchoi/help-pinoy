<?php

function api_call($url, $jwt_token, $method = 'GET', $data = null)
{
    $ch = curl_init($url);

    $headers = [
        'Content-Type: application/json',
        "Authorization: Bearer $jwt_token"
    ];

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? json_encode($data) : $data);
        }
    } elseif ($method !== 'GET') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    }

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 401) {
        session_destroy();
        header('Location: admin_logout.php');
        exit();
    }

    if ($response === false) {
        session_destroy();
        header('Location: admin_logout.php');
        exit();
    }
    $decoded = json_decode($response, true);

    if (is_array($decoded) && isset($decoded['status']) && ($decoded['status'] === 'error' || $decoded['status'] === 'expire')) {
        session_destroy();
        header('Location: admin_logout.php');
        exit();
    }

    return $decoded;
}
