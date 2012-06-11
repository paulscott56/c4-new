<?php 

namespace C4\Library\Filter;

interface FilterInterface
{
    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws C4\Library\Filter\Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value);
}