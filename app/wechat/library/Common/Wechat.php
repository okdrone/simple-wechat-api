<?php

/**************************************************
 * Copyright (c).
 * Filename: Wechat.php
 * Author: Wanbo Ge <gewanbo@gmail.com>
 * Create Date: 2018/5/31
 * Description:
 **************************************************/
namespace Common;

class Wechat
{
    protected $logger;
    protected $config;
    protected $request_msg;

    public function __construct($conf = [])
    {
        $this->logger = new \Logger_App();
        $this->config = $conf;
    }

    public function parseMessage($msg_str){

        $this->logger->info('This is in Wechat class.');
        $this->request_msg = simplexml_load_string($msg_str, 'SimpleXMLElement', LIBXML_NOCDATA);

        $this->logger->info('Message ID: ' . $this->request_msg->MsgID);
        $this->logger->info('Message Type: ' . $this->request_msg->MsgType);
    }

    public function responseHandler(){

        $response_msg = '';

        try {

            if (is_object($this->request_msg)) {
                switch ($this->request_msg->MsgType) {
                    case 'event':
                        $event = $this->request_msg->Event;
                        $todo_method = 'do' . ucfirst($event);

                        if(method_exists($this, $todo_method)){
                            $response_msg = $this->$todo_method($this->request_msg);
                        } else {
                            $this->logger->warning('Unsupported event type:' . $event);
                        }

                        break;

                    default:
                        $this->logger->warning('Unsupported message type:' . $this->request_msg->MsgType);
                }
            } else {
                throw new \Exception('The request message is not a legal object.');
            }
        } catch (\Exception $e){
            $this->logger->error($e->getMessage(), $e->getTrace());
        }

        return $response_msg;
    }

    private function doSubscribe($request_msg){

        $this->logger->info('This is subscribe event.');

        $openid = $request_msg->FromUserName;

        $uInfo = $this->getUserInfoByOpenId($openid);

        $this->logger->info('-----------------------------------');
        $this->logger->info(var_export($uInfo));

        $user = new \Service_Wechat_UserInfo();

        $user_info = new \Dao_UserInfo();
        $user_info->username = 'aaa';
        $user_info->nickname = 'bbb';

        $user->createUser($user_info);
    }

    private function doUnsubscribe(){

        $this->logger->info('This is unsubscribe event.');

    }

    public function getUserInfoByOpenId($openId){
        if (isset($openId)){
            $accessToken = AccessToken::getAccessToken($this->config);
            $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$accessToken.'&openid='.$openId.'&lang=zh_CN ';
            $info_result = Curl::get($url);
            if (!empty($info_result)){
                $userInfo_arr = json_decode($info_result,true);
                return $userInfo_arr;
            }
        }else{

            return false;
        }
        return false;
    }
}
