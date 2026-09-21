<?php

class Database
{
    private $host = "db";
    private $username = "root";
    private $password = "root";
    private $database = "crud_operations";

    public function connect()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $conn = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->database
        );

        $conn->set_charset("utf8mb4");

        return $conn;
    }
}