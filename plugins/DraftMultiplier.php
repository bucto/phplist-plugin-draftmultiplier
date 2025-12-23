<?php

class DraftMultiplier extends phplistPlugin
{
    public $name = 'DraftMultiplier';
    public $version = '1.1.11';
    public $authors = 'bucto';
    public $enabled = true;
    public $description = 'Pro tool to duplicate campaign drafts with individual personalization.';
    public $documentationUrl = 'https://github.com/bucto/phplist-plugin-draftmultiplier';

    public $topMenuLinks = array(
        'multiplier' => array(
            'category' => 'campaigns',
            'min_auth' => 0,    // Sichtbarkeit im Menü für alle Admins
        ),
        'manage'     => array(
            'category' => 'campaigns',
            'min_auth' => 0,    // Sichtbarkeit im Menü für alle Admins
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

    /**
     * Erlaubt den Zugriff auf die Seiten für alle Administratoren.
     * Ohne diese Funktion könnten nur Superuser die Seiten tatsächlich öffnen.
     */
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