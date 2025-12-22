<?php
class DraftMultiplier extends phplistPlugin {
    public $name = 'DraftMultiplier';
    public $version = '1.1.1';
    public $authors = 'bucto';
    public $enabled = true;

    public $topMenuLinks = array(
        'multiplier' => array('category' => 'system'),
    );

    public $pageTitles = array(
        'multiplier' => 'Draft Multiplier Pro',
    );

    function __construct() {
        $this->coderoot = dirname(__FILE__) . '/DraftMultiplier/';
        parent::__construct();
    }
}