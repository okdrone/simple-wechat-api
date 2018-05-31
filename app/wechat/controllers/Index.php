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

    public function init() {
        Yaf_Dispatcher::getInstance()->autoRender(FALSE);

        $this->logger = new Logger_App();
    }

    public function greetingAction(){
        echo "Greeting:";
        echo 'Hello world!';
    }

    public function messageServiceAction(){

        if ($_SERVER['REQUEST_METHOD'] == 'GET'){
            $this->checkSignature();
        } else {

            $this->logger->info('This is a info log.');

            $xml_str = file_get_contents("php://input");

            $this->logger->info($xml_str);
        }

        echo 'This is message service';
    }

    private function checkSignature(){
        $conf = App_Config::getConfig('wechat');

        var_dump($conf);

        $signature = $this->getRequest()->get("signature", 0);
        $timestamp = $this->getRequest()->get("timestamp", 0);
        $nonce = $this->getRequest()->get("nonce", 0);
        $echostr = $this->getRequest()->get('echostr');

        $tmpArr = array($timestamp, $nonce);

        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        $this->logger->info('signature:' . $signature);
        $this->logger->info('sigStr:' . $tmpStr);
        $this->logger->info($echostr);

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}