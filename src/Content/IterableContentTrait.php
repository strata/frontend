<?php

declare(strict_types=1);

namespace Strata\Frontend\Content;

use Strata\Data\Traits\IterableTrait;

trait IterableContentTrait
{
    use IterableTrait;

    public function offsetExists($offset)
    {
        return isset($this->collection[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->collection[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->collection[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->collection[$offset]);
        }
    }

}