<?php
 
// src/C4/Modules/Calendar/Controller/LeapYearController.php
 
namespace C4\Modules\Calendar\Controller;
 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use C4\Modules\Calendar\Model\LeapYear;
 
class LeapYearController
{
    public function indexAction(Request $request, $year)
    {
        $leapyear = new LeapYear();
        var_dump($leapyear); die();
        if ($leapyear->isLeapYear($year)) {
            $response = new Response('Yep, this is a leap year!');
        } else {
            $response = new Response('Nope, this is not a leap year.');
        }
 
        //$response->setTtl(10);
 
        return $response;
    }
}
