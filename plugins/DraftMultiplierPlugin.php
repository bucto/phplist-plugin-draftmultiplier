<?php

class DraftMultiplierPlugin extends phplistPlugin
{
    /* Hier steht jetzt nur noch DraftMultiplier */
    public $name = 'DraftMultiplier'; 
    
    public $version = '1.0.2';
    public $authors = 'bucto';
    public $enabled = true;
    public $description = 'Vervielfältigt Entwürfe für Massentests.';

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

    function display($page)
    {
        if ($page == 'multiplier') {
            echo '<div class="container-fluid">';
            echo '<h1>' . s('Draft Multiplier Tool') . '</h1>';
            echo '<p>' . s('Die Anzeige wurde aktualisiert.') . '</p>';
            echo '</div>';
            return true;
        }
        return false;
    }
}