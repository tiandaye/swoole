<?php

// Swoole Task 的应用
class Client

{

    private $client;

    public function __construct() {

        $this->client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

        $this->client->on('Connect', [$this, 'onConnect']);

        $this->client->on('Receive', [$this, 'onReceive']);

        $this->client->on('Close', [$this, 'onClose']);

        $this->client->on('Error', [$this, 'onError']);

    }

    public function connect() {

        if(!$fp = $this->client->connect("127.0.0.1", 9501 , 1)) {

            echo "Error: {$fp->errMsg}[{$fp->errCode}]".PHP_EOL;

            return;

        }

    }

    public function onConnect($cli) {

        fwrite(STDOUT, "输入Email:");

        swoole_event_add(STDIN, function() {

            fwrite(STDOUT, "输入Email:");

            $msg = trim(fgets(STDIN));

            $this->send($msg);

        });

    }

    public function onReceive($cli, $data) {

        echo PHP_EOL."Received: ".$data.PHP_EOL;

    }

    public function send($data) {

        $this->client->send($data);

    }

    public function onClose($cli) {

        echo "Client close connection".PHP_EOL;

    }

    public function onError() {

    }

}

$client = new Client();

$client->connect();
