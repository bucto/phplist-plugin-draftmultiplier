<?php

class DraftMultiplier extends phplistPlugin
{
    public $name = 'DraftMultiplier';
    public $version = '1.1.12';
    public $authors = 'bucto';
    public $enabled = true;
    public $description = 'Pro tool to duplicate campaign drafts with individual personalization.';
    public $documentationUrl = 'https://github.com/bucto/phplist-plugin-draftmultiplier';

    public $topMenuLinks = array(
        'multiplier' => array(
            'category' => 'campaigns',
            'min_auth' => 0, 
        ),
        'manage'     => array(
            'category' => 'campaigns',
            'min_auth' => 0,
        ),
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

    // WICHTIG: Diese Funktion überschreibt die Standardprüfung von phpList
    function adminAllowed($page)
    {
        if ($page == 'multiplier' || $page == 'manage') {
            return true;
        }
        return parent::adminAllowed($page);
    }

    // Zusätzliche Absicherung für den Seitenaufruf
    function allowAccess($page)
    {
        if ($page == 'multiplier' || $page == 'manage') {
            return true;
        }
        return parent::allowAccess($page);
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