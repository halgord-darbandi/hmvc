<?php

namespace App\Database;

class Connection
{
    private $connection;

    public function __construct()
    {
        $this->connection = mysqli_connect($_ENV['HOST'], $_ENV['USER'], $_ENV['PASSWORD'], $_ENV['DATABASE']);
        mysqli_set_charset($this->connection , 'utf-8');
    }

    public function Connection(): object
    {
        return $this->connection;
    }
}