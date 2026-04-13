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
    public function errorCheck($errorCode, $message, $actualCode = null)
    {
        $code = $actualCode ?? $this->connection->errno;
        if ((int) $code === (int) $errorCode) {
            $safeMessage = json_encode($message, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
            echo "<script>alert($safeMessage + $errorCode)</script>";
        }
    }
    public function runQuery(string $sql, string $message = 'Terjadi kesalahan database')
    {
        try {
            return $this->connection->query($sql);
        } catch (\mysqli_sql_exception $e) {
            $this->errorCheck((int) $e->getCode(), $message, (int) $e->getCode());
            return false;
        }
    }
}
?>