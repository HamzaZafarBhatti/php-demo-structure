<?php

class Referrals
{

    public $user_id;
    public $referral_user_id;

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create_referred_user()
    {
        $sql = "INSERT INTO referrals SET user_id = ?, referral_user_id = ?";

        $ref_obj = $this->conn->prepare($sql);

        $ref_obj->bind_param('ii', $this->user_id, $this->referral_user_id);

        if ($ref_obj->execute()) {
            return $ref_obj->insert_id;
        }
        return false;
    }

    public function get_referred_users()
    {
        $sql = "SELECT * FROM users INNER JOIN referrals ON users.id = referrals.referral_user_id WHERE referrals.user_id = ?";

        $ref_obj = $this->conn->prepare($sql);

        $ref_obj->bind_param('i', $this->user_id);

        if ($ref_obj->execute()) {
            $data = $ref_obj->get_result();
            return $data->fetch_all(MYSQLI_ASSOC);
        }
        return array();
    }
}
