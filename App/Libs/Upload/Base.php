<?php

namespace App\Libs\Upload;

use App\Libs\Utils;

/**
 * 文件上传基类
 * Class Base
 *
 * @package \App\Libs\Upload
 */
class Base
{
    /**
     * 文件类型
     * @var string
     */
    private $type = "";
    private $request = "";
    private $size = "";
    private $clientMediaType = "";
    private $file = "";

    public function __construct($request,$type = null)
    {
        $this->request = $request;
        if(empty($type)){
            $files = $this->request->getSwooleRequest()->files;
            $types = array_keys($files);
            $this->type = $types[0];
        }else {
            $this->type = $type;
        }

    }

    public function upload()
    {
        if($this->type != $this->fileType)
        {
            return false;
        }

        $videos = $this->request->getUploadedFile($this->type);

        $this->size = $videos->getSize();

        $this->checkSize();

        $fileName = $videos->getClientFileName();

        $this->clientMediaType = $videos->getClientMediaType();

        $this->checkMediaType();

        $file = $this->getFile($fileName);

        $flag = $videos->moveTo($file);

        if(!empty($flag))
        {
            return $this->file;
        }

        return false;
    }

    public function getFile($fileName)
    {
        $pathinfo  = pathinfo($fileName);
        $extension = $pathinfo['extension'];

        $dirname = "/". $this->type . "/" . date("Y") . "/" . date("m");
        $dir = EASYSWOOLE_ROOT . "/Public" . $dirname ;

        if(!is_dir($dir))
        {
            mkdir($dir,0777,true);
        }

        $basename = "/" .Utils::getFileKey($fileName) . "." . $extension;

        $this->file = $dirname . $basename;

        return $dir . $basename;
    }

    public function checkSize()
    {
        if(empty($this->size))
        {
            return false;
        }

        //todo 检查文件大小是否合法
    }

    public function checkMediaType()
    {
        $clientMediaType = explode("/",$this->clientMediaType);
        $clientMediaType = $clientMediaType[1] ?? "";
        if(empty($clientMediaType))
        {
            throw new \Exception("上传{$this->type} 文件不合法");
        }
        if(!in_array($clientMediaType,$this->fileExtTypes))
        {
            throw new \Exception("上传{$this->type} 文件不合法");
        }

        return true;
    }
}
