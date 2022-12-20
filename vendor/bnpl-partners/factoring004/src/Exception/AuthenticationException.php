<?php

namespace BnplPartners\Factoring004\Exception;

use Throwable;

class AuthenticationException extends ApiException
{
    /**
     * @var string
     */
    protected $description;

    /**
     * @param \Throwable $previous
     * @param string $description
     * @param string $message
     * @param int $code
     */
    public function __construct($description, $message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
