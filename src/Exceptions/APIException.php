<?php

namespace AuthManager\Exceptions;

use Exception;

class APIException extends Exception
{
    private string $status = '';
    private string $statusMessage = '';

    public function __construct(string $message, int $code = -1)
    {
        parent::__construct($message, $code);
        $this->message = $message;
        $this->code = $code;
    }

    public function setStatus($status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatusMessage(string $statusMessage): self
    {
        $this->statusMessage = $statusMessage;
        return $this;
    }

    public function getStatusMessage(): string
    {
        return $this->statusMessage;
    }
}