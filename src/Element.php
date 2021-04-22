<?php

namespace XML\Support;

class Element extends DataAccess
{
    public function toArray()
    {
        return $this->data;
    }
}
