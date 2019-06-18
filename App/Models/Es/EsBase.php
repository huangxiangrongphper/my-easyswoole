<?php

namespace App\Models\Es;

use EasySwoole\Component\Di;

/**
 * Class EsBase
 *
 * @package \App\Models\Es
 */
class EsBase
{

    protected $esClient;

    public function __construct()
    {
        $this->esClient = Di::getInstance()->get("ES");
    }

    public function searchByname($name,$from = 0,$size = 10, $type = "match")
    {
        $name = trim($name);
        if(empty($name))
        {
            return [];
        }

        $params = [
            "index" => $this->index,
            "type"  => $this->type,
            "body" => [
                'query' => [
                    $type => [
                        'name' => $name
                    ],
                ],
                'from' => $from,
                'size' => $size
            ],
        ];


        $result = $this->esClient->search($params);

        return $result;

    }
}
