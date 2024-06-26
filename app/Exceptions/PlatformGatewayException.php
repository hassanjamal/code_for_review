<?php

namespace App\Exceptions;

use Exception;

class PlatformGatewayException extends Exception
{
    public function render()
    {
        return back()->withError($this->getMessage());
    }
}
