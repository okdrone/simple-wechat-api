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

        $this->logger->info('This is a info log.');

        echo 'This is message service';

        $xml_str = file_get_contents("php://input");

        $this->logger->info($xml_str);
    }
}