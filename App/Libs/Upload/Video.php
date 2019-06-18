<?php

namespace App\Libs\Upload;

/**
 * 视频文件上传
 * Class Video
 *
 * @package \App\Libs\Upload
 */
class Video extends Base
{
    public $fileType = "video";

    public $maxSize  = 122;

    public $fileExtTypes = [
        'mp4',
        'x-flv',
    ];
}
