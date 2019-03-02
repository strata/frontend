<?php

namespace Studio24\Frontend\Content;

/**
 * Simple class to manage metadata we want to expose to Frontend for list results
 *
 * @package Studio24\Frontend\Content
 */
class Metadata implements \ArrayAccess
{
    protected $metadata = [];

    /**
     * Add metadata property
     *
     * @param string $key Metadata property name
     * @param $value Metadata value
     */
    public function add(string $key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Does the metadata property exist?
     *
     * @param $offset Metadata property name
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->metadata[$offset]);
    }

    /**
     * Return metadata property
     *
     * @param $offset Metadata property name
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->metadata[$offset];
    }

    /**
     * Set metadata property
     *
     * @param $offset Metadata property name
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->metadata[$offset] = $value;
    }

    /**
     * Unset metadata property
     *
     * @param $offset Metadata property name
     */
    public function offsetUnset($offset)
    {
        unset($this->metadata[$offset]);
    }

    /**
     * Direct access to metadata properties
     *
     * @param string $name
     * @param $arguments
     * @return mixed|null
     */
    public function __get(string $name)
    {
        if ($this->offsetExists($name)) {
            return $this->offsetGet($name);
        }
        return null;
    }

    /**
     * Direct access to isset/empty on metadata properties
     *
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return $this->offsetExists($name);
    }

    /**
     * Direct access to unset on metadata properties
     *
     * @param string $name
     */
    public function __unset(string $name)
    {
        $this->offsetUnset($name);
    }

    /**
     * Return text representation of all metadata values
     *
     * @return string
     */
    public function __toString()
    {
        $content = [];
        foreach ($this->metadata as $key => $item) {
            $content[] =  (string) $key . ': ' . (string) $item;
        }
        return implode(', ', $content);
    }
}
