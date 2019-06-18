<?php

namespace App\Libs\Redis;

use EasySwoole\EasySwoole\Config;

class RedisTool
{
    const REDIS_MAIN = 'master';
    const REDIS_SLAVE = 'slave';

    private static $ins = null;

    public static function getInstance($redisType = self::REDIS_MAIN, $extras = [])
    {
        // 获取配置
        $redisConf = Config::getInstance()->getConf('REDIS');
        $redisConf = array_merge($redisConf[$redisType], $extras);

        if (is_null(self::$ins)) {
            self::$ins = new self($redisConf);
        }

        return self::$ins;
    }

    /*
     *  构造函数
     *
     */
    private function __construct($config)
    {
        $redis = new \Predis\Client($config);
        $this->ser = $redis;
    }

    /*
     *  结束时关闭连接
     */
    private function __destruct()
    {
        $this->ser->disconnect();
    }

    public function produce($key, array $data)
    {
        return $this->ser->lpush($key, $data);
    }

    public function lpush($key, $data)
    {
        if (is_string($data)) {
            $data = (array)$data;
        }

        return $this->ser->lpush($key, $data);
    }

    public function set($key,$value,$time = 0)
    {
        if(empty($key))
        {
            return '';
        }
        if(is_array($value))
        {
            $value = json_encode($value);
        }
        if(!$time)
        {
            return $this->ser->set($key,$value);
        }
        return $this->ser->setex($key,$time,$value);
    }
}
