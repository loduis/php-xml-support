<?php

namespace XML\Support;

class Element extends DataAccess
{
    public static function fromArray(array $data): self
    {
        return new static($data);
    }

    public function toArray()
    {
        return $this->data;
    }
}
