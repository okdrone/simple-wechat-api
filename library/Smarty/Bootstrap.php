<?php

/**************************************************
 * Copyright (c).
 * Filename: Smarty_Bootstrap.php
 * Author: Wanbo Ge <gewanbo@gmail.com>
 * Create Date: 2018/7/13
 * Description:
 **************************************************/

Yaf_Loader::import( dirname(__FILE__)."/libs/Smarty.class.php");
Yaf_Loader::import( dirname(__FILE__)."/libs/Autoloader.php");

class Smarty_Bootstrap implements Yaf_View_Interface
{

    protected $smarty;

    public function __construct()
    {
        Smarty_Autoloader::register();
        $this->smarty = new Smarty();
    }

    /**
     * Setting template files path
     *
     * @param string $path Template files path
     */
    public function setTemplatePath($path){
        if(is_readable($path)){
            $this->smarty->setTemplateDir($path);
        }
    }

    /**
     * Fetch template and display page
     *
     * @param string $tpl Template name
     */
    public function display($tpl){
        echo $this->smarty->fetch($tpl);
    }
}