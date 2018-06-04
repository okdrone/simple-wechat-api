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

    public function testAction() {
        var_dump('Class exists:');
        var_dump(class_exists('Common\Wechat'));

        $wechat = new Common\Wechat();
        $wechat->parseMessage('kkk');

        $app = Yaf_Application::app();

        $conf = $app->getConfig();

        var_dump($conf);

        $modules = $app->getModules();

        var_dump($modules);


        $loader = Yaf_Loader::getInstance();
        var_dump($loader);
        var_dump($loader->getLibraryPath());
        var_dump($loader->getLocalNamespace());
    }

    public function messageServiceAction(){

        try {

            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                if ($this->checkSignature()) {
                    exit($this->getRequest()->get('echostr'));
                }
            } else {

                $this->logger->info('Received a message from Wechat:');

                $xml_str = file_get_contents("php://input");

                $this->logger->info($xml_str);

                $wechat = new Common\Wechat();

                $wechat->parseMessage($xml_str);

                $this->logger->info($xml_str);
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
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
