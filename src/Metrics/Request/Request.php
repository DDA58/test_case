<?php

declare(strict_types=1);

namespace App\Metrics\Request;

class Request
{
    public function __construct(
        private readonly ?string $method = null,
        private readonly ?string $path = null
    ) {
    }

    /**
     * @param string $request expected like GET /healthcheck HTTP/1.1 Host: localhost:8181 User-Agent: curl/7.88.1
     */
    public static function createFromString(string $request): self
    {
        $matches = [];

        preg_match('/^(?<METHOD>\w+) (?<PATH>(\w|\/)+)/', $request, $matches);

        return new self(
            $matches['METHOD'] ?? null,
                $matches['PATH'] ?? null
        );
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }
}