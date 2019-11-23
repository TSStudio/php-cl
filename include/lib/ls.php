<?php
class ls{
    public static $help='\\-ls [dir] - 显示扫描目录下所有文件';
}
if(!isset($help)){
    if($cmdcot==1){
        $filename = scandir(ROOT);
        $msg=ROOT.":";
        foreach($filename as $k=>$v){
            if($v=="." || $v==".."){continue;}
            $msg=$msg.'<br>'.$v;
        }
    }else if(!file_exists($cmdarr[1])){
        $error=true;
        $errcode='ERROR:DIRECTORY "'.$cmdarr.'" does not exist!';
    }else{
        $filename = scandir($cmdarr[1]);
        $msg=ROOT.":";
        foreach($filename as $k=>$v){
            if($v=="." || $v==".."){continue;}
            $msg=$msg.'<br>'.$v;
        }
    }
}