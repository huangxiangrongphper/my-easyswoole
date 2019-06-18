<?php

namespace App\Models;

/**
 * Class VideoModel
 *
 * @package \App\Models
 */
class Video extends Model
{
    protected $tableName = 'video';

    protected $pageLimit;


    /**
     * 根据条件获取数据库里面的video数据
     * @param array $condition
     * @param int   $size
     * @param int   $page
     *
     * @return array
     * @throws \EasySwoole\Mysqli\Exceptions\ConnectFail
     * @throws \EasySwoole\Mysqli\Exceptions\Option
     * @throws \EasySwoole\Mysqli\Exceptions\PrepareQueryFail
     * @throws \Throwable
     */
    public function getVideoData($condition = [] ,$size = 10 ,$page = 1)
    {
        if(!empty($condition['cat_id']))
        {
            $this->db->where('cat_id',$condition['cat_id']);
        }

        // 获取正常的内容
        $this->db->where("status",\Yaconf::get("status.normal"));

        $this->pageLimit = $size;

        $offset = $this->pageLimit * ($page -1 );

//        $count = $page * 10;

        $res =  $this->db->withTotalCount()->get($this->tableName,[$offset,$this->pageLimit]);
        $this->db->orderBy("id","DESC");

       $totalPage = ceil($this->db->getTotalCount() / $this->pageLimit);

        $data = [
            'total_page' => $totalPage,
            'page_size'  => $this->pageLimit,
            'count'      => intval($this->db->getTotalCount()),
            'lists'      => $res,
        ];

        return $data;
    }


    public function getVideoCacheData($condition = [] ,$size = 1000,$page = 1 )
    {
        if(!empty($condition['cat_id']))
        {
            $this->db->where('cat_id',$condition['cat_id']);
        }

        // 获取正常的内容
        $this->db->where("status",\Yaconf::get("status.normal"));
        $this->db->orderBy("id","DESC");

        $this->pageLimit = $size;

        $offset = $this->pageLimit * ($page -1 );

        $res =  $this->db->withTotalCount()->get($this->tableName,[$offset,$this->pageLimit]);

//        var_dump($this->db->getLastQuery());

        return $res;
    }
}
