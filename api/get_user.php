<?php
require '../vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
header('Content-Type: application/json; charst=UTF-8');

include_once('../config/database.php');
include_once('../classes/Users.php');

$db = new Database();
$connection = $db->connect();
$user_obj = new Users($connection);

try {
    $all_headers = getallheaders();
    $token = $all_headers['Authorization'] ?? null;
    if (!empty($token)) {
        $secret_key = "owt125";
        $alg = "HS256";
        $decoded = JWT::decode($token, new Key($secret_key, $alg));
        $user_id = $decoded->data->id;

        $user = $user_obj->get_user_by_id($user_id);

        unset($user['password']);
        http_response_code(200);
        $response = [
            'status' => 1,
            'message' => 'User Data',
            'user' => $user,
        ];
    } else {
        http_response_code(404);
        $response = [
            'status' => 0,
            'message' => 'You are not authorized'
        ];
    }
} catch (Exception $e) {
    http_response_code(500);
    $response = [
        'status' => 0,
        'message' => $e->getMessage(),
    ];
}
echo json_encode($response);
