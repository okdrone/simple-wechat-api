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

    public function createUser(Dao_UserInfo $userInfo, Dao_UserOpenInfo $userOpenInfo){

        try {

            $db = \Common\DatabaseManager::getInstance('xyz');

            if ($db instanceof PDO) {
                $db->beginTransaction();

                $stm = $db->prepare('INSERT INTO xyz_user_info (`username`, `nickname`, `icon`, `create_ts`) VALUE (:username, :nickname, :icon, :create_ts)');
                $stm->bindValue(':username', $userInfo->username, PDO::PARAM_STR);
                $stm->bindValue(':nickname', $userInfo->nickname, PDO::PARAM_STR);
                $stm->bindValue(':icon', $userInfo->icon, PDO::PARAM_STR);
                $stm->bindValue(':create_ts', time(), PDO::PARAM_INT);
                $ret = $stm->execute();

                if($ret === false)
                    throw new Exception('Create user failed!');

                $user_id = $db->lastInsertId();

                $userOpenInfo->user_id = $user_id;

                $stm = $db->prepare('INSERT INTO xyz_user_open_info (`user_id`, `open_type`, `open_app_id`, `open_user_id`, `create_ts`) VALUE (:user_id, :open_type, :open_app_id, :open_user_id, :create_ts)');
                $stm->bindValue(':user_id', $userOpenInfo->user_id, PDO::PARAM_INT);
                $stm->bindValue(':open_type', $userOpenInfo->open_type, PDO::PARAM_INT);
                $stm->bindValue(':open_app_id', $userOpenInfo->open_app_id, PDO::PARAM_STR);
                $stm->bindValue(':open_user_id', $userOpenInfo->open_user_id, PDO::PARAM_STR);
                $stm->bindValue(':create_ts', time(), PDO::PARAM_INT);
                $ret = $stm->execute();

                if($ret === false) {
                    $db->rollBack();
                    throw new Exception('Create user failed!');
                }

                $db->commit();
            } else {
                throw new Exception('The $db is not instance of PDO.');
            }

        } catch (Exception $e){
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }

    public function disableOpenUser(Dao_UserOpenInfo $userOpenInfo){
        try {

            $db = \Common\DatabaseManager::getInstance('xyz');

            if ($db instanceof PDO) {
                $db->beginTransaction();

                $stm = $db->prepare('SELECT user_id xyz_user_open_info where `open_type`=:open_type and `open_app_id`=:open_app_id and `open_user_id`=:open_user_id limit 1');
                $stm->bindValue(':open_type', $userOpenInfo->open_type, PDO::PARAM_INT);
                $stm->bindValue(':open_app_id', $userOpenInfo->open_app_id, PDO::PARAM_STR);
                $stm->bindValue(':open_user_id', $userOpenInfo->open_user_id, PDO::PARAM_STR);
                $ret = $stm->execute();

                if($ret === false)
                    throw new Exception('Query user open info failed!');

                $result = $stm->fetch(PDO::FETCH_ASSOC);

                if($result){
                    $user_id = $result['user_id'];
                } else {
                    throw new Exception('There was error when fetch user open info.');
                }

                $stm = $db->prepare('UPDATE xyz_user_open_info set `status`=1 where `user_id`=:user_id');
                $stm->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                $ret = $stm->execute();

                if($ret === false) {
                    throw new Exception('Disable user failed!');
                }

                $db->commit();
            } else {
                throw new Exception('The $db is not instance of PDO.');
            }

        } catch (Exception $e){
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }
}