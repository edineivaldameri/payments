<?php

declare(strict_types=1);

namespace EdineiValdameri\Pagamentos\ValueObject;

use EdineiValdameri\Pagamentos\Interface\Generate;
use Exception;

class AbsctractPart implements Generate
{
    private array $fields = [];

    public function addField(Field $field): void
    {
        if (isset($this->fields[$field->getFinal()])) {
            throw new Exception('Conflict with field');
        }

        if (isset($this->fields[$field->getInitial()])) {
            throw new Exception('Field already exists');
        }

        $this->fields[$field->getInitial()] = $field;
    }

    public function generate(): string
    {
        if (empty($this->fields)) {
            throw new Exception('Not found fields');
        }

        ksort($this->fields);

        return implode('', array_map(fn (Field $field) => $field->getValue(), $this->fields));
    }
}
