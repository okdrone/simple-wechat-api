<?php

/**************************************************
 * Copyright (c).
 * Filename: Config.php
 * Author: Wanbo Ge <gewanbo@gmail.com>
 * Create Date: 2018/5/31
 * Description:
 **************************************************/
class App_Config
{

    /**
     * @param string $name     Config file name.
     * @param string $section  Which section in the config file.
     * @return array Config options
     */
    public static function getAppConfig($name, $section = ''){
        $conf = [];

        if(!empty($name)){

            if(empty($section)) {
                $confObj = new Yaf_Config_Ini(CONF_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR
                    . APP_Core::appName() . DIRECTORY_SEPARATOR . $name . '.ini');
            } else {
                $confObj = new Yaf_Config_Ini(CONF_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR
                    . APP_Core::appName() . DIRECTORY_SEPARATOR . $name . '.ini', $section);
            }

            $conf = $confObj->toArray();
        }

        return $conf;
    }

    /**
     * @param string $name     Config file name.
     * @param string $section  Which section in the config file.
     * @return array Config options
     */
    public static function getDBConfig($name, $section = ''){
        $conf = [];

        if(!empty($name)){

            if(empty($section)) {
                $confObj = new Yaf_Config_Ini(CONF_PATH . DIRECTORY_SEPARATOR . 'db'
                    . DIRECTORY_SEPARATOR . $name . '.ini');
            } else {
                $confObj = new Yaf_Config_Ini(CONF_PATH . DIRECTORY_SEPARATOR . 'db'
                    . DIRECTORY_SEPARATOR . $name . '.ini', $section);
            }

            $conf = $confObj->toArray();
        }

        return $conf;
    }
}