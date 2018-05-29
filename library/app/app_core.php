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

        echo 'root_path:';
        var_dump(ROOT_PATH);

        new Yaf_Application(ROOT_PATH . "/conf/app/demo_app/app.ini");

        self::$isInit = true;

        return Yaf_Application::app();
    }
}