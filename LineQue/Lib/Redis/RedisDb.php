<?php

namespace LineQue\Lib\Redis;

use Exception;
use LineQue\Lib\DbInterface;
use LineQue\Lib\Status;

/**
 * 这一层是Redis和Job之间的层,用于job操作和redis实际操作隔离开
 * 
 * @author Linko
 * @email 18716463@qq.com
 * @link https://github.com/kk1987n/LineQue.git
 * @version 1.0.0
 *
 */
class RedisDb implements DbInterface {

    /**
     * @var redis实例
     */
    public $redis = null;

    /**
     * redis中默认有16个数据库,默认采用0号
     * @var type 
     */
    private $dbId = 0;

    public function __construct($redisConf, $dbId = 0) {
        $this->redis = new RedisD($redisConf);
        $this->dbId = $dbId;
    }

    /**
     * 获取一个job,本方法不出队
     * @param type $queue
     * @return type
     */
    public function getJob($queue) {
        $job = $this->redis->getListI0('lineque:' . $queue); //返回最后位置元素
        if ($job) {
            return json_decode($job, true);
        }
        return null;
    }

    /**
     * 出队一个job
     * @param type $queue
     * @return type
     */
    public function popJob($queue) {
        $job = $this->redis->popListI0('lineque:' . $queue);
        if ($job) {
            return json_decode($job, true);
        }
        return;
    }

    /**
     * 更新job状态
     * @param type $jobid
     * @param type $status
     * @param type $otherinfo
     * @return type
     */
    public function updateJobStatus($jobid, $status, $otherinfo = null) {
        return $this->redis->setKeyVal('lineque:job:' . Status::statusToString($status) . ':' . $jobid, json_encode(array('status' => $status, 'utime' => time(), 'job' => $otherinfo)));
    }

    /**
     * 删除一个job
     * @param type $jobid
     * @param type $status
     * @return type
     */
    public function delByJobid($jobid, $status) {
        return $this->redis->delKey('lineque:job:' . Status::statusToString($status) . ':' . $jobid);
    }

    /**
     * 新增一个job
     * @param type $key
     * @param type $class
     * @param type $args
     * @param type $id
     * @return boolean
     * @throws Exception
     */
    public function addJobToList($key, $class, $args = null, $id = null) {
        if (is_null($id)) {
            $id = $this->generateJobId();
        }
        if ($args !== null && !is_array($args)) {
            throw new Exception('参数必须传递数组');
        }
        if ($this->redis->pushToList('lineque:' . $key, array('class' => $class, 'args' => $args, 'id' => $id, 'utime' => microtime(true)))) {
            return $id;
        }
        return false;
    }

    /**
     * 一个整型的key/value,增加他的值
     * @param type $key
     * @param type $step
     * @return type
     */
    public function incrStat($status, $step = 1) {
        return $this->redis->incrStat('lineque:stat:' . Status::statusToString($status), $step);
    }

    /**
     * 一个整型的key/value,减少他的值
     * @param type $key
     * @param type $step
     * @return type
     */
    public function decrStat($status, $step = 1) {
        return $this->redis->decrStat('lineque:stat:' . Status::statusToString($status), $step);
    }

    /**
     * 关闭数据库连接
     * @return type
     */
    public function closeDbInstance() {
        return $this->redis->closeDbInstance();
    }

    /**
     * 生成唯一ID
     * @return type
     */
    private function generateJobId() {
        return md5(uniqid('', true));
    }

}
