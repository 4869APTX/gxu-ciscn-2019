<?php
  error_reporting(0);
  if(!$_GET[file]){echo '<a href="./1.php?file=show.php">click me? no</a>';}
  $file=$_GET['file'];
  if(strstr($file,"../")||stristr($file, "tp")||stristr($file,"input")||stristr($file,"data")){
    echo "Oh no!";
    exit();
  }
  include($file); 
//flag:nctf{edulcni_elif_lacol_si_siht}

?>