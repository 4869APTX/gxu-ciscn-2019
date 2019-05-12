<?php

if (isset($_POST['url'])) { 
    echo $_POST['url'];
    $content = file_get_contents($_POST['url']); 
    $ip = $_SERVER['REMOTE_ADDR'];
    // var_dump($content);
    // $path = 'D:\\phpstudy\\PHPTutorial\\WWW\ciscn\\ssrf\\';
    // $filename =$path.'images\\'.rand().'img1.jpg'; 
    // file_put_contents($filename, $content); 
    $img = "<img src=\"".$content."\"/>"; 
}
echo @$img;

echo 'look image site,enter you url';
echo <<<EOF
<form action = 'index.php' method='POST'>
    <input type="text" name='url'>
    <input id="subLogin"  name ="subLogin" type="submit" value="提交" />
</form>
EOF;



?>