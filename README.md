QH4框架扩展模块-上传模块

该模块现在仅支持本地上传和阿里云上传,后续会增加其它云的上传

### 注意
所有云相关的扩展类（ExtAliyunOSS等）全部为抽象类,也就是必须继承后重写

### 依赖
如果你要使用阿里云相关的上传功能,必须安装 oss 扩展
```shell
composer require aliyuncs/oss-sdk-php
```


### 功能
* 上传到本地服务器（包括上传大小、类型等限制）
* 申请用于客户端直传阿里云oss的签名
* 上传本地文件到阿里云


### api列表
```php
actionUploadFile()
```
上传单个文件到本地服务器

该接口没有进行任何文件相关验证,不建议直接使用该接口上传,应该针对具体业务, 单独写 `ExtUpload` 控制类

```php
actionApplyOSSSignature()
```
申请前端直传oss用的签名

### 方法列表
```php
/**
 * 从远程url下载文件保存到本地
 * @param $url string
 * @param $file string 文件的完整路径
 * @return bool
 */
public static function downloadFileFormUrl($url, $file)
```

```php
/**
 * 通过文件url将文件上传阿里云oss
 * @param string $url 文件url
 * @param string $target 存到阿里云的路径,注意不要以 `/` 开头
 *               类似于 2001-01-01/xxxxxx/file.jpeg
 * @param ExtAliyunOSS $external
 * @return bool
 */
public static function uploadOssByUrl($url, $target, ExtAliyunOSS $external)
```

```php
/**
 * 将本地文件上传到阿里云
 * @param string $file 本地文件路径
 * @param string $target 存到阿里云的路径,注意不要以 `/` 开头
 *               类似于 2001-01-01/xxxxxx/file.jpeg
 * @param ExtAliyunOSS $external
 * @return bool
 */
public static function uploadFileOss($file, $target, ExtAliyunOSS $external)
```