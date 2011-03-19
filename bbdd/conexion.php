<?php

include_once("sanitize.php");
/** 
* Establece la conexión con al BBDD
*/
$link=mysql_connect( "mysql.example.com", "user", "pass");
mysql_select_db("ddbb_name",$link) OR DIE ("Error: Imposible Conectar");

?> 
