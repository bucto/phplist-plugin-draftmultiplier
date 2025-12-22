<?php

class DraftMultiplier extends phplistPlugin
{
    public $name = 'DraftMultiplier';
    public $version = '1.0.4';
    public $authors = 'bucto';
    public $enabled = true;
    public $description = 'Vervielfältigt Entwürfe für Massentests.';

    /* RSS-Manager Methode für das Menü */
    public $topMenuLinks = array(
        'multiplier' => array('category' => 'system'),
    );

    public $pageTitles = array(
        'multiplier' => 'Draft Multiplier Tool',
    );

    function __construct()
    {
        // Wir setzen den Pfad explizit auf das Verzeichnis, in dem diese Datei liegt
        $this->coderoot = dirname(__FILE__) . '/';
        parent::__construct();
    }

    function display($page)
    {
        if ($page == 'multiplier') {
            echo '<div class="container-fluid">';
            echo '<h1>' . s('Draft Multiplier Tool') . '</h1>';
            echo '<p>' . s('Das Plugin ist bereit zum Kopieren.') . '</p>';
            
            // Test-Abfrage der Entwürfe
            $req = Sql_Query(sprintf(
                'SELECT id, subject FROM %s WHERE status = "draft" ORDER BY entered DESC LIMIT 10',
                $GLOBALS['tables']['message']
            ));
            
            echo '<ul>';
            while ($row = Sql_Fetch_Assoc($req)) {
                echo '<li>' . $row['id'] . ': ' . htmlspecialchars($row['subject']) . '</li>';
            }
            echo '</ul>';
            
            echo '</div>';
            return true;
        }
        return false;
    }
}