<?php

/**************************************************
 * Copyright (c).
 * Filename: AccessToken.php
 * Author: Wanbo Ge <gewanbo@gmail.com>
 * Create Date: 2018/6/4
 * Description:
 **************************************************/
class Service_Wechat_AccessToken
{

    public function getAccessTokenByApp($appid){

        $accessToken = new Dao_AccessToken();

        $db = \Common\DatabaseManager::getInstance('xyz');

        if($db instanceof PDO){
            $stm = $db->query('SELECT * FROM xyz_wechat_access_token where appid=:appid and `type`=1');
            $stm->bindParam(':appid', $appid, PDO::PARAM_STR);
            $stm->execute();
            $ret = $stm->fetch(PDO::FETCH_ASSOC);

            if($ret) {
                $accessToken->id = $ret['id'];
                $accessToken->access_token = $ret['access_token'];
                $accessToken->create_ts = $ret['create_ts'];
                $accessToken->expire_ts = $ret['expire_ts'];
            }
        }

        return $accessToken;
    }

    public function setAccessTokenByApp(Dao_AccessToken $accessToken){

        $ret = false;

        $db = \Common\DatabaseManager::getInstance('xyz');

        if($db instanceof PDO){
            $stm = $db->prepare('INSERT INTO xyz_wechat_access_token (`app_id`, `type`, `access_token`, `create_ts`, `expire_ts`) VALUE (:app_id, :type, :access_token, :create_ts, :expire_ts)');
            $stm->bindParam(':app_id', $accessToken->app_id, PDO::PARAM_STR);
            $stm->bindParam(':type', $accessToken->type, PDO::PARAM_INT);
            $stm->bindParam(':access_token', $accessToken->access_token, PDO::PARAM_STR);
            $stm->bindParam(':create_ts', $accessToken->create_ts, PDO::PARAM_INT);
            $stm->bindParam(':expire_ts', $accessToken->expire_ts, PDO::PARAM_INT);
            $ret = $stm->execute();
        }

        return $ret;
    }
}