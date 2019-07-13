<?php
$help=true;
$msg='内部命令:<br>help - 显示帮助<br><br>外部挂载库:<br>';
$filename = scandir(ROOT.'/include/lib/');
// 定义一个数组接收文件名
$conname = array();
foreach($filename as $k=>$v){
    // 跳过两个特殊目录   continue跳出循环
    if($v=="." || $v==".." || $v=="help.php"){continue;}
    include $v;
    $clsn=substr($v,0,strpos($v,"."));
    $conname[]=$clsn;
    if(class_exists($clsn)){
        eval("\$res=".$clsn."::\$help;");
        if(empty($res)){
            $res="未找到使用方法";
        }
    }else{
        $res="未找到使用方法";
    }
    $msg=$msg."<br>".$clsn."<br>".$res."<br>";
}
$msg=$msg."已挂载的库：help";
for($i=0;$i<count($conname);$i++){
    $msg=$msg.",".$conname[$i];
}
$msg=$msg."<br>";