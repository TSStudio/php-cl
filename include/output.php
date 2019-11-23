<?php
if($error){
    $output=array("error"=>"1","msg"=>$errcode);
}else{
    $output=array("error"=>"0","msg"=>$msg."<br>");
}
if(!empty($_SESSION["status"])){
    $output["isstatus"]="1";
    $output["status"]=$_SESSION["status"];
}else{
    $output["isstatus"]="0";
}
echo json_encode($output);