<?php

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class EmailNotVerifiedException extends AuthenticationException
{
    public function getMessageKey(): string
    {
        return 'Unconfirmed Account';
    }
}
