<?php

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;


/**
 * Class Index
 * @package App\HttpController
 */
class Category extends Controller
{
    /**
     * 首页方法
     * @author : evalor <master@evalor.cn>
     */
    function index()
    {
        $data = [
            'id' => 1,
            'name' => 'Floruishing',
        ];
        $this->writeJson(200,$data,'ok');
    }
}
