<?php

namespace App\HttpController\Api;


use App\Models\Es\EsVideo;
use EasySwoole\Http\Message\Status;

/**
 * Class Search
 *
 * @package \App\HttpController\Api
 */
class Search extends Base
{
    public function index()
    {
        $keyword = trim($this->params['keyword']);

        if(empty($keyword))
        {
            return $this->writeJson(Status::CODE_OK,$this->getPagingDatas(0,[],false));
        }

        $es = new EsVideo();

        $result = $es->searchByname($keyword,$this->params['from'],$this->params['size']);

        if(empty($result))
        {
            return $this->writeJson(Status::CODE_OK,$this->getPagingDatas(0,[],false));
        }

        $hits  = $result['hits']['hits'];
        $total = $result['hits']['total'];

        if(empty($total))
        {
            return $this->writeJson(Status::CODE_OK,$this->getPagingDatas(0,[],false));
        }

        foreach ($hits as $hit )
        {
            $source = $hit['_source'];
            $resData[] = [
                'id'   => $hit['_id'],
                'name' => $source['name'],
                'image' => $source['image'],
                'uploader' => $source['uploader'],
                'create_time' => '',
                'video_duration' => '',
                'keywords' => [$keyword]
            ];
        }

        return $this->writeJson(Status::CODE_OK,$this->getPagingDatas($total,$resData,false),"ok");
    }
}
