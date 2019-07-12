<?php
class Rcon{
    private $host;
    private $port;
    private $password;
    private $timeout;
    private $socket;
    private $authorized = false;
    private $lastResponse = '';
    const PACKET_AUTHORIZE = 5;
    const PACKET_COMMAND = 6;
    const SERVERDATA_AUTH = 3;
    const SERVERDATA_AUTH_RESPONSE = 2;
    const SERVERDATA_EXECCOMMAND = 2;
    const SERVERDATA_RESPONSE_VALUE = 0;
    public function __construct($host, $port, $password, $timeout)
    {
        $this->host = $host;
        $this->port = $port;
        $this->password = $password;
        $this->timeout = $timeout;
    }
    public function getResponse()
    {
        return $this->lastResponse;
    }
    public function connect()
    {
        $this->socket = fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
        if (!$this->socket) {
            $this->lastResponse = $errstr;
            return false;
        }
        stream_set_timeout($this->socket, 3, 0);
        return $this->authorize();
    }
    public function disconnect()
    {
        if ($this->socket) {
                    fclose($this->socket);
        }
    }
    public function isConnected()
    {
        return $this->authorized;
    }
    public function sendCommand($command)
    {
        if (!$this->isConnected()) {
                    return false;
        }
        $this->writePacket(self::PACKET_COMMAND, self::SERVERDATA_EXECCOMMAND, $command);
        $response_packet = $this->readPacket();
        if ($response_packet['id'] == self::PACKET_COMMAND) {
            if ($response_packet['type'] == self::SERVERDATA_RESPONSE_VALUE) {
                $this->lastResponse = $response_packet['body'];

                return $response_packet['body'];
            }
        }
        return false;
    }
    private function authorize()
    {
        $this->writePacket(self::PACKET_AUTHORIZE, self::SERVERDATA_AUTH, $this->password);
        $response_packet = $this->readPacket();

        if ($response_packet['type'] == self::SERVERDATA_AUTH_RESPONSE) {
            if ($response_packet['id'] == self::PACKET_AUTHORIZE) {
                $this->authorized = true;
                return true;
            }
        }
        $this->disconnect();
        return false;
    }
    private function writePacket($packetId, $packetType, $packetBody)
    {
        $packet = pack('VV', $packetId, $packetType);
        $packet = $packet.$packetBody."\x00";
        $packet = $packet."\x00";
        $packet_size = strlen($packet);
        $packet = pack('V', $packet_size).$packet;
        fwrite($this->socket, $packet, strlen($packet));
    }
    private function readPacket()
    {
        $size_data = fread($this->socket, 4);
        $size_pack = unpack('V1size', $size_data);
        $size = $size_pack['size'];
        $packet_data = fread($this->socket, $size);
        $packet_pack = unpack('V1id/V1type/a*body', $packet_data);
        return $packet_pack;
    }
}
function mcolor($str){
    $str=str_replace("\n","<br>",$str);
    $strarr=explode("§",$str);
    $colors=array("0"=>"black","1"=>"blue","2"=>"green","3"=>"aqua","4"=>"red","5"=>"purple","6"=>"orange","7"=>"silver","8"=>"dimgray","9"=>"DarkOrchid","a"=>"lightgreen","b"=>"cyan","c"=>"tomato","d"=>"fuchsia","e"=>"gold");
    $strarrC=array();
    $strarrE=array();
    $out='<font>'.$strarr[0].'</font>';
    for($i=1;$i<count($strarr);$i++){
        $strarrE[$i]=substr($strarr[$i],1);
        $strarrC[$i]=substr($strarr[$i],0,1);
        $out=$out.'<font style="color:'.$colors[$strarrC[$i]].';">'.$strarrE[$i].'</font>';
    }
    return $out;
}
if(empty($_SESSION["status"])){
    //未登录情况
    if($cmdarr[1]=="connect"){
        if(count($cmdarr)!=5){
            //如果格式不对
            $error=true;
            $errcode="参数错误！使用方法: rcon connect &lt;地址&gt; &lt;端口&gt; &lt;密码&gt;";
        }else{
            //尝试连接
            $rcon = new Rcon($cmdarr[2], $cmdarr[3], $cmdarr[4], 3);
            if ($rcon->connect()){
                $msg="成功连接到服务器<br>你现在在Rcon模式下，用 \"disconnect\"来断开连接<br>";
                $_SESSION["status"]="rcon";
                $_SESSION["rhost"]=$cmdarr[2];
                $_SESSION["rport"]=$cmdarr[3];
                $_SESSION["rpass"]=$cmdarr[4];
            }else{
                //连不上
                $error=true;
                $errcode="连不上服务器，请检查参数";
            }
        }
    }else{
        $error=true;
        $errcode="参数 \"".$cmdarr[1]."\"未找到！使用方法: rcon connect &lt;地址&gt; &lt;端口&gt; &lt;密码&gt;";
    }
}else{
    //已登录
    if($cmdarr[0]=="disconnect"){
        $_SESSION["status"]="";
        $_SESSION["rhost"]="";
        $_SESSION["rport"]="";
        $_SESSION["rpass"]="";
        $msg="已断开";
    }else{
        //尝试连接
        $rcon = new Rcon($_SESSION["rhost"], $_SESSION["rport"], $_SESSION["rpass"], 3);
        if ($rcon->connect()){
            $msg=$rcon->sendCommand($cmd);
            $msg=mcolor($msg);
        }else{
            $error=true;
            $_SESSION["status"]="";
            $_SESSION["rhost"]="";
            $_SESSION["rport"]="";
            $_SESSION["rpass"]="";
            $errcode="连接不到服务器，已断开";
        }
    }
}