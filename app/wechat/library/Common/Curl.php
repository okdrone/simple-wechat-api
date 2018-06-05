<?php
/**************************************************
 * Copyright (c).
 * Filename: Curl.php
 * Author: Wanbo Ge <gewanbo@gmail.com>
 * Create Date: 2018/6/4
 * Description:
 **************************************************/

namespace Common;


class Curl
{
    private static $default_opts = array(
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_TIMEOUT => 10
    );

    private static function send($opts)
    {
        $ch = curl_init();
        curl_setopt_array($ch, $opts);

        $result = curl_exec($ch);
        if(curl_errno($ch)){
            $logger = new \Logger_App();
            $logger->error(var_export($opts));
            $logger->error('CURL ERROR:' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    public static function get($url){
        $arr_opts = array_merge(self::$default_opts, array(
            CURLOPT_URL => $url
        ));

        return self::send($arr_opts);
    }

    public static function post($url, $data){
        $arr_opts = array_merge(self::$default_opts, array(
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => $data
        ));

        return self::send($arr_opts);
    }
}