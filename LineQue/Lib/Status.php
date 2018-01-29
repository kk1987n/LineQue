<?php

namespace LineQue\Lib;

/**
 * Job的状态类
 * 
 * @author Linko
 * @email 18716463@qq.com
 * @link https://github.com/kk1987n/LineQue.git
 * @version 1.0.0
 */
class Status {

    const STATUS_WAITING = 1; //新job,处于等待状态
    const STATUS_RUNNING = 2; //出队执行
    const STATUS_FAILED = 3; //执行失败
    const STATUS_COMPLETE = 4; //执行完成

    /**
     * 根据状态值输出状态字符串
     * @param type $status
     * @return string
     */

    public static function statusToString($status) {
        switch ($status) {
            case self::STATUS_WAITING:
                return 'WATTING';
            case self::STATUS_RUNNING:
                return 'RUNNING';
            case self::STATUS_FAILED:
                return 'FAILED';
            case self::STATUS_COMPLETE:
                return 'COMPLETE';
        }
        return '';
    }

}
