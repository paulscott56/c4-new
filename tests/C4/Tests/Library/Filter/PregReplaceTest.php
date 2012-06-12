<?php 

namespace C4\Tests\Library\Filter;

use C4\Library\Filter\PregReplace as PregReplaceFilter;

class PregReplaceTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->filter = new PregReplaceFilter();
    }

    public function testPassingMatchPatternToConstructorSetsMatchPattern()
    {
        $pattern = '#^controller/(?P<action>[a-z_-]+)#';
        $filter  = new PregReplaceFilter($pattern);
        $this->assertEquals($pattern, $filter->getMatchPattern());
    }

    public function testPassingReplacementToConstructorSetsReplacement()
    {
        $replace = 'foo/bar';
        $filter  = new PregReplaceFilter(null, $replace);
        $this->assertEquals($replace, $filter->getReplacement());
    }

    public function testIsUnicodeSupportEnabledReturnsSaneValue()
    {
        $enabled = (@preg_match('/\pL/u', 'a')) ? true : false;
        $this->assertEquals($enabled, $this->filter->isUnicodeSupportEnabled());
    }

    public function testMatchPatternInitiallyNull()
    {
        $this->assertNull($this->filter->getMatchPattern());
    }

    public function testMatchPatternAccessorsWork()
    {
        $pattern = '#^controller/(?P<action>[a-z_-]+)#';
        $this->filter->setMatchPattern($pattern);
        $this->assertEquals($pattern, $this->filter->getMatchPattern());
    }

    public function testReplacementInitiallyEmpty()
    {
        $replacement = $this->filter->getReplacement();
        $this->assertTrue(empty($replacement));
    }

    public function testReplacementAccessorsWork()
    {
        $replacement = 'foo/bar';
        $this->filter->setReplacement($replacement);
        $this->assertEquals($replacement, $this->filter->getReplacement());
    }

    public function testFilterPerformsRegexReplacement()
    {
        $filter = $this->filter;
        $string = 'controller/action';
        $filter->setMatchPattern('#^controller/(?P<action>[a-z_-]+)#')
               ->setReplacement('foo/bar');
        $filtered = $filter($string);
        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('foo/bar', $filtered);
    }

    public function testFilterThrowsExceptionWhenNoMatchPatternPresent()
    {
        $filter = $this->filter;
        $string = 'controller/action';
        $filter->setReplacement('foo/bar');
        $this->setExpectedException('\C4\Library\Filter\Exception\RuntimeException', 'does not have a valid MatchPattern set');
        $filtered = $filter($string);
    }

    public function testExtendsPregReplace()
    {
        $startMatchPattern = '~(&gt;){3,}~i';
        $filter = new XPregReplace();
        $this->assertEquals($startMatchPattern, $filter->getMatchPattern());
    }
}

class XPregReplace extends PregReplaceFilter
{
    protected $_matchPattern = '~(&gt;){3,}~i';
}