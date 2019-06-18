<?php

namespace App\HttpController\Api;

use EasySwoole\Http\AbstractInterface\Controller;


/**
 * Api基类
 * Class Base
 *
 * @package App\HttpController\Api
 */
class Base extends Controller
{

    protected $params;
    /**
     * Api 基类实现框架抽象方法
     *
     * @author : evalor <master@evalor.cn>
     */
    public function index()
    {

    }

    /**
     * 权限相关
     * @param string|null $action
     *
     * @return bool|null
     */
    protected function onRequest(?string $action): ?bool
    {
        $this->getParams();

        return true;
    }

    public function getParams()
    {
        $params = $this->request()->getRequestParam();
        $params['page'] = !empty($params['page']) ? intval($params['page']) : 1;
        $params['size'] = !empty($params['size']) ? intval($params['size']) : 5;

        $params['from'] = ($params['page'] - 1) * $params['size'];

        $this->params = $params;
    }

    /**
     * @param \Throwable $throwable
     *
     * @throws \Throwable
     */
//    protected function onException(\Throwable $throwable): void
//    {
//        $this->writeJson(400,'请求不合法');
//    }


    /**
     * 分页读取json文件中的视频信息
     * @param $count
     * @param $data
     *
     * @return array
     */
    public function getPagingDatas($count,$data,$isSplice = true )
    {
        $totalPage = ceil( $count / $this->params['size'] );

        if($totalPage > 100)
        {
            $totalPage = 100;
        }

        $data = $data ?? [];

        if($isSplice)
        {
            $data = array_splice($data,$this->params['from'],$this->params['size']);
        }

        return [
            'total_page' => $totalPage,
            'page_size'  => $this->params['page'],
            'count'      => intval($count),
            'lists'      => $data
        ];
    }
}
