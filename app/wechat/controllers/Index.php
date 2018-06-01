<?php

/**************************************************
 * Copyright (c).
 * Filename: Index.php
 * Author: Wanbo Ge <gewanbo@gmail.com>
 * Create Date: 2018/5/28
 * Description:
 **************************************************/
class Controller_Index extends Yaf_Controller_Abstract
{
    protected $logger;

    // Only can be "production" or "debug".
    protected $deploy_mode = 'debug';

    public function init() {
        Yaf_Dispatcher::getInstance()->autoRender(FALSE);

        $this->logger = new Logger_App();
    }

    public function messageServiceAction(){

        if ($_SERVER['REQUEST_METHOD'] == 'GET'){
            if($this->checkSignature()) {
                exit($this->getRequest()->get('echostr'));
            }
        } else {

            $this->logger->info('Received a message from Wechat:');

            $xml_str = file_get_contents("php://input");

            $this->logger->info($xml_str);

            $this->logger->info("Class Wechat:" . class_exists('Wechat'));
            $this->logger->info("Class \\Wechat:" . class_exists('\Wechat'));

//            $wechat = new Wechat();
//
//            $wechat->parseMessage($wechat);

            $this->logger->info($xml_str);
        }
    }

    private function checkSignature(){
        $conf = App_Config::getConfig('wechat', $this->deploy_mode);

        $token = $conf['wechat']['token'];

        $signature = $this->getRequest()->get("signature", 0);
        $timestamp = $this->getRequest()->get("timestamp", 0);
        $nonce = $this->getRequest()->get("nonce", 0);

        $tmpArr = array($token, $timestamp, $nonce);

        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        $this->logger->info('signature:' . $signature);
        $this->logger->info('sigStr:' . $tmpStr);

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}