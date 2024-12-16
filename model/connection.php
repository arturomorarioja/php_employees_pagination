<?php

require_once 'config.php';

/**
 * Encapsulates a connection to the database 
 * 
 * @author  Arturo Mora-Rioja
 * @version 1.0.0 August 2020
 * @version 1.0.1 December 2024 Adapted to PHP8's syntax
 */
class DB extends Config 
{
    public const ERROR = 'There was a database error';

    /**
     * Opens a connection to the database
     * 
     * @returns a PDO object
     */
    public function connect(): PDO 
    {
        $dsn = 'mysql:host=' . DB::HOST . ';dbname=' . DB::DB_NAME . ';charset=utf8';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            $pdo = @new PDO($dsn, DB::USERNAME, DB::PASSWORD, $options); 
            return($pdo);   
        } catch (\PDOException $e) {
            die('Connection unsuccessful: ' . $e->getMessage());
        }    
    }

    /**
     * Closes a connection to the database
     * 
     * @param the connection object to disconnect
     */
    public function disconnect(PDO $pdo): void 
    {
        $pdo = null;
    }
}