<?php 
namespace C4\Library\Filter\Word;

class CamelCaseToUnderscore extends CamelCaseToSeparator
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct('_');
    }
}