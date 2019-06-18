<?php

namespace App\HttpController\Api;

/**
 * 视频栏目
 * Class Category
 *
 * @package \App\HttpController\Api
 */
class Category extends Base
{
    public function index()
    {
        $config = \Yaconf::get("category.cats");

        return $this->writeJson(200,'ok',$config);
    }
}
