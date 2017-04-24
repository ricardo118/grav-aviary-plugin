<?php
/**
 * Created by PhpStorm.
 * User: Ricardo
 * Date: 24/04/2017
 * Time: 01:56
 */

$img = $_POST['imgBase64'];
$path = $_POST['uploadPath'];
$imgname = $_POST['imgName'];
$img = str_replace('data:image/png;base64,', '', $img);
$img = str_replace(' ', '+', $img);
$data = base64_decode($img);
$file = $path.'/'.$imgname;
$success = file_put_contents($file, $data);
print_r($success);
print_r($file);
