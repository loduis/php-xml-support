<?php

namespace XML\Support;

class Element extends DataAccess
{
    public static function fromArray(array $data): static
    {
        return new static($data);
    }

    public function toArray()
    {
        return $this->data;
    }
}
