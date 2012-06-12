<?php

namespace C4\Library\Filter\Word;

class CamelCaseToSeparator extends AbstractSeparator
{
    /**
     * Defined by C4\Library\Filter\Filter
     * 
     * @param  string $value 
     * @return string
     */
    public function filter($value)
    {
        if (self::isUnicodeSupportEnabled()) {
            parent::setMatchPattern(array('#(?<=(?:\p{Lu}))(\p{Lu}\p{Ll})#','#(?<=(?:\p{Ll}|\p{Nd}))(\p{Lu})#'));
            parent::setReplacement(array($this->_separator . '\1', $this->_separator . '\1'));
        } else {
            parent::setMatchPattern(array('#(?<=(?:[A-Z]))([A-Z]+)([A-Z][A-z])#', '#(?<=(?:[a-z0-9]))([A-Z])#'));
            parent::setReplacement(array('\1' . $this->_separator . '\2', $this->_separator . '\1'));
        }

        return parent::filter($value);
    }
}