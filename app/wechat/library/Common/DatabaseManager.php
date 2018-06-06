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

            $options = array(
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_AUTOCOMMIT => false
            );

            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8', $conf['host'], $conf['port'], $conf['name']);

            $instance = new \PDO($dsn, $conf['user'], $conf['pswd'], $options);

            //$db_conn = new DatabaseConnector($conf);

            self::$databaseList[$db_name] = $instance;
        }

        return self::$databaseList[$db_name];
    }
}