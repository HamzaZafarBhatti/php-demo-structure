<?php

class Users
{

    public $username;
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $country_id;
    public $dob;

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create_user()
    {
        $sql = "INSERT INTO users SET username = ?, email = ?, password = ?, country_id = ?, dob = ?";

        $user_obj = $this->conn->prepare($sql);

        $user_obj->bind_param('sssis', $this->username, $this->email, $this->password, $this->country_id, $this->dob);

        if ($user_obj->execute()) {
            return $user_obj->insert_id;
        }
        echo $user_obj->error;
        die();
        return false;
    }

    public function check_email()
    {
        $sql = "SELECT * FROM users WHERE email = ?";

        $user_obj = $this->conn->prepare($sql);

        $user_obj->bind_param('s', $this->email);

        if ($user_obj->execute()) {
            $data = $user_obj->get_result();
            return $data->fetch_assoc();
        }
        return array();
    }

    public function check_username()
    {
        $sql = "SELECT * FROM users WHERE username = ?";

        $user_obj = $this->conn->prepare($sql);

        $user_obj->bind_param('s', $this->username);

        if ($user_obj->execute()) {
            $data = $user_obj->get_result();
            return $data->fetch_assoc();
        }
        return array();
    }

    public function get_user()
    {
        $sql = "SELECT * FROM users WHERE email = ?";

        $user_obj = $this->conn->prepare($sql);

        $user_obj->bind_param('s', $this->email);

        if ($user_obj->execute()) {
            $data = $user_obj->get_result();
            return $data->fetch_assoc();
        }
        return array();
    }

    public function get_user_by_id($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";

        $user_obj = $this->conn->prepare($sql);

        $user_obj->bind_param('i', $id);

        if ($user_obj->execute()) {
            $data = $user_obj->get_result();
            return $data->fetch_assoc();
        }
        return array();
    }

    public function delete_user($id)
    {
        $sql = "DELETE FROM users WHERE id = ?";

        $user_obj = $this->conn->prepare($sql);

        $user_obj->bind_param('i', $id);

        if ($user_obj->execute()) {
            return true;
        }
        return false;
    }
}
