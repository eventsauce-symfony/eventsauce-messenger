<?php

declare(strict_types=1);


namespace Andreo\EventSauce\Messenger\Stamp;

use Andreo\EventSauce\Messenger\Headers;
use Symfony\Component\Messenger\Stamp\StampInterface;

final class HeadersStamp implements StampInterface
{
    public function __construct(private Headers $headers) {}

    public function getHeaders(): Headers
    {
        return $this->headers;
    }
}