<?php



$path = 'D:\\phpstudy\\PHPTutorial\\WWW\ciscn\\';
if (isset($_GET['icon'])){
    $filename = $_GET['icon'];
    // var_dump($filename);
    if (file_exists($filename)){
        readfile($filename);
    }
}else{
    echo <<<EOF
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="icon" href="?icon=favicon.png" type="image/x-icon" />
    
</head>
hack me
EOF;
}



    