<?php

namespace App\Libs;

/**
 * 处理反射机制相关
 * Class ClassArr
 *
 * @package \App\Libs
 */
class ClassArr
{
    public function uploadClassStat()
    {
        return [
            "image" => "App\Libs\Upload\Image",
            "video" => "App\Libs\Upload\Video",
        ];
    }

    public function initClass($type,$supportedClass,$params = [] , $needInstance = true )
    {
        if(!array_key_exists($type,$supportedClass))
        {
            return false;
        }

        $className = $supportedClass[$type];

        return $needInstance ? (new \ReflectionClass($className))->newInstanceArgs($params) : $className;
    }
}
