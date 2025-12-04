<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ValidateSignature as BaseValidateSignature;

/**
 * middleware base de Laravel para asegurar que firmas en URLs sean válidas.
 */
class ValidateSignature extends BaseValidateSignature
{
}
