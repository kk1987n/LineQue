<?php

namespace LineQue\Lib;

use LineQue\Config\Conf;

/**
 * 显示命令行
 * 记录日志
 * 
 * @author Linko
 * @email 18716463@qq.com
 * @link https://github.com/kk1987n/LineQue.git
 * @version 1.0.0
 */
class ProcLine {

    private $logFile;
    private $initDisplay = array();

    public function __construct($logFile = null) {
        $this->logFile = $logFile;
    }

    /**
     * 显示UI方框.
     *
     * @return void
     */
    public function displayUI() {
        $this->EchoAndLog("┌───────────────────────────── LineQue ─────────────────────────────┐" . PHP_EOL);
        $this->EchoAndLog("├───────────────────────────────────────────── LineQueVersion:" . Conf::$VERSION . " ┤" . PHP_EOL);
        $this->EchoAndLog("│感谢您选择LineQue                                                  │" . PHP_EOL);
        $this->EchoAndLog("│LineQue是一款基于PHP的简单队列程序                                 │" . PHP_EOL);
        $this->EchoAndLog("│本程序参考了很多PHP_RESQUE思想                                     │" . PHP_EOL);
        $this->EchoAndLog("│需要更多帮助,请访问https://github.com/kk1987n/LineQue.git                 │" . PHP_EOL);
        $this->showInitDisplay();
        $this->EchoAndLog("├──────────────────────────────────────────────── PHPVersion:" . PHP_VERSION . " ┤" . PHP_EOL);
        $this->EchoAndLog("└───────────────────────────────────────────────────────────────────┘" . PHP_EOL);
        $this->initDisplay = null;
    }

    private function showInitDisplay() {
        foreach ($this->initDisplay as $string) {
            $lenth = strlen($string);
            for ($i = 0; $i < 67 - $lenth; $i++) {//结尾字符串补充这么多空格
                $string .= ' ';
            }
            $this->EchoAndLog("│" . $string . "│" . PHP_EOL);
        }
    }

    public function initDisplay($string) {
        $this->initDisplay[] = $string;
    }

    /**
     * Safe Echo.
     *
     * @param $msg
     */
    public function EchoAndLog($msg) {
        echo $msg;
        $this->log($msg);
    }

    /**
     * Log.
     *
     * @param string $msg
     * @return void
     */
    public function log($msg) {
        if ($this->logFile) {
            file_put_contents((string) $this->logFile, date('Y-m-d H:i:s') . ' ' . $msg, FILE_APPEND | LOCK_EX);
        }
    }

}
