<?php
require '../vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json; charst=UTF-8');

include_once('../config/database.php');
include_once('../classes/Referrals.php');

$db = new Database();
$connection = $db->connect();
$ref_obj = new Referrals($connection);

// if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $all_headers = getallheaders();
        $token = $all_headers['Authorization'] ?? null;
        if (!empty($token)) {
            $secret_key = "owt125";
            $alg = "HS256";
            $decoded = JWT::decode($token, new Key($secret_key, $alg));
            $user_id = $decoded->data->id;

            $ref_obj->user_id = $user_id;
            $users = $ref_obj->get_referred_users();

            http_response_code(200);
            $response = [
                'status' => 1,
                'message' => 'Your referred Users',
                'referred_users' => $users,
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
// } else {
//     http_response_code(503);
//     $response = [
//         'status' => 0,
//         'message' => 'Access denied'
//     ];
// }
echo json_encode($response);
