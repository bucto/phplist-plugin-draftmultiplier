<?php

class DraftMultiplier extends phplistPlugin
{
    public $name = 'DraftMultiplier';
    public $version = '1.1.5';
    public $authors = 'bucto';
    public $enabled = true;
    public $description = 'Pro tool to duplicate campaign drafts with individual personalization.';

    public $topMenuLinks = array(
        'multiplier' => array('category' => 'campaigns'),
        'manage'     => array('category' => 'campaigns'),
    );

    public $pageTitles = array(
        'multiplier' => 'Draft Multiplier: Create Copies',
        'manage'     => 'Draft Multiplier: Manage Recipients',
    );

    function __construct()
    {
        $this->coderoot = dirname(__FILE__) . '/DraftMultiplier/';
        parent::__construct();
    }

    function initialise()
    {
        parent::initialise();
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