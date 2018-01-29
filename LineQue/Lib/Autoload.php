<?php

namespace LineQue\Lib;

use const APP;
use const LineQue;
use const LOGPATH;

/**
 * 自动加载类
 * 
 * @author Linko
 * @email 18716463@qq.com
 * @link https://github.com/kk1987n/LineQue.git
 * @version 1.0.0
 */
class Autoload {

    public static function start() {
        spl_autoload_register('\LineQue\Lib\Autoload::autoload');
        register_shutdown_function('\LineQue\Lib\Autoload::fatalError');
        set_error_handler('\LineQue\Lib\Autoload::appError');
        set_exception_handler('\LineQue\Lib\Autoload::appException');
    }

    /**
     * 类库自动加载
     * @param string $class 对象类名
     * @return void
     */
    public static function autoload($class) {
        // 检查是否存在映射
        if (false !== strpos($class, '\\')) {
            $filename = str_replace('\\', '/', $class) . '.php';
            $FirstNamespace = substr($filename, 0, strpos($filename, '/'));
            switch ($FirstNamespace) {
                case 'LineQue':
                    $filename = dirname(LineQue) . '/' . $filename;
                    break;
                default :
                    $filename = dirname(APP) . '/' . $filename;
            }
            file_put_contents('/data/LineQueA.log', $filename . PHP_EOL);
//            print_r(is_file($filename) . $filename . '<br/>');
            if (is_file($filename)) {
                include $filename;
            }
        }
    }

    /**
     * 严重错误
     */
    public static function fatalError() {
        $e = error_get_last();
        if ($e) {
            $logLine = new ProcLine(LOGPATH);
            $logLine->EchoAndLog('---------------------发生严重错误---------------------' . PHP_EOL);
            $logLine->EchoAndLog(json_encode($e) . PHP_EOL);
        }
    }

    /**
     * 程序错误
     * @param type $errno
     * @param type $errstr
     * @param type $errfile
     * @param type $errline
     */
    public static function appError($errno, $errstr, $errfile, $errline) {
        if ($errno) {
            $errorStr = "$errstr " . $errfile . " 第 $errline 行.";
            $logLine = new ProcLine(LOGPATH);
            $logLine->EchoAndLog('---------------------发生程序错误---------------------' . PHP_EOL);
            $logLine->EchoAndLog($errno . ':' . $errorStr . PHP_EOL);
        }
    }

    /**
     * 程序异常
     * @param type $e
     */
    public static function appException($e) {
        if ($e) {
            $error = array();
            $error['message'] = $e->getMessage();
            $trace = $e->getTrace();
            if ('E' == $trace[0]['function']) {
                $error['file'] = $trace[0]['file'];
                $error['line'] = $trace[0]['line'];
            } else {
                $error['file'] = $e->getFile();
                $error['line'] = $e->getLine();
            }
            $error['trace'] = $e->getTraceAsString();
            $logLine = new ProcLine(LOGPATH);
            $logLine->EchoAndLog('---------------------发生异常---------------------' . PHP_EOL);
            $logLine->EchoAndLog(json_encode($error) . PHP_EOL);
        }
    }

}
