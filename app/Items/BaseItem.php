<?php

namespace App\Items;

use ArrayAccess;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

/**
 * Базовая сущность.
 *
 * @package App\Items
 */
class BaseItem implements ArrayAccess, Arrayable
{

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return property_exists($this, $offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset)
            ? $this->$offset
            : null;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function offsetSet($offset, $value)
    {
        throw new Exception('A model property cannot be written');
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->$offset);
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return (array)$this;
    }
}