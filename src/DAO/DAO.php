<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MicroCMS\DAO;

use Doctrine\DBAL\Connection;

/**
 * Description of DAO
 *
 * @author trigger
 */
abstract class DAO
{
    /**
     * Database connection.
     * 
     * @var \Doctrine\DBAL\Connection
     */
    private $db;

    /**
     * 
     * @param \Doctrine\DBAL\Connection The database connection object.
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Grant access to the database connection object
     * 
     * @return \Doctrine\DBAL\Connection The database connection object.
     */
    protected function getDb()
    {
        return $this->db;
    }

    /**
     * Build a domain object from a DB row.
     * Must be overriden by child classes.
     */
    protected abstract function buildDomainObject($row);
}
