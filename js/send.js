var prefix=document.getElementById("prefix");
var box=document.getElementById("output");
var input=document.getElementById("command");
var ajaxhttp=new XMLHttpRequest();
function changePrefix(inner){
    prefix.innerText=inner;
    return true;
}
function changeOutput(type,content){
    d = new Date();
    time="["+d.getHours()+":"+d.getMinutes()+":"+d.getSeconds()+"."+d.getMilliseconds()+"]";
    if(type==0){
        box.innerHTML=box.innerHTML+time+prefix.innerText+content;
    }else{
        box.innerHTML=box.innerHTML+"<br>"+time+content;
    }
}
function send(){
    ajaxhttp.open("POST","https://security.tmysam.top/php-cl/php-cl.php",false);
    ajaxhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    changeOutput(0,input.value);
    ajaxhttp.send("cmd="+input.value);
    data=JSON.parse(ajaxhttp.responseText);
    out="";
    if(data["error"]==1){
        out="ERROR:"+data["msg"];
    }else{
        if(data["isstatus"]==1){
            changePrefix(data["status"]+">");
        }else{
            changePrefix("php-cl>");
        }
        out=data["msg"];
        input.value="";
    }
    changeOutput(1,out);
}