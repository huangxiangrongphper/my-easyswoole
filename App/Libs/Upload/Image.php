<?php

namespace App\Libs\Upload;

/**
 * 视频文件上传
 * Class Video
 *
 * @package \App\Libs\Upload
 */
class Image extends Base
{
    public $fileType = "image";

    public $maxSize  = 10;

    public $fileExtTypes = [
        'jpg',
        'png',
        'jpeg'
    ];
}
