<?php

require_once dirname(__FILE__) . '/Cron/CronExpression.php';

$cronExpression = CronExpression::factory("0 */12 * * *");

$date = date_create('2016-09-18 12:00:42');
var_dump($date);
var_dump($cronExpression->getPreviousRunDate($date));
var_dump($cronExpression->getPreviousRunDate($date, 0 , true));
var_dump($cronExpression->getNextRunDate($date, 0 , true));
var_dump($cronExpression->isDue($date));
//var_dump($cronExpression->getMultipleRunDates(3));
echo "------\r\n";

$timerCron = CronExpression::factory("*/2 * * * *");
$timerCurDate = $timerCron->getPreviousRunDate('now', 0, true);
$timerNextDate = $timerCron->getNextRunDate('now');
echo "当前定时器执行时间:" . date_format($timerCurDate, 'Y-m-d H:i') .
    ", 下一执行时间: " . date_format($timerNextDate, 'Y-m-d H:i') . "\r\n" ;

$taskCron = CronExpression::factory("*/5 17 * * *");
$taskNextDate = $taskCron->getNextRunDate($timerCurDate, 0, true);
echo "任务下一执行时间:" . date_format($taskNextDate, 'Y-m-d H:i') . "\r\n" ;

if ($timerCurDate <= $taskNextDate && $taskNextDate < $timerNextDate) {
    echo "执行任务...\r\n";
} else {
    echo "未到执行时间\r\n";
}
