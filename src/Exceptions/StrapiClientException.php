<?php
declare(strict_types=1);


namespace Geekabel\SymfonyStrapi\Exception;

use Symfony\Component\HttpClient\Exception\ClientException;

class StrapiClientException extends \RuntimeException
{
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function createFromClientException(ClientException $clientException): self
    {
        $message = $clientException->getResponse()->getContent(false);
        $code = $clientException->getCode();

        return new self($message, $code, $clientException);
    }
}