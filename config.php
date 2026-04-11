<?php
class Database
{
    private $host = "localhost";
private $user = "root";
private $pass = "";
    private $db = "simedic";
    protected $connection;
    public function __construct()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
                $this->connection = mysqli_connect($this->host, $this->user, $this->pass, $this->db);
        } catch (\mysqli_sql_exception $e) {
            header('Location: ./error?code=500');
        }
    }
}
?>