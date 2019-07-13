<?php
//include "include/auth.php";
//此页面必须加入权限验证！
if(!empty($_SESSION["status"])){
    //如果有状态 例如rcon已连接
    include ROOT."/include/lib/".$_SESSION["status"].".php";
}else if(file_exists(ROOT."/include/lib/".$cmdarr[0].".php")){
    //如果该库存在
    include ROOT."/include/lib/".$cmdarr[0].".php";
}else{
    //如果存在异常，则抛出
    $error=true;
    $errcode="找不到指令，请使用help来查看所有可用命令";
}