<?php
namespace C4\Modules\Calendar\Model;

/**
 * created on 2012-02-15 at 12:10:36.
 */
class LeapYearTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LeapYear
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new LeapYear;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers C4\Modules\Calendar\Model\LeapYear::isLeapYear
     * @todo   Implement testIsLeapYear().
     */
    public function testIsLeapYear()
    {
        $year = 2012;
        $this->assertTrue($this->object->isLeapYear($year));
        $this->assertFalse($this->object->isLeapYear(2011));
        // will only work correctly in leap years...
        $this->assertTrue($this->object->isLeapYear(NULL));
        
    }
}