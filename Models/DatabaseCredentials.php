<?php

namespace Models;

class DatabaseCredentials
{
    private string $host;
    private string $user;
    private string $pass;
    private string $port;

    /**
     * DatabaseCredentials constructor.
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $port
     */
    public function __construct(string $host, string $user, string $pass, string $port)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getPass(): string
    {
        return $this->pass;
    }

    /**
     * @param string $pass
     */
    public function setPass(string $pass): void
    {
        $this->pass = $pass;
    }

    /**
     * @return string
     */
    public function getPort(): string
    {
        return $this->port;
    }

    /**
     * @param string $port
     */
    public function setPort(string $port): void
    {
        $this->port = $port;
    }
}