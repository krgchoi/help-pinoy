<?php

/**
 * Helper function to make authenticated API calls with automatic logout on token expiration
 */
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

    // Handle 401 Unauthorized - token expired or invalid
    if ($http_code === 401) {
        session_destroy();
        header('Location: admin_logout.php');
        exit();
    }

    // Handle curl errors
    if ($response === false) {
        session_destroy();
        header('Location: admin_logout.php');
        exit();
    }

    $decoded = json_decode($response, true);

    // Handle error status in response
    if (is_array($decoded) && isset($decoded['status']) && ($decoded['status'] === 'error' || $decoded['status'] === 'expire')) {
        session_destroy();
        header('Location: admin_logout.php');
        exit();
    }

    return $decoded;
}
