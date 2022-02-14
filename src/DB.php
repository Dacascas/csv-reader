<?php

namespace Importer;

use PDO;

class DB
{
    /**
     * @var string
     */
    private $host;
    /**
     * @var string
     */
    private $user;
    /**
     * @var string
     */
    private $pass;
    /**
     * @var string
     */
    private $dbname;
    /**
     * @var PDO
     */
    private $conn;

    /**
     * @param string $host
     * @param string $dbname
     * @param string $user
     * @param string $pass
     */
    public function __construct(string $host, string $dbname, string $user, string $pass)
    {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->user = $user;
        $this->pass = $pass;
    }

    /**
     * Get a connection to the database
     *
     * @return PDO|void
     */
    public function getConnection()
    {
        try {
            if (!$this->conn) {
                $conn = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->user, $this->pass);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn = $conn;
            }

            return $this->conn;
        } catch(\PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }
}
