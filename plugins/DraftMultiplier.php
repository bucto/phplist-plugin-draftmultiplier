<?php

class DraftMultiplier extends phplistPlugin {
    public $name = 'DraftMultiplier';
    public $version = '1.0.9';
    public $authors = 'bucto';
    public $enabled = true;

    public $topMenuLinks = array(
        'multiplier' => array('category' => 'system'),
    );

    public $pageTitles = array(
        'multiplier' => 'Draft Multiplier Tool',
    );

    function __construct() {
        // WICHTIG: Coderoot muss auf den Unterordner zeigen
        $this->coderoot = dirname(__FILE__) . '/DraftMultiplier/';
        parent::__construct();
    }
    
    // Wir lassen display() hier weg, damit phpList in den Unterordner schaut
}