<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json; charst=UTF-8');

include_once('../config/database.php');
include_once('../classes/Users.php');
include_once('../classes/Referrals.php');

$db = new Database();
$connection = $db->connect();
$user_obj = new Users($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['country_id']) && !empty($_POST['dob'])) {
        $user_obj->username = $_POST['username'];
        $user_obj->email = $_POST['email'];

        // $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
        // $random_pass = substr(str_shuffle($data), 0, 8);

        $random_pass = 'password';

        $user_obj->password = password_hash($random_pass, PASSWORD_DEFAULT);
        $user_obj->country_id = $_POST['country_id'];
        $user_obj->dob = date('Y-m-d', strtotime($_POST['dob']));

        if (!empty($user_obj->check_email()) || !empty($user_obj->check_username())) {
            http_response_code(500);
            $response = [
                'status' => 0,
                'message' => 'User already exists!'
            ];
        } else {
            $user_id = $user_obj->create_user();
            if ($user_id) {
                if (isset($_POST['referral_username']) && $_POST['referral_username'] != '') {
                    $user_obj->username = $_POST['referral_username'];
                    $user_data = $user_obj->check_username();
                    if (empty($user_data)) {
                        $user_obj->delete_user($user_id);
                        http_response_code(500);
                        $response = [
                            'status' => 0,
                            'message' => 'Your Referral user does not exist!'
                        ];
                        echo json_encode($response);
                        die();
                    } else {
                        $ref_obj = new Referrals($connection);
                        $ref_obj->user_id = $user_data['id'];
                        $ref_obj->referral_user_id = $user_id;
                        $ref_obj->create_referred_user();
                    }
                }
                $user_data = $user_obj->get_user_by_id($user_id);
                // the message
                $msg = "This is your password " . $random_pass . ". Keep it safe!";

                // use wordwrap() if lines are longer than 70 characters
                $msg = wordwrap($msg, 70);

                // send email
                $is_sent = mail($user_data['email'], "Password", $msg);
                http_response_code(200);
                $response = [
                    'status' => 1,
                    'message' => 'User has been created',
                    'mail_sent' => $is_sent
                ];
            } else {
                http_response_code(500);
                $response = [
                    'status' => 0,
                    'message' => 'Failed to save user'
                ];
            }
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
