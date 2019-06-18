<?php

namespace App\Libs\AliyunSdk;

require_once EASYSWOOLE_ROOT .'/App/Libs/AliyunSdk/aliyun-php-sdk-core/Config.php';
require_once EASYSWOOLE_ROOT .'/App/Libs/AliyunSdk/aliyun-oss-php-sdk-master/autoload.php';

use vod\Request\V20170321 as vod;
use OSS\OssClient;
use OSS\Core\OssException;
/**
 * 利用阿里云OSS上传点播视频 利用官方文档的封装 https://help.aliyun.com/document_detail/61388.html?spm=5176.10695662.1996646101.searchclickresult.39822b53raZQtb#h2-php-6
 * Class AliVod
 *
 * @package \App\Libs\AliyunSdk
 */
class AliVod
{
    protected $regionId = 'cn-shanghai'; //点播区域 国内这里填写统一格式
    protected $client;
    protected $ossClient;

    // step 1
    public function __construct() {
        $profile = \DefaultProfile::getProfile($this->regionId,\Yaconf::get("aliyun.accessKeyId"),\Yaconf::get("aliyun.accessKeySecret"));
        $this->client =  new \DefaultAcsClient($profile);
    }

    // step 2
   public function createUploadVideo($title,$videoFileName,$other = []) {
        $request = new vod\CreateUploadVideoRequest();
        $request->setTitle($title);        // 视频标题(必填参数)
        $request->setFileName($videoFileName); // 视频源文件名称，必须包含扩展名(必填参数)

       if(!empty($other['description']))
       {
           $request->setDescription($other['description']);  // 视频源文件描述(可选)
       }

       if(!empty($other['coverURL']))
       {
           $request->setCoverURL($other['coverURL']); // 自定义视频封面(可选)
       }

       if(!empty($other['tags']))
       {
           $request->setTags($other['tags']); // 视频标签，多个用逗号分隔(可选)
       }

       $result =  $this->client->getAcsResponse($request);

       if(empty($result) || empty($result->VideoId))
       {
           throw new \Exception("获取上传视频相关凭证失败!");
       }

       return $result;
    }

    // step 3
    public function initOssClient($uploadAuth, $uploadAddress) {

        $this->ossClient = new OssClient($uploadAuth['AccessKeyId'], $uploadAuth['AccessKeySecret'], $uploadAddress['Endpoint'],
            false, $uploadAuth['SecurityToken']);
        $this->ossClient->setTimeout(\Yaconf::get("aliyun.uploadFileTimeOut"));    // 设置请求超时时间，单位秒，默认是5184000秒, 建议不要设置太小，如果上传文件很大，消耗的时间会比较长
        $this->ossClient->setConnectTimeout(\Yaconf::get("aliyun.uploadFileConnectTimeout"));  // 设置连接超时时间，单位秒，默认是10秒
    }

    // step 4
    public function uploadLocalFile($uploadAddress, $localFile) {
        return $this->ossClient->uploadFile($uploadAddress['Bucket'], $uploadAddress['FileName'], $localFile);
    }

    // step 5 获取视频播放地址
    public function getPlayInfo($videoId = 0)
    {
        if(empty($videoId))
        {
            return [];
        }

        $request = new vod\GetPlayInfoRequest();
        $request->setVideoId($videoId);
        $request->setAcceptFormat("JSON");

        return $this->client->getAcsResponse($request);
    }
}
