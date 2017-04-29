<?php
/**
 * Created by PhpStorm.
 * User: leifei
 * Date: 2017/4/29
 * Time: 下午2:31
 */
require_once("src/API.php");
use ProductAI\API;

$access_key_id = '0d30d0979a1c9410fe44cc6387b255b7';
$secret_key = 'ef4c95300c780f41986c036c7b166908';
$service_type = 'search';
$service_id = 'usyq3ic1';


$file = $_FILES['file'];//得到传输的数据
//得到文件名称
$name = $file['name'];
$type = strtolower(substr($name, strrpos($name, '.') + 1)); //得到文件类型，并且都转化成小写
$allow_type = array('jpg', 'jpeg', 'gif', 'png'); //定义允许上传的类型
//判断文件类型是否被允许上传
if (!in_array($type, $allow_type)) {
    //如果不被允许，则直接停止程序运行
    return;
}
//判断是否是通过HTTP POST上传的
if (!is_uploaded_file($file['tmp_name'])) {
    //如果不是通过HTTP POST上传的
    return;
}
$upload_path = "/vagrant/test.com/"; //上传文件的存放路径
//开始移动文件到相应的文件夹
$newname = time() + rand(1, 100) . '.' . $type;
$new_url = $upload_path . $newname;
if (move_uploaded_file($file['tmp_name'], $new_url)) {
    $data = getimagesize($new_url);
    list($width, $height) = $data;
    if ($width > 800 || $height > 800) {
        image_resize($new_url, $new_url, $width * 0.5, $height * 0.5);
    }
    $product_ai = new API($access_key_id,$secret_key);
    $result = $product_ai->searchImage($service_type, $service_id, '@'.$new_url);
    foreach ($result['results'] as $vo){
        echo "<img src='".$vo['url']."' width='200' height='120'/>";
    }
    echo "ok!";
} else {
    echo "Failed!";
}

function image_resize($f, $t, $tw, $th)
{
// 按指定大小生成缩略图，而且不变形，缩略图函数
    $temp = array(1 => 'gif', 2 => 'jpeg', 3 => 'png');

    list($fw, $fh, $tmp) = getimagesize($f);

    if (!$temp[$tmp]) {
        return false;
    }
    $tmp = $temp[$tmp];
    $infunc = "imagecreatefrom$tmp";
    $outfunc = "image$tmp";

    $fimg = $infunc($f);

    // 使缩略后的图片不变形，并且限制在 缩略图宽高范围内
    if ($fw / $tw > $fh / $th) {
        $th = $tw * ($fh / $fw);
    } else {
        $tw = $th * ($fw / $fh);
    }

    $timg = imagecreatetruecolor($tw, $th);
    imagecopyresampled($timg, $fimg, 0, 0, 0, 0, $tw, $th, $fw, $fh);
    if ($outfunc($timg, $t)) {
        return true;
    } else {
        return false;
    }
}





