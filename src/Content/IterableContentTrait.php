<?php

declare(strict_types=1);

namespace Strata\Frontend\Content;

use Strata\Data\Traits\IterableTrait;

/**
 * Iterator functionality
 *
 * Can be used with classes that implement \SeekableIterator, \Countable, \ArrayAccess
 * Colection data is stored in the $this->collection array
 *
 * @see https://www.php.net/seekableiterator
 * @see https://www.php.net/countable
 * @see https://www.php.net/arrayaccess
 * @package Strata\Data\Traits
 */
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