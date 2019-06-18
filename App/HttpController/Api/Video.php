<?php

namespace App\HttpController\Api;

use App\Libs\Redis\RedisTool;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Http\Message\Status;

use App\Models\Video as VideoModel;
use EasySwoole\Validate\Validate;

/**
 * Class Video
 *
 * @package \App\HttpController\Api
 */
class Video extends Base
{
    protected $type = "Video-Add-log: ";


    public function index()
    {
        $id = intval($this->params['id']);
        if(empty($id))
        {
            return $this->writeJson(Status::CODE_BAD_REQUEST,[],'请求不合法');
        }

        //获取视频的基本信息
        try{
            $video = (new \App\Models\Video())->getById($id);
        }catch (\Exception $e)
        {
            // 记录日志
            return $this->writeJson(Status::CODE_BAD_REQUEST,[],'请求不合法');
        }
        if(!$video || $video['status'] != \Yaconf::get("status.normal"))
        {
            return $this->writeJson(Status::CODE_BAD_REQUEST,"该视频不存在");
        }
        $video['video_duration'] = gmstrftime("%H:%M:%S",$video['video_duration']);

        // 增加Redis有序集合存储视频的播放量
        TaskManager::async(function() use($id){
            RedisTool::getInstance()->ser->zincrby(\Yaconf::get("redis.video_play_key"),1,$id);
        });

        return $this->writeJson(Status::CODE_OK,$video,'ok');
    }

    public function rank()
    {
        $result = RedisTool::getInstance()->ser->zrevrange(\Yaconf::get("redis.video_play_key"),0,-1,"withscores");

        return $this->writeJson(200,$result,'ok');
    }

    public function add()
    {
        $params = $this->request()->getRequestParam();

        if($params)
        {
            //记录日志
            Logger::getInstance()->log($this->type.json_encode($params)."\n");
        }

        //数据验证
        $validate = new Validate();
        $validate->addColumn('name')->required('姓名必填')->betweenLen(5,36,'视频长度必须在5到36之间');
        $validate->addColumn('url')->required('视频地址必填');
        $validate->addColumn('image')->required('图片地址必填');
        $validate->addColumn('content')->required('视频描述必填');
        $validate->addColumn('cat_id')->required('栏目ID必填');

        if (!$this->validate($validate)) {
            return $this->writeJson(Status::CODE_BAD_REQUEST, $validate->getError()->__toString(), 'fail');
        }

        $data = [
            'name' => $params['name'],
            'url' => $params['url'],
            'image' => $params['image'],
            'content' => $params['content'],
            'cat_id' => intval($params['cat_id']),
            'create_time' => time(),
            'status' => \Yaconf::get("status.normal") // 0 1 2
        ];

        //写入数据
        $video = new VideoModel();
        $id = $video->add($data);

        if(!$id)
        {
            return $this->writeJson(Status::CODE_BAD_REQUEST,-1,'操作失败');
        }

        return $this->writeJson(Status::CODE_OK,1,'操作成功');
    }
}
