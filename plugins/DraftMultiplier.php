<?php

class DraftMultiplier extends phplistPlugin
{
    public $name = 'DraftMultiplier';
    public $version = '1.1.3';
    public $authors = 'bucto';
    public $enabled = true;
    public $description = 'Automatically creates the required database table on installation.';

	public $topMenuLinks = array(
        'multiplier' => array('category' => 'system'),
        'manage'     => array('category' => 'system'), // NEU
    );

    public $pageTitles = array(
        'multiplier' => 'Draft Multiplier Pro',
        'manage'     => 'Manage Recipient Data', // NEU
    );

    function __construct()
    {
        $this->coderoot = dirname(__FILE__) . '/DraftMultiplier/';
        parent::__construct();
    }

    /* Diese Funktion wird von phpList beim Aktivieren/Laden aufgerufen */
    function initialise()
    {
        parent::initialise();
        
        // SQL zum Erstellen der Tabelle
        $sql = "CREATE TABLE IF NOT EXISTS Draft_Multiplier_Data (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255),
            footer TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        Sql_Query($sql);
        
        return true;
    }
}