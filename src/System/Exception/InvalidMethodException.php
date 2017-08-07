<?php
namespace Cheyoo\System\Exception;

use Psr\Http\Message\ServerRequestInterface;

class InvalidMethodException extends \InvalidArgumentException
{
    public function __construct()
    {
        // parent::__construct(sprintf('Unsupported HTTP method "%s" provided', $method));
    }
}
