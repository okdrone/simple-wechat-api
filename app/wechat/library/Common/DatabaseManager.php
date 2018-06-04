<?php
/**************************************************
 * Copyright (c).
 * Filename: DatabaseManager.php
 * Author: Wanbo Ge <gewanbo@gmail.com>
 * Create Date: 2018/6/4
 * Description:
 **************************************************/

namespace Common;


class DatabaseManager
{

    static protected $databaseList = [];

    public static function getInstance($db_name){
        if(!key_exists($db_name, self::$databaseList)){

            $conf = \App_Config::getDBConfig('wechat', $db_name);

            $db_conn = new DatabaseConnector($conf);

            self::$databaseList[$db_name] = $db_conn;
        }

        return self::$databaseList[$db_name];
    }
}