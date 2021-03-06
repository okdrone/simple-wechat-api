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

    private $wechat_conf = [];

    public function init() {
        Yaf_Dispatcher::getInstance()->autoRender(FALSE);

        $this->logger = new Logger_App();

        $this->wechat_conf = App_Config::getAppConfig('wechat', $this->deploy_mode);
    }

    public function testAction() {

        $app = Yaf_Application::app();

        $conf = $app->getConfig();

        var_dump($conf);

        $modules = $app->getModules();

        var_dump($modules);


        $loader = Yaf_Loader::getInstance();
        var_dump($loader);
        var_dump($loader->getLibraryPath());
        var_dump($loader->getLocalNamespace());


        var_dump('Database -----------');
        $db = \Common\DatabaseManager::getInstance('xyz');

        var_dump($db);

        $user = new Service_Wechat_UserInfo();

        var_dump($user);
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

                $wechat = new Common\Wechat($this->wechat_conf);

                $wechat->parseMessage($xml_str);
                $response_msg = $wechat->responseHandler();

                if(!empty($response_msg)){
                    exit($response_msg);
                }
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }

    private function checkSignature(){

        $token = $this->wechat_conf['wechat']['token'];

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
