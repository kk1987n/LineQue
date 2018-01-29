<?php

namespace LineQue\Config;

/**
 * 数据库配置信息
 * @author Linko
 * @email 18716463@qq.com
 * @link https://github.com/kk1987n/LineQue.git
 * @version 1.0.0
 */
class Conf {

    public static function getConf() {
        return array(
            'DBTYPE' => 'Redis',
            'Redis' => self::getRedis()
        );
    }

    private static function getRedis() {
        return array(
            'SERVER' => '127.0.0.1',
            'PORT' => 6379,
            'PWD' => '',
            'DBNAME' => '0',
        );
    }

}
