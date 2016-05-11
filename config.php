<meta charset="utf-8">
<?php

    define('DB_HOST','localhost');
    define('DB_USER','mqt');
    define('DB_PWD','20151006');//密码
    define('DB_NAME','mqt_wechat');

    //连接数据库
    $connect = mysql_connect(DB_HOST,DB_USER,DB_PWD) or die('数据库连接失败，错误信息：'.mysql_error());

    //选择指定数据库，设置字符集
    mysql_select_db(DB_NAME,$connect) or die('数据表连接错误，错误信息：'.mysql_error());
    mysql_query('SET NAMES UTF8') or die('字符集设置出错'.mysql_error());

?> 
