<?php

require '../vendor/autoload.php';

use \Firebase\JWT\JWT;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json; charst=UTF-8');

include_once('../config/database.php');
include_once('../classes/Users.php');

$db = new Database();
$connection = $db->connect();
$user_obj = new Users($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $user_obj->email = $_POST['email'];

        $user_data = $user_obj->get_user();

        if (!empty($user_data)) {
            $username = $user_data['username'];
            $email = $user_data['email'];
            $first_name = $user_data['first_name'];
            $last_name = $user_data['last_name'];
            if (password_verify($_POST['password'], $user_data['password'])) {

                $iss = 'localhost';
                $iat = time();
                $exp = $iat + 3600;
                $aud = 'myusers';
                $user_arr_data = [
                    'id' => $user_data['id'],
                    // 'username' => $user_data['username'],
                    // 'email' => $user_data['email'],
                    // 'first_name' => $user_data['first_name'],
                    // 'last_name' => $user_data['last_name'],
                ];

                $secret_key = "owt125";
                $alg = "HS256";

                $payload_info = [
                    'iss' => $iss,
                    'iat' => $iat,
                    'exp' => $exp,
                    'aud' => $aud,
                    'data' => $user_arr_data,
                ];

                $jwt = JWT::encode($payload_info, $secret_key, $alg);

                http_response_code(200);
                $response = [
                    'status' => 1,
                    'token' => $jwt,
                    // 'user_data' => $user_arr_data,
                    'message' => 'User Logged in successfully'
                ];
            } else {
                http_response_code(500);
                $response = [
                    'status' => 0,
                    'message' => 'Invalid credentials!'
                ];
            }
        } else {
            http_response_code(404);
            $response = [
                'status' => 0,
                'message' => 'Invalid email or password!'
            ];
        }
    } else {
        http_response_code(500);
        $response = [
            'status' => 0,
            'message' => 'All fields are required'
        ];
    }
} else {
    http_response_code(503);
    $response = [
        'status' => 0,
        'message' => 'Access denied'
    ];
}
echo json_encode($response);
