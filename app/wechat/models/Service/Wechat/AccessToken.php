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
            $stm->bindValue(':username', $appid, PDO::PARAM_STR);
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
}