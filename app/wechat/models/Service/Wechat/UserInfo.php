<?php

/**************************************************
 * Copyright (c).
 * Filename: UserInfo.php
 * Author: Wanbo Ge <gewanbo@gmail.com>
 * Create Date: 2018/6/4
 * Description:
 **************************************************/
class Service_Wechat_UserInfo
{
    protected $logger;

    public function __construct()
    {
        $this->logger = new Logger_App();
    }

    public function createUser(Dao_UserInfo $userInfo){

        $db = \Common\DatabaseManager::getInstance('xyz');

        if($db instanceof PDO){
            $stm = $db->prepare('INSERT INTO xyz_user_info (`username`, `nickname`, `create_ts`) VALUE (:username, :nickname, :create_ts)');
            $stm->bindValue(':username', $userInfo->username, PDO::PARAM_STR);
            $stm->bindValue(':nickname', $userInfo->nickname, PDO::PARAM_STR);
            $stm->bindValue(':create_ts', time(), PDO::PARAM_INT);
            $stm->execute();
        }

    }
}