<?php

namespace C4\Library\Filter;

use Traversable;
    //C4\Library\Stdlib\ArrayUtils;

class PregReplace extends AbstractFilter
{
    /**
     * Pattern to match
     * @var mixed
     */
    protected $_matchPattern = null;

    /**
     * Replacement pattern
     * @var mixed
     */
    protected $_replacement = '';

    /**
     * Is unicode enabled?
     *
     * @var bool
     */
    static protected $_unicodeSupportEnabled = null;

    /**
     * Is Unicode Support Enabled Utility function
     *
     * @return bool
     */
    static public function isUnicodeSupportEnabled()
    {
        if (self::$_unicodeSupportEnabled === null) {
            self::_determineUnicodeSupport();
        }

        return self::$_unicodeSupportEnabled;
    }

    /**
     * Method to cache the regex needed to determine if unicode support is available
     *
     * @return bool
     */
    static protected function _determineUnicodeSupport()
    {
        self::$_unicodeSupportEnabled = (@preg_match('/\pL/u', 'a')) ? true : false;
    }

    /**
     * Constructor
     * Supported options are
     *     'match'   => matching pattern
     *     'replace' => replace with this
     *
     * @param  string|array $options
     * @return void
     */
    public function __construct($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            $options = func_get_args();
            $temp    = array();
            if (!empty($options)) {
                $temp['match'] = array_shift($options);
            }

            if (!empty($options)) {
                $temp['replace'] = array_shift($options);
            }

            $options = $temp;
        }

        if (array_key_exists('match', $options)) {
            $this->setMatchPattern($options['match']);
        }

        if (array_key_exists('replace', $options)) {
            $this->setReplacement($options['replace']);
        }
    }

    /**
     * Set the match pattern for the regex being called within filter()
     *
     * @param mixed $match - same as the first argument of preg_replace
     * @return PregReplace
     */
    public function setMatchPattern($match)
    {
        $this->_matchPattern = $match;
        return $this;
    }

    /**
     * Get currently set match pattern
     *
     * @return string
     */
    public function getMatchPattern()
    {
        return $this->_matchPattern;
    }

    /**
     * Set the Replacement pattern/string for the preg_replace called in filter
     *
     * @param mixed $replacement - same as the second argument of preg_replace
     * @return PregReplace
     */
    public function setReplacement($replacement)
    {
        $this->_replacement = $replacement;
        return $this;
    }

    /**
     * Get currently set replacement value
     *
     * @return string
     */
    public function getReplacement()
    {
        return $this->_replacement;
    }

    /**
     * Perform regexp replacement as filter
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        if ($this->_matchPattern == null) {
            throw new Exception\RuntimeException(get_class($this) . ' does not have a valid MatchPattern set.');
        }

        return preg_replace($this->_matchPattern, $this->_replacement, $value);
    }
}