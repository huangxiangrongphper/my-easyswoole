<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use App\Crontab\TaskOne;
use App\Crontab\TaskTwo;
use App\Crontab\TaskVideo;
use App\Libs\Process\Consumer;
use App\Models\Es\EsClient;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Crontab\Crontab;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use Illuminate\Database\Capsule\Manager as Capsule;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');

        // 初始化数据库
        $dbConf = Config::getInstance()->getConf('MYSQL');

        $capsule = new Capsule();

        // 创建链接
        $capsule->addConnection($dbConf);

        // 设置全局静态可访问
        $capsule->setAsGlobal();

        // 启动Eloquent
        $capsule->bootEloquent();
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.

        $allNum = 3;
        for ($i = 0 ;$i < $allNum;$i++){
            ServerManager::getInstance()->getSwooleServer()->addProcess((new Consumer("consumer_{$i}"))->getProcess());
        }


        // 容器中注入连接es的实例
        Di::getInstance()->set("ES",EsClient::getInstance());


        /**
         * **************** Crontab任务计划 **********************
         */
        // 开始一个定时任务计划
//        Crontab::getInstance()->addTask(TaskOne::class);
        // 开始一个定时任务计划
//        Crontab::getInstance()->addTask(TaskTwo::class);
        // 开始一个定时任务计划
//        Crontab::getInstance()->addTask(TaskVideo::class);


//        $taskVideo = new TaskVideo();

        /**
         * **************** swoole任务计划 **********************
         */
//        $register->add(EventRegister::onWorkerStart, function (\swoole_server $server, $workerId) {
//
//            if($workerId == 0)
//            {
//                \EasySwoole\Component\Timer::getInstance()->loop(2 * 1000, function () use($server,$workerId)  {
//                        TaskVideo::run($server,0,$workerId);
//                });
//            }
//        });
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}
