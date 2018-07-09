<?php
namespace App\View\Helper;

use Cake\View\Helper;

class PhoneHelper extends Helper
{
    public function makeEdit($number)
    {
        $number = preg_replace("/[^0-9]/", "", $number);
        if (strlen($number) == 10) {
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1 $2 $3", $number);
        } elseif (strlen($number) == 11) {
            return preg_replace("/([0-9]{4})([0-9]{3})([0-9]{4})/", "$1 $2 $3", $number);
        } else {
            return $number;
        }
    }
}