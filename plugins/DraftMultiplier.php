<?php

class DraftMultiplier extends phplistPlugin
{
    public $name = 'DraftMultiplier';
    public $version = '1.0.6';
    public $authors = 'bucto';
    public $enabled = true;

    public $topMenuLinks = array(
        'multiplier' => array('category' => 'system'),
    );

    public $pageTitles = array(
        'multiplier' => 'Draft Multiplier Tool',
    );

    function __construct()
    {
        // Wir setzen den Pfad absolut sicher
        $this->coderoot = dirname(__FILE__) . '/';
        parent::__construct();
    }

    function display($page)
    {
        // Hier prüfen wir, ob phpList die Seite 'multiplier' aufruft
        if ($page == 'multiplier') {
            echo '<div class="container-fluid">';
            echo '<h1>' . s('Draft Multiplier Tool') . '</h1>';
            echo '<p>Erfolg! Die Seite wurde gefunden.</p>';
            
            // Zeige uns zur Sicherheit an, wo phpList gerade sucht
            echo '<p>Coderoot: ' . $this->coderoot . '</p>';
            
            echo '</div>';
            return true;
        }
        return false;
    }
}