<?php

/**************************************************
 * Copyright (c).
 * Filename: Core.php
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
        define('APP_PATH', ROOT_PATH . '/app');
        define('LIB_PATH', ROOT_PATH . '/library');
        define('CONF_PATH', ROOT_PATH . '/conf');
        define('LOG_PATH', ROOT_PATH . '/logs');


        self::initYaf();

        self::$isInit = true;

        return Yaf_Application::app();
    }

    static public function initYaf() {

        $objYCI = new Yaf_Config_Ini(CONF_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . self::appName() .
            DIRECTORY_SEPARATOR . 'app.ini');
        $yaf_conf = $objYCI->toArray();
        $yaf_conf['application']['directory'] = APP_PATH . DIRECTORY_SEPARATOR . self::appName();
        $yaf_conf['application']['library']['directory'] = $yaf_conf['application']['directory'] . DIRECTORY_SEPARATOR . 'library';
        new Yaf_Application($yaf_conf);

    }

    static public function appName() {

        $app_name = 'demo';
        $uri_arr = explode('/', $_SERVER['SCRIPT_NAME']);

        if (isset($uri_arr[1]) && !empty($uri_arr)) {
            $app_name = $uri_arr[1];
        }

        return $app_name;

    }
}
