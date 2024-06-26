<?php

namespace App\Exceptions;

use Exception;

class PropertyNotVerifiedException extends Exception
{
    public function render()
    {
        return redirect(route('properties.index'))->withError($this->getMessage());
    }
}
