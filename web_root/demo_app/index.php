<?php
/**************************************************
 * Copyright (c).
 * Filename: index.php
 * Author: Wanbo Ge <gewanbo@gmail.com>
 * Create Date: 2018/5/28
 * Description:
 **************************************************/

define("APPLICATION_PATH",  dirname(__FILE__));

$app = APP_Core::init();

$app->bootstrap()
    ->run();