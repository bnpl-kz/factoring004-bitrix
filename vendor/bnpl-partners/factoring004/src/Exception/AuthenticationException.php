<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Exception;

use Throwable;

class AuthenticationException extends ApiException
{
    /**
     * @var string
     */
    protected $description;

    public function __construct(string $description, string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
