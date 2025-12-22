<?php

class DraftMultiplierPlugin extends phplistPlugin // Muss zum Dateinamen passen!
{
    public $name = 'Draft Multiplier Tool';
    public $version = '1.0.0';
    public $authors = 'bucto';
    public $enabled = true;

    /* Deine bewährte RSS-Technik für das Menü */
    public $topMenuLinks = array(
        'multiplier' => array('category' => 'system'),
    );

    public $pageTitles = array(
        'multiplier' => 'Draft Multiplier Tool',
    );

    function __construct()
    {
        // Da die Datei jetzt im /plugins/ Ordner liegt, 
        // findet dirname(__FILE__) den Pfad automatisch richtig.
        $this->coderoot = dirname(__FILE__) . '/';
        parent::__construct();
    }

    function display($page)
    {
        if ($page == 'multiplier') {
            echo '<div class="container-fluid">';
            echo '<h1>' . s('Draft Multiplier Tool') . '</h1>';
            echo '<p>' . s('Willkommen im neuen, sauberen Plugin-Repo!') . '</p>';
            
            // Hier kommt später die Tabellen-Abfrage rein
            echo '</div>';
            return true;
        }
        return false;
    }
}