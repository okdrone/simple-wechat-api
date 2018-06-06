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
        if(isset($conf['wechat'])) {
            $this->config = $conf['wechat'];
        }
    }

    public function parseMessage($msg_str){

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

                    case 'text':
                        $response_msg = $this->responseTextMessage($this->request_msg, 'Good!');
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

        $userOpenInfo = new \Dao_UserOpenInfo();
        $userOpenInfo->open_type = 1; // 1: Wechat
        $userOpenInfo->open_app_id = $this->config['appid'];
        $userOpenInfo->open_user_id = $openid;

        $userInfoService = new \Service_Wechat_UserInfo();
        $user_info = $userInfoService->getUserByOpenInfo($userOpenInfo);

        if($user_info !== false && $user_info instanceof \Dao_UserInfo){
            if($user_info->status === 1){
                $userInfoService->enableOpenUser($userOpenInfo);
            }

        } else {
            $user_info = $this->getUserInfoByOpenId($openid);

            if($user_info !== false){
                $userInfoService->createUser($user_info, $userOpenInfo);
            } else {
                $this->logger->error('There has error when getting user info from Wechat.');
            }
        }

        return $this->responseTextMessage($request_msg, 'Welcome!');
    }

    private function doUnsubscribe($request_msg){

        $this->logger->info('This is unsubscribe event.');

        $openid = $request_msg->FromUserName;

        $userOpenInfo = new \Dao_UserOpenInfo();
        $userOpenInfo->open_type = 1; // 1: Wechat
        $userOpenInfo->open_app_id = $this->config['appid'];
        $userOpenInfo->open_user_id = $openid;

        $user = new \Service_Wechat_UserInfo();
        $user->disableOpenUser($userOpenInfo);
    }

    private function responseTextMessage($request_msg, $msg){
        $this->logger->info('This is a text message.');

        $openid = $request_msg->FromUserName;

        $userOpenInfo = new \Dao_UserOpenInfo();
        $userOpenInfo->open_type = 1; // 1: Wechat
        $userOpenInfo->open_app_id = $this->config['appid'];
        $userOpenInfo->open_user_id = $openid;

        $user = new \Service_Wechat_UserInfo();
        $user_info = $user->getUserByOpenInfo($userOpenInfo);

        $this->logger->error('User state:' . $user_info->status);

        return sprintf($this->responseMsgTempletes['text'], $openid, $request_msg->ToUserName, time(), $msg);
    }

    public function getUserInfoByOpenId($openId){
        $userInfo = false;

        try {

            if (empty($openId))
                throw new \Exception('The input openId is empty!');

            $accessToken = AccessToken::getAccessToken($this->config);

            if (empty($accessToken))
                throw new \Exception('The AccessToken is empty!');

            $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $accessToken . '&openid=' . $openId . '&lang=zh_CN ';
            $info_result = Curl::get($url);

            if ($info_result === false)
                throw new \Exception('There was something error when calling Wechat API to get user info.');


            $userInfo_arr = json_decode($info_result, true);
            if (!isset($userInfo_arr['errcode'])) {
                $userInfo = new \Dao_UserInfo();
                $userInfo->username = 'wx_' . $userInfo_arr['nickname'];
                $userInfo->nickname = $userInfo_arr['nickname'];
                $userInfo->icon = $userInfo_arr['headimgurl'];
            } else {
                throw new \Exception(sprintf('Response error [%s] when calling Wechat API.', json_encode($userInfo_arr)));
            }

        } catch (\Exception $e){
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
        return $userInfo;
    }

    private $responseMsgTempletes = [
        'text' => '<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                   </xml>'
    ];
}
