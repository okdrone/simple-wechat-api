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

                /**
                 * 1. Whether the user exists or not
                 */
                $stm = $db->prepare('SELECT i.user_id, i.`status` from xyz_user_info i, xyz_user_open_info oi where i.user_id=oi.user_id and `open_type`=:open_type and `open_app_id`=:open_app_id and `open_user_id`=:open_user_id limit 1');
                $stm->bindValue(':open_type', $userOpenInfo->open_type, PDO::PARAM_INT);
                $stm->bindValue(':open_app_id', $userOpenInfo->open_app_id, PDO::PARAM_STR);
                $stm->bindValue(':open_user_id', $userOpenInfo->open_user_id, PDO::PARAM_STR);
                $ret = $stm->execute();

                if($ret === false) {
                    throw new Exception('Query user open info failed! ERROR:' . json_encode($stm->errorInfo()));
                }

                if ($stm->rowCount() > 0) {
                    $result = $stm->fetch(PDO::FETCH_ASSOC);

                    if ($result) {
                        $user_id = $result['user_id'];
                        $user_status = $result['status'];

                        $this->logger->info('uid:' . $user_id . '=>ustat:' . $user_status);

                        if($user_status === 1){
                            $stm = $db->prepare('UPDATE xyz_user_info set `status`=0 where `user_id`=:user_id');
                            $stm->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                            $ret = $stm->execute();

                            if($ret === false) {
                                throw new Exception('Enable user failed! ERROR:' . json_encode($stm->errorInfo()));
                            }
                        }
                    } else {
                        throw new Exception('There was error when fetch user open info.');
                    }
                } else {

                    /**
                     * 2. Create user info
                     */
                    $stm = $db->prepare('INSERT INTO xyz_user_info (`username`, `nickname`, `icon`, `create_ts`) VALUE (:username, :nickname, :icon, :create_ts)');
                    $stm->bindValue(':username', $userInfo->username, PDO::PARAM_STR);
                    $stm->bindValue(':nickname', $userInfo->nickname, PDO::PARAM_STR);
                    $stm->bindValue(':icon', $userInfo->icon, PDO::PARAM_STR);
                    $stm->bindValue(':create_ts', time(), PDO::PARAM_INT);
                    $ret = $stm->execute();

                    if ($ret === false)
                        throw new Exception('Create user failed! ERROR:' . json_encode($stm->errorInfo()));

                    $user_id = $db->lastInsertId();

                    $userOpenInfo->user_id = $user_id;

                    /**
                     * 3. Create user open info
                     */
                    $stm = $db->prepare('INSERT INTO xyz_user_open_info (`user_id`, `open_type`, `open_app_id`, `open_user_id`, `create_ts`) VALUE (:user_id, :open_type, :open_app_id, :open_user_id, :create_ts)');
                    $stm->bindValue(':user_id', $userOpenInfo->user_id, PDO::PARAM_INT);
                    $stm->bindValue(':open_type', $userOpenInfo->open_type, PDO::PARAM_INT);
                    $stm->bindValue(':open_app_id', $userOpenInfo->open_app_id, PDO::PARAM_STR);
                    $stm->bindValue(':open_user_id', $userOpenInfo->open_user_id, PDO::PARAM_STR);
                    $stm->bindValue(':create_ts', time(), PDO::PARAM_INT);
                    $ret = $stm->execute();

                    if ($ret === false) {
                        $db->rollBack();
                        throw new Exception('Create user open info failed! ERROR:' . json_encode($stm->errorInfo()));
                    }

                }

                $db->commit();
            } else {
                throw new Exception('The $db is not instance of PDO.');
            }

        } catch (Exception $e){
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }

    public function getUserByOpenInfo(Dao_UserOpenInfo $userOpenInfo){
        $retUserInfo = false;

        try {

            $db = \Common\DatabaseManager::getInstance('xyz');

            if ($db instanceof PDO) {

                $stm = $db->prepare('SELECT user_id from xyz_user_open_info where `open_type`=:open_type and `open_app_id`=:open_app_id and `open_user_id`=:open_user_id limit 1');
                $stm->bindParam(':open_type', $userOpenInfo->open_type, PDO::PARAM_INT);
                $stm->bindParam(':open_app_id', $userOpenInfo->open_app_id, PDO::PARAM_STR);
                $stm->bindParam(':open_user_id', $userOpenInfo->open_user_id, PDO::PARAM_STR);
                $ret = $stm->execute();

                if($ret === false) {
                    throw new Exception('Query user open info failed! ERROR:' . json_encode($stm->errorInfo()));
                }

                if($stm->rowCount() > 0){
                    $result = $stm->fetch(PDO::FETCH_ASSOC);

                    if($result){
                        $user_id = $result['user_id'];
                    } else {
                        throw new Exception('There was error when fetch user open info.');
                    }

                    $this->logger->info('User ID:' . $user_id);

                    $stm = $db->prepare('SELECT * from xyz_user_info where `user_id`=:user_id');
                    $stm->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $ret = $stm->execute();

                    if($ret === false) {
                        throw new Exception('Query user info failed! ERROR:' . json_encode($stm->errorInfo()));
                    }

                    if($stm->rowCount() > 0){
                        $result = $stm->fetch();

                        if($result){
                            $retUserInfo = new Dao_UserInfo();
                            $retUserInfo->user_id = $result['user_id'];
                            $retUserInfo->username = $result['username'];
                            $retUserInfo->nickname = $result['nickname'];
                            $retUserInfo->icon = $result['icon'];
                            $retUserInfo->status = $result['status'];
                            $retUserInfo->create_ts = $result['create_ts'];
                        } else {
                            throw new Exception('There was error when fetch user info.');
                        }
                    }
                }

            } else {
                throw new Exception('The $db is not instance of PDO.');
            }

        } catch (Exception $e){
            $this->logger->error($e->getMessage(), $e->getTrace());
        }

        return $retUserInfo;
    }

    public function enableOpenUser(Dao_UserOpenInfo $userOpenInfo){
        try {

            $db = \Common\DatabaseManager::getInstance('xyz');

            if ($db instanceof PDO) {
                $db->beginTransaction();

                $stm = $db->prepare('SELECT user_id from xyz_user_open_info where `open_type`=:open_type and `open_app_id`=:open_app_id and `open_user_id`=:open_user_id limit 1');
                $stm->bindValue(':open_type', $userOpenInfo->open_type, PDO::PARAM_INT);
                $stm->bindValue(':open_app_id', $userOpenInfo->open_app_id, PDO::PARAM_STR);
                $stm->bindValue(':open_user_id', $userOpenInfo->open_user_id, PDO::PARAM_STR);
                $ret = $stm->execute();

                if($ret === false) {
                    throw new Exception('Query user open info failed! ERROR:' . json_encode($stm->errorInfo()));
                }

                if($stm->rowCount() > 0){
                    $result = $stm->fetch(PDO::FETCH_ASSOC);

                    if($result){
                        $user_id = $result['user_id'];
                    } else {
                        throw new Exception('There was error when fetch user open info.');
                    }

                    $this->logger->info('User ID:' . $user_id);

                    $stm = $db->prepare('UPDATE xyz_user_info set `status`=:user_status where `user_id`=:user_id');
                    $stm->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                    $stm->bindValue(':user_status', Dao_UserState::ENABLE, PDO::PARAM_INT);
                    $ret = $stm->execute();

                    if($ret === false) {
                        throw new Exception('Enable user failed! ERROR:' . json_encode($stm->errorInfo()));
                    }
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

                $stm = $db->prepare('SELECT user_id from xyz_user_open_info where `open_type`=:open_type and `open_app_id`=:open_app_id and `open_user_id`=:open_user_id limit 1');
                $stm->bindValue(':open_type', $userOpenInfo->open_type, PDO::PARAM_INT);
                $stm->bindValue(':open_app_id', $userOpenInfo->open_app_id, PDO::PARAM_STR);
                $stm->bindValue(':open_user_id', $userOpenInfo->open_user_id, PDO::PARAM_STR);
                $ret = $stm->execute();

                if($ret === false) {
                    throw new Exception('Query user open info failed! ERROR:' . json_encode($stm->errorInfo()));
                }

                if($stm->rowCount() > 0){
                    $result = $stm->fetch(PDO::FETCH_ASSOC);

                    if($result){
                        $user_id = $result['user_id'];
                    } else {
                        throw new Exception('There was error when fetch user open info.');
                    }

                    $this->logger->info('User ID:' . $user_id);

                    $stm = $db->prepare('UPDATE xyz_user_info set `status`=:user_status where `user_id`=:user_id');
                    $stm->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                    $stm->bindValue(':user_status', Dao_UserState::DISABLE, PDO::PARAM_INT);
                    $ret = $stm->execute();

                    if($ret === false) {
                        throw new Exception('Disable user failed! ERROR:' . json_encode($stm->errorInfo()));
                    }
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