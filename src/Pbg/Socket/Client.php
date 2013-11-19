<?php

namespace Pbg\Socket;

class Client
{
    use Configurable;

    protected $socket = null;

    /**
     * Available options:
     *
     * - protoFamily
     * - socketType
     * - socketProtocol
     * - socketTimeout
     * - blocking
     * @param $options array
     */
    public function __construct($options = array())
    {
        if (sizeof($options) > 0) {
            $this->setOptions($options);
        }
    }

    public function connect()
    {
        $this->socket = $this->createSocket();

        try {
            $port = $this->getPort();
        } catch(\Exception $e) {
            $port = 0;
        }

        $r = @socket_connect(
            $this->socket,
            $this->getHost(),
            $port
        );

        if ($r === false) {
            $this->throwSocketException();
        }
    }

    public function send($buffer)
    {
        if ($this->socket === null) {
            $this->connect();
        }

        $r = socket_write(
            $this->socket,
            $buffer,
            strlen($buffer)
        );

        if ($r === false) {
            $this->throwSocketException();
        }

        return $r;
    }

    public function receive()
    {
        $buffer = "";

        while ($r = socket_read($this->socket, 2048) !== false) {
            $buffer .= $r;
        }

        return $buffer;
    }

    protected function createSocket()
    {
        $s = socket_create(
            $this->getProtoFamily(),
            $this->getSocketType(),
            $this->getSocketProtocol()
        );

        if ($s === false) {
            $this->throwSocketException();
        }

        return $s;
    }

    protected function throwSocketException()
    {
        throw new \Exception(
            sprintf("Can't create socket: %s",
                    socket_strerror(socket_last_error($this->socket))
                   )
        );
    }
}
