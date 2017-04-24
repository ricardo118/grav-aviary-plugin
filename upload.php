<?php
/**
 * Created by PhpStorm.
 * User: Ricardo
 * Date: 24/04/2017
 * Time: 01:56
 */
function getRootPath() {
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $root_path = str_replace(' ', '%20', rtrim(substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], 'index.php')), '/'));
    // check if userdir in the path and workaround PHP bug with PHP_SELF
    if (strpos($uri, '/~') !== false && strpos($_SERVER['PHP_SELF'], '/~') === false) {
        $root_path = substr($uri, 0, strpos($uri, '/', 1)) . $root_path;
    }
    return $root_path;
}

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
