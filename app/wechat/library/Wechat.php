<?php

/**************************************************
 * Copyright (c).
 * Filename: Wechat.php
 * Author: Wanbo Ge <gewanbo@gmail.com>
 * Create Date: 2018/5/31
 * Description:
 **************************************************/

class Wechat
{
    protected $logger;

    public function __construct()
    {
        $this->logger = new \Logger_App();
    }

    public function parseMessage($msg_str){

        $this->logger->info('This is in Wechat class.');
    }
}