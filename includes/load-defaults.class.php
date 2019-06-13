<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * General Settings class
 */
class EC_Load_Defaults
{
    private $_menu_position;

    public function __construct()
    {
        $this->_menu_position=new Helper_Menu_Position();
        $this->menu_position();
    }

    public function menu_position()
    {
        $this->_menu_position->add();
    }
}