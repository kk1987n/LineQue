<?php

namespace LineQue\Lib\Redis;

use Redis;

/**
 * Redis基本操作类
 * 需要开启Redis扩展
 * @author Linko
 * @email 18716463@qq.com
 * @link https://github.com/kk1987n/LineQue.git
 * @version 1.0.0
 */
class RedisD {

    private $redis;

    public function __construct($redisConf) {
        $this->redis = $this->getRedis($redisConf);
    }

    public function getRedis($redisConf) {
        if ($redisConf && class_exists('Redis')) {
            $redis = new Redis();
            $redis->connect($redisConf['HOST'], $redisConf['PORT']);
            !$redisConf['PWD'] ?: $redis->auth($redisConf['PWD']);
            !$redisConf['DBNAME'] ?: $redis->select($redisConf['DBNAME']);
            if ($redis) {
                return $redis;
            } else {
                die('Redis初始化失败');
            }
        } else {
            die('Redis配置无效');
        }
    }

    /**
     * 插入到队列
     * @param type $listName 队列明
     * @param type $item 插入的内容
     * @return boolean
     */
    public function pushToList($listName, $item) {
        $encodedItem = json_encode($item);
        if ($encodedItem === false) {
            return false;
        }
        $length = $this->redis->rPush($listName, $encodedItem);
        if ($length < 1) {
            return false;
        }
        return true;
    }

    public function getListI0($list) {
        return $this->redis->lindex($list, 0);
    }

    public function popListI0($list) {
        return $this->redis->lPop($list);
    }

    public function setKeyVal($key, $val) {
        return $this->redis->set($key, $val);
    }

    public function delKey($key) {
        return $this->redis->delete($key);
    }

    public function incrStat($key, $step = 1) {
        return (bool) $this->redis->incrby($key, $step);
    }

    public function decrStat($key, $step = 1) {
        return (bool) $this->redis->decrby($key, $step);
    }

    public function closeDbInstance() {
        $this->redis->close();
        $this->redis = null;
        return true;
    }

}
