<?php

namespace App\HttpController\Api;



/**
 * Class Index
 * @package App\HttpController
 */
class Index extends Base
{

    function video()
    {
//        new abc();
        $data = [
            'id' => 1,
            'name' => 'Floruishing-boy',
            'params' => $this->request()->getRequestParam()
        ];
        $this->writeJson(201,$data,'ok');

    }
}
