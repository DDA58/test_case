<?php

declare(strict_types=1);

namespace App\Metrics\Response;

class Response
{
    public const HTTP_OK = 200;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;

    private const STATUS_TEXTS = [
        self::HTTP_OK => 'OK',
        self::HTTP_NOT_FOUND => 'Not Found',
        self::HTTP_INTERNAL_SERVER_ERROR => 'Internal Server Error',
    ];

    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        private readonly string $content = '',
        private readonly int $status = 200,
        private readonly array $headers = []
    ) {
    }

    public function __toString(): string
    {
        $result = sprintf('HTTP/1.1 %d %s', $this->status, self::STATUS_TEXTS[$this->status] ?? 'OK') . PHP_EOL;

        foreach ($this->headers as $name => $value) {
            $result .= ucwords($name, '-') . ':' . $value . PHP_EOL;
        }

        return $result . PHP_EOL . PHP_EOL . $this->content;
    }
}
