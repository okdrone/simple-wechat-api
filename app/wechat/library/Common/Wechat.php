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

    public function __construct()
    {
        $this->logger = new \Logger_App();
    }

    public function parseMessage($msg_str){

        $this->logger->info('This is in Wechat class.');
        $msg_obj = simplexml_load_string($msg_str, 'SimpleXMLElement', LIBXML_NOCDATA);

        $this->logger->info('Message ID: ' . $msg_obj->MsgID);
        $this->logger->info('Message Type: ' . $msg_obj->MsgType);
    }
}
