<?php

class Database extends PDO
{
    public function __construct() {
        parent::__construct(
                'mysql:host='.DB_HOST.';'.'dbname='.DB_DB,
                DB_USER,
                DB_PWD,
                array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
                );        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
      public function query($query, $fetchMode = null) {
        if ($fetchMode !== null) {
            return parent::query($query, $fetchMode);
        }
        return parent::query($query);
    }

    public function executeQueries($queries) {
        if (!is_array($queries)){
            return $this->query($queries);
        }else{
            foreach ($queries as $query) {
                $this->query($query);
            }
        }
    }
}

