<?php

namespace App\Models\Es;

use EasySwoole\Component\Singleton;
use Elasticsearch\ClientBuilder;

/**
 * Class EsClient
 *
 * @package \App\Models\Es
 */
class EsClient
{
    use Singleton;

    protected $esclient = null;

    private function __construct()
    {
        $this->esclient = ClientBuilder::create()->setHosts([
            "127.0.0.1:9200"
        ])->build();
    }

    public function __call($name,$arguments)
    {
//        var_dump($name,$arguments);
        return $this->esclient->$name(...$arguments);
    }
}
