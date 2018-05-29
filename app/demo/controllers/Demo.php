<?php

/**************************************************
 * Copyright (c).
 * Filename: Index.php
 * Author: Wanbo Ge <gewanbo@gmail.com>
 * Create Date: 2018/5/28
 * Description:
 **************************************************/
class Controller_Demo extends Yaf_Controller_Abstract
{

    public function init() {
        Yaf_Dispatcher::getInstance()->autoRender(FALSE);
    }

    public function greeting(){
        echo 'Hello world!';
    }
}