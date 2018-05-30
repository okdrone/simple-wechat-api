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

    public function init() {
        Yaf_Dispatcher::getInstance()->autoRender(FALSE);
    }

    public function greetingAction(){
        echo "Greeting:";
        echo 'Hello world!';
    }

    public function logAction(){
        $logger = new \Wanbo\Logger\AppLogger();

        $logger->info('This is a info log.');

        echo 'OK';
    }
}