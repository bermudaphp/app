<?php

use Bermuda\App\Exception;

class BadRequestException extends HttpException
{
    public function __construct(string $reasonPhrase = null)
    {
        parent::__construct($reasonePhrase, 400);
    }
}
