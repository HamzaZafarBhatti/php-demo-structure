<?php

class Database
{
    private $hostname;
    private $dbname;
    private $username;
    private $password;
    private $conn;

    public function connect()
    {
        $this->hostname = 'localhost';
        $this->dbname = 'register_login';
        $this->username = 'root';
        $this->password = '';

        $conn = new mysqli($this->hostname, $this->username, $this->password, $this->dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }
}
