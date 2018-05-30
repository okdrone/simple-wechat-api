<?php

/**************************************************
 * Copyright (c).
 * Filename: app_core.php
 * Author: Wanbo Ge <gewanbo@gmail.com>
 * Create Date: 2018/5/29
 * Description:
 **************************************************/
class APP_Core
{
    static private $isInit = false;

    static public function init(){
        if (self::$isInit){
            return false;
        }

        define('ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
        define('CONF_PATH', ROOT_PATH . '/conf');

        self::initYaf();

        self::$isInit = true;

        return Yaf_Application::app();
    }

    static public function initYaf() {

        $objYCI = new Yaf_Config_Ini(CONF_PATH . '/app/demo/app.ini');
        $yaf_conf = $objYCI->toArray();
        $yaf_conf['application']['directory'] = ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'demo';
        new Yaf_Application($yaf_conf);
    

    }
}
