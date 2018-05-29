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

        self::$isInit = true;

        return Yaf_Application::app();
    }
}