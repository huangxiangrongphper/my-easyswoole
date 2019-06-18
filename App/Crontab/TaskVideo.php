<?php

namespace App\Crontab;

use App\Libs\Redis\RedisTool;
use App\Models\Video;
use EasySwoole\EasySwoole\Crontab\AbstractCronTask;

/**
 * 定时静态化缓存视频数据内容
 * Class TaskVideo
 *
 * @package \App\Crontab
 */
class TaskVideo extends AbstractCronTask
{
    public static function getRule(): string
    {
        // TODO: Implement getRule() method.
        // 定时周期 （每一分钟一次）
        return '*/1 * * * *';
    }

    public static function getTaskName(): string
    {
        // TODO: Implement getTaskName() method.
        // 定时任务名称
        return 'taskVideo';
    }

    /**
     * @param \swoole_server $server
     * @param int            $taskId
     * @param int            $fromWorkerId
     * @param null           $flags
     */
    static function run(\swoole_server $server, int $taskId, int $fromWorkerId,$flags=null)
    {
        // TODO: Implement run() method.
        $catIds = array_keys(\Yaconf::get("category.cats"));
        array_unshift($catIds,0);

        $video = new Video();

        //将视频资源以json格式缓存起来
        foreach ($catIds as $catId )
        {
            $condition = [];
            if(!empty($catId))
            {
                $condition['cat_id'] = $catId;
            }

            try {
                $data = $video->getVideoCacheData($condition);
            }catch (\Exception $e)
            {
                // TODO 报警通知
                $data = [];
            }

            if(empty($data))
            {
                continue;
            }

            foreach ($data as &$list)
            {
                $list['create_time'] = date("Ymd H:i:s",$list['create_time']);

                $list['video_duration'] = gmstrftime("%H:%m:%s",$list['video_duration']);
            }

            // 文件存储
//            $flag = file_put_contents(EASYSWOOLE_ROOT."/Public/video/json/" .$catId.".json",json_encode($data));

            //redis 存储
            $flag = RedisTool::getInstance()->set("index_video_data_cat_id".$catId,$data);
            if(!$flag)
            {
                // TODO 报警通知
                echo "cat_id:".$catId." put data error".PHP_EOL;
            }
        }

    }
}
