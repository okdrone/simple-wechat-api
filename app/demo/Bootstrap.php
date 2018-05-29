<?php

/**************************************************
 * Copyright (c).
 * Filename: Bootstrap.php
 * Author: Wanbo Ge <gewanbo@gmail.com>
 * Create Date: 2018/5/29
 * Description: All the methods named with prefix "_ini"
 * will be called according to their declare order.
 **************************************************/
class Bootstrap extends Yaf_Bootstrap_Abstract
{
    public function _initConfig(Yaf_Dispatcher $dispatcher) {
        //var_dump(__METHOD__);
        $dispatcher->setDefaultController('Index');
    }
    public function _initPlugin(Yaf_Dispatcher $dispatcher) {
        //var_dump(__METHOD__);
    }

    public function _initRoute(Yaf_Dispatcher $dispatcher){
        {
            $route = new Yaf_Route_Rewrite(
                '/demo/:xaction',
                array(
                    "module"    =>  "Index",
                    "controller"=>  "Index",
                    "action"    =>  ":xaction",
                )
            );
            $dispatcher->getInstance()->getRouter()->addRoute("my_route", $route);
        }
    }
}