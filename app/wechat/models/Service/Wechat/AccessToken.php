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
    protected $logger;

    public function __construct()
    {
        $this->logger = new Logger_App();
    }

    public function getAccessTokenByApp($app_id){

        $accessToken = new Dao_AccessToken();

        try {

            $db = \Common\DatabaseManager::getInstance('xyz');

            if ($db instanceof PDO) {
                $stm = $db->prepare('SELECT * FROM xyz_wechat_access_token where app_id=:app_id and `type`=1');
                $stm->bindParam(':app_id', $app_id, PDO::PARAM_STR);
                $stm->execute();

                // When using rowCount read official document first: http://php.net/manual/en/pdostatement.rowcount.php
                $gotRows = $stm->rowCount();

                $this->logger->info('Got rows:' . $gotRows);

                if ($gotRows > 0){
                    $ret = $stm->fetch(PDO::FETCH_ASSOC);

                    if ($ret) {
                        $accessToken->id = $ret['id'];
                        $accessToken->access_token = $ret['access_token'];
                        $accessToken->create_ts = $ret['create_ts'];
                        $accessToken->expire_ts = $ret['expire_ts'];
                    } else {
                        throw new Exception('There was error when fetch AccessToken. Error:' . var_export($stm->errorInfo()));
                    }
                }

            } else {
                throw new Exception('The $db is not instance of PDO when connecting to database.');
            }
        } catch (Exception $e){
            $this->logger->error($e->getMessage(), $e->getTrace());
        }

        return $accessToken;
    }

    public function setAccessTokenByApp(Dao_AccessToken $accessToken){

        $ret = false;

        try {

            $db = \Common\DatabaseManager::getInstance('xyz');

            if ($db instanceof PDO) {
                $stm = $db->prepare('INSERT INTO xyz_wechat_access_token (`app_id`, `type`, `access_token`, `create_ts`, `expire_ts`) VALUE (:app_id, :type, :access_token, :create_ts, :expire_ts)');
                $stm->bindParam(':app_id', $accessToken->app_id, PDO::PARAM_STR);
                $stm->bindParam(':type', $accessToken->type, PDO::PARAM_INT);
                $stm->bindParam(':access_token', $accessToken->access_token, PDO::PARAM_STR);
                $stm->bindParam(':create_ts', $accessToken->create_ts, PDO::PARAM_INT);
                $stm->bindParam(':expire_ts', $accessToken->expire_ts, PDO::PARAM_INT);
                $ret = $stm->execute();
            } else {
                new Exception('The $db is not instance of PDO when connecting to database.');
            }

        } catch (Exception $e){
            $this->logger->error($e->getMessage(), $e->getTrace());
        }

        return $ret;
    }
}