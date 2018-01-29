<?php

namespace LineQue\Worker;

use LineQue\Lib\dbJobInstance;
use LineQue\Lib\ProcLine;

/**
 * 执行job的子进程
 * 本进程主要获取job,更新job等操作
 * 实际最终执行用户app的,也就是执行run方法的,为本进程开启的子进程
 * 这样做可以保护本进程,也可以获取run方法有没有意外终止导致执行失败
 * 
 * @author Linko
 * @email 18716463@qq.com
 * @link https://github.com/kk1987n/LineQue.git
 * @version 1.0.0
 */
class Worker {

    private $Que; //队列名
    private $interval; //循环时间间隔
    private $DbInstance = null; //数据库操作实例
    private $procLine = null; //日志记录

    public function __construct($Que, $interval) {
        $this->Que = $Que;
        $this->interval = $interval;
        $this->DbInstance = new dbJobInstance();
        $this->procLine = new ProcLine(LOGPATH);
    }

    public function startWork() {
        //此处已经是子进程的子进程了,可以在此处进行下一步逻辑了
        $this->procLine->EchoAndLog('子进程开始循环PID=' . posix_getpid() . PHP_EOL);
        while (1) {
            pcntl_signal_dispatch(); //查看信号队列
            $job = $this->DbInstance->getJob($this->Que); //此时可以执行一个新job
            $this->doAJob($job); //执行一个job
            usleep($this->interval * 1000000); //休眠多少秒
        }
    }

    /**
     * 检查job
     * @param type $job
     * @return boolean
     */
    private function doAJob($job) {
        if ($job) {
            $canDo = isset($job['args']['lineDoTime']) && $job['args']['lineDoTime'] > 0 ? ($job['args']['lineDoTime'] <= time() ? true : false) : true; //如果设置了执行时间,则在执行时间之后才出队
            if ($canDo) {
                $job = $this->DbInstance->popJob($this->Que); //将这个job出队
                $this->procLine->EchoAndLog('子进程即将开始一个新Job,PID=' . posix_getpid() . 'JobInfo:' . json_encode($job) . PHP_EOL);
                try {
                    $this->DbInstance->run($job); //执行
                } catch (Exception $ex) {
                    $this->procLine->EchoAndLog('新Job执行发生异常PID=' . posix_getpid() . ':' . json_encode($ex) . PHP_EOL);
                }
                $this->procLine->EchoAndLog('新Job执行结束PID=' . posix_getpid() . ',JobId=' . $job['id'] . PHP_EOL);
            } else {
                $this->procLine->EchoAndLog('子进程PID=' . posix_getpid() . '获取到了一个job,但由于未到执行时间,跳过此job,JobInfo:' . json_encode($job) . PHP_EOL);
            }
        }
        return true;
    }

}
