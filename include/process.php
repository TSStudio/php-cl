<?php
/*需求:
help h -列出所有命令

rcon:
rcon connect <HOST> <PORT> <password>
INNER:
disconnect

query:

query blocks <fromtime(理论上只要你中间没空格就可以，全英文，例如 18:00,June13th,2019 )> <totime> [ASC|DESC] [LIMIT]
query chat <playerid>

*/
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