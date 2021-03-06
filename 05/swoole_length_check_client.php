<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-24
 * Time: 18:33
 */

class Client
{
    private $client;

    public function __construct()
    {
        $this->client = new swoole_client(SWOOLE_SOCK_TCP);
    }

    public function connect()
    {
        if (!$fp = $this->client->connect("127.0.0.1", 9501, 1)) {
            echo "Error: {$fp->errMsg}[{$fp->errCode}]\n";
        }

        $msg_normal = "This is a Msg";

        $msg_length = pack("N" , strlen($msg_normal) ). $msg_normal;

        $i = 0;
        while( $i < 100 ) {
            $this->client->send( $msg_length );
            $i ++;
        }
    }
}

$client = new Client();
$client->connect();