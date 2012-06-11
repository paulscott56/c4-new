<?php

namespace C4\Library\Filter;

abstract class AbstractFilter implements FilterInterface
{
    /**
     * Invoke filter as a command
     *
     * Proxies to {@link filter()}
     *
     * @param  mixed $value
     * @throws Exception\ExceptionInterface If filtering $value is impossible
     * @return mixed
     */
    public function __invoke($value)
    {
        return $this->filter($value);
    }
}