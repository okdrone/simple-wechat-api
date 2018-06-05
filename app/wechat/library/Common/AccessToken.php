<?php
/**************************************************
 * Copyright (c).
 * Filename: AccessToken.php
 * Author: Wanbo Ge <gewanbo@gmail.com>
 * Create Date: 2018/6/4
 * Description:
 **************************************************/

namespace Common;


class AccessToken
{
    private static $accessToken = [];

    public static function getAccessToken($conf){

        $logger = new \Logger_App();

        $appId = $conf['appid'];

        /**
         * 1. Get from local
         */

        if(isset(self::$accessToken[$appId])){
            $tokenData = self::$accessToken[$appId];
            if(self::isValidToken($tokenData)){
                return $tokenData['access_token'];
            }
        }

        $logger->warning('Can not found AccessToken from local storage.');


        /**
         * 2. Get from database
         */
        $serviceAccessToken = new \Service_Wechat_AccessToken();

        $accessTokenObj = $serviceAccessToken->getAccessTokenByApp($appId);
        if($accessTokenObj instanceof \Dao_AccessToken && !empty($accessTokenObj->access_token)) {
            $tokenData = array(
                'access_token' => $accessTokenObj->access_token,
                'create_ts' => $accessTokenObj->create_ts,
                'expire_ts' => $accessTokenObj->expire_ts
            );

            if(self::isValidToken($tokenData)){
                return $tokenData['access_token'];
            }
        }

        $logger->warning('Can not found AccessToken from database.');

        /**
         * 3. Get from Wechat API and sync to database
         */

        $current_time = time();
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s', $appId, $conf['appsecret']);
        $res = Curl::get($url);

        $logger->info('Getting AccessToken Response:'. $res);

        if (!empty($res)) {
            $ret = json_decode($res, true);
            $strAccessToken = $ret['access_token'];
            self::$accessToken[$appId] = array(
                'access_token' => $strAccessToken,
                'create_ts' => $current_time,
                'expire_ts' => $ret['expires_in'],
                'app_id' => $appId,
                'type' => 1,
            );

            $objAccessToken = new \Dao_AccessToken();
            $objAccessToken->access_token = $strAccessToken;
            $objAccessToken->create_ts = $current_time;
            $objAccessToken->expire_ts = $ret['expires_in'];
            $objAccessToken->type = 1;
            $objAccessToken->app_id = $appId;

            $serviceAccessToken->setAccessTokenByApp($objAccessToken);

            return $strAccessToken;
        } else {
            return '';
        }
    }

    private static function isValidToken(array $tokenData){
        return $tokenData['create_ts'] + $tokenData['expire_ts'] > time() + 100;
    }
}