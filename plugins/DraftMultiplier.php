<?php

class DraftMultiplier extends phplistPlugin
{
    public $name = 'DraftMultiplier'; // Das wird in der Liste angezeigt
    public $version = '1.0.5';
    public $authors = 'bucto';
    public $enabled = true;
    public $description = 'Vervielfältigt Entwürfe für Massentests.';

    /* Diese Struktur hat bei Drafttest funktioniert */
    public $topMenuLinks = array(
        'multiplier' => array('category' => 'system'),
    );

    public $pageTitles = array(
        'multiplier' => 'Draft Multiplier Tool',
    );

    function __construct()
    {
        $this->coderoot = dirname(__FILE__) . '/';
        parent::__construct();
    }

    /* Falls topMenuLinks klemmt, fängt adminmenu es ab */
    function adminmenu()
    {
        return array(
            'multiplier' => 'Draft Multiplier Tool'
        );
    }

    function display($page)
    {
        if ($page == 'multiplier') {
            echo '<div class="container-fluid">';
            echo '<h1>' . s('Draft Multiplier Tool') . '</h1>';
            echo '<p>' . s('Menüpunkt erfolgreich wiederhergestellt!') . '</p>';
            
            // Kleine Vorschau der Entwürfe zur Kontrolle
            $req = Sql_Query(sprintf('SELECT id, subject FROM %s WHERE status = "draft" LIMIT 5', $GLOBALS['tables']['message']));
            while ($row = Sql_Fetch_Assoc($req)) {
                echo '<li>' . htmlspecialchars($row['subject']) . '</li>';
            }
            
            echo '</div>';
            return true;
        }
        return false;
    }
}