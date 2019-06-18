<?php

namespace App\HttpController;
use App\Libs\AliyunSdk\AliVod;
use App\Libs\Redis\redisTool;
use App\Models\Es\EsVideo;
use App\Models\Video;
use EasySwoole\Component\Di;
use EasySwoole\Http\Message\Status;
use EasySwoole\Mysqli\Config;
use EasySwoole\Mysqli\Mysqli;

use App\HttpController\Api\Base;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;


/**
 * Class Index
 * @package App\HttpController
 */
class Index extends Base
{
    /**
     * 首页方法
     * @author : evalor <master@evalor.cn>
     */
    function index()
    {

//        $conf = new Config([
//            'host'                 => '127.0.0.1',//数据库连接ip
//            'user'                 => 'homestead',//数据库用户名
//            'password'             => 'secret',//数据库密码
//            'database'             => 'tp5_app',//数据库
//        ]);
//        $db = new Mysqli($conf);
//        $data = $db->get('ent_user');
//
//        $this->writeJson(200,$data,'ok');


//        $client =  redisTool::getInstance()->ser;
//
//        $client->set('foo', 'bar');
//        $value = $client->get('foo');
//
//        var_dump($value);

//        var_dump(\Yaconf::get("redis"));
//        $client =  RedisTool::getInstance()->ser;
//        $client->lpush('task_list','huang');
//        $client->lpush('task_list','xiang');
//        $client->lpush('task_list','rong');
//
//        $str = $client->lrange('task_list',0,-1);
//
//        var_dump($str);

            // 测试 php-elasticsearch demo


//        $client = ClientBuilder::create()->setHosts([
//            "127.0.0.1:9200"
//        ])->build();



        $result = (new EsVideo())->searchByname($this->params['name']);

        return $this->writeJson(200,$result,"ok");

    }

    public function testaliyun()
    {
        $title = "Flourishing_aliyun-video";
        $videoFileName = "1.mp4";
        $obj = new AliVod();

        $res = $obj->createUploadVideo($title,$videoFileName);

        $uploadAddress = json_decode(base64_decode($res->UploadAddress),true);

        $uploadAuth = json_decode(base64_decode($res->UploadAuth),true);

        $obj->initOssClient($uploadAuth,$uploadAddress);

        $localFile = '/home/vagrant/code/easyswoole/getPublic/video/2019/06/c3271823a66796ec.mp4';

        $result = $obj->uploadLocalFile($uploadAddress,$localFile);

        print_r($result);
    }

    public function getVideo()
    {
        $videoId = "ae4b723b956b4c6db7a908982584b437";
        $obj = new AliVod();
        print_r($obj->getPlayInfo($videoId));
    }

    /**
     * 使用读取MySQL的方案
     * @return bool
     * @throws \Throwable
     */
    /*public function lists()
    {

        $condition = [];

        if(!empty($this->params['cat_id']))
        {
            $condition['cat_id'] = intval($this->params['cat_id']);
        }

        try {
            $videoModel = new Video();
            $data = $videoModel->getVideoData($condition,$this->params['size'],$this->params['page']);
        }catch (\Exception $e)
        {
            return $this->writeJson(Status::CODE_BAD_REQUEST,[],'服务异常');
        }

        if(!empty($data['lists']))
        {
            foreach ($data['lists'] as &$list)
            {
                $list['create_time'] = date("Ymd H:i:s",$list['create_time']);

                $list['video_duration'] = gmstrftime("%H:%m:%s",$list['video_duration']);
            }
        }

        return  $this->writeJson(Status::CODE_OK,$data,'ok');
    }*/

    /**
     * 使用读取 预先定义好的json文件中的数据
     * @return bool
     * @throws \Throwable
     */
    public function lists()
    {
        $catId = !empty($this->params['cat_id']) ? intval($this->params['cat_id']) : 0;

        $videoFile = EASYSWOOLE_ROOT."/Public/video/json/" .$catId.".json";

        // 读取文件数据
//        $videoData = is_file($videoFile) ? file_get_contents($videoFile) : [];
//
//        $videoData = !empty($videoData) ? json_decode($videoData,true) : [];


        //读取Redis中的数据

        $videoData = RedisTool::getInstance()->ser->get("index_video_data_cat_id".$catId);

        $videoData = !empty($videoData) ? json_decode($videoData,true) : [];

        $count = count($videoData);

        return $this->writeJson(Status::CODE_OK,$this->getPagingDatas($count,$videoData),'ok');
    }
}
