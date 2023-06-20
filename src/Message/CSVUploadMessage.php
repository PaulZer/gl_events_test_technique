<?php

namespace App\Message;

class CSVUploadMessage
{
    public function __construct(private array $content)
    {

    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function getCSVFileName(): string
    {
        return $this->content['file_name'];
    }
}