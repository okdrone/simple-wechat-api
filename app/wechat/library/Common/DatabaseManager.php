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

            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8', $conf['host'], $conf['port'], $conf['name']);

            $instance = new \PDO($dsn, $conf['user'], $conf['pswd']);

            //$db_conn = new DatabaseConnector($conf);

            self::$databaseList[$db_name] = $instance;
        }

        return self::$databaseList[$db_name];
    }
}