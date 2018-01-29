#!/usr/bin/env php
<?php
/**
 * 主入口
 * @author Linko
 * @email 18716463@qq.com
 * @link https://github.com/kk1987n/LineQue.git
 * @version 1.0.0
 */
define('LineQue', __DIR__); //LineQue目录绝对路径,autoload中用到了,要加载类
define('APP', __DIR__ . '/App'); //APP目录下为您的job类,您的job只能放在那儿,此处定义绝对路径,插入队列后,取出初始化job类时要用到这个常量
// 只允许在cli下面运行  
if (php_sapi_name() != "cli") {
    die("仅支持命令行模式\n");
}
if (!class_exists('\\Redis', false)) {
    die('必须开启Redis扩展' . PHP_EOL);
}
$QUE = getenv('QUE'); //队列名
$INTERVAL = getenv('INTV'); //worker循环间隔
$queue = $QUE ? $QUE : 'default'; //默认队列名
define('LOGPATH', LineQue . '/LineQue.log'); //系统日志文件路径,默认放在框架目录下

require_once __DIR__ . '/Lib/Autoload.php';

if (!class_exists('LineQue\Lib\Autoload', false)) {
    die('自动加载类错误' . PHP_EOL);
}
if (!function_exists('pcntl_fork')) {
    die('您的运行环境不支持pcntl_fork函数' . PHP_EOL);
}
LineQue\Lib\Autoload::start(); //注册自动加载和错误处理函数

$worker = new \LineQue\Worker\Master($queue, $INTERVAL > 0 ? $INTERVAL : 5, 0); //主进程
$worker->startWork();
