<?php

namespace Revo\Sidecar\ExportFields;

class Enum extends ExportField
{
    protected ?array $options = [];

    public function options(array $options) : self
    {
        $this->options = $options;
        return $this;
    }

    public function toHtml($row): string
    {
        return $this->options[parent::toHtml($row)] ?? '';
    }

    public function filterOptions() : array
    {
        return $this->options;
    }
}