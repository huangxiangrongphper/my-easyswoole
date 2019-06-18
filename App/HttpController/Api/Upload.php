<?php

namespace App\HttpController\Api;

use App\Libs\ClassArr;

/**
 *
 * Class upload
 *
 * @package \App\HttpController\Api
 */
class Upload extends Base
{
    public function file()
    {
        $request = $this->request();
        $files = $request->getSwooleRequest()->files;
        $types = array_keys($files);
        $type = $types[0];

        if(!$type)
        {
            return $this->writeJson(400,[],'没有选择上传文件');
        }

        try {
            $classobj     =  new ClassArr();
            $classStats   = $classobj->uploadClassStat();

            $uploadObj    = $classobj->initClass($type,$classStats,[$request,$type]);
            $file = $uploadObj->upload();

        }catch (\Exception $e){
            return $this->writeJson(400,[],$e->getMessage());
        }
        if(empty($file))
        {
            return $this->writeJson(400,[],"上传失败");
        }

        $data = [
            'url' => $file
        ];
        return $this->writeJson(200,$data,'ok');
    }
}
