<?php

/**************************************************
 * Copyright (c).
 * Filename: UserInfo.php
 * Author: Wanbo Ge <gewanbo@gmail.com>
 * Create Date: 2018/6/4
 * Description:
 **************************************************/
class Service_Wechat_UserInfo
{
    protected $logger;

    public function __construct()
    {
        $this->logger = new Logger_App();
    }

    public function createUser(){
        $this->logger->info('This is UserInfo model.!!!');
    }
}