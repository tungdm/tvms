<?php
namespace App\View\Helper;

use Cake\View\Helper;

class MonthHelper extends Helper
{
    public function makeEdit($str)
    {
        if (empty($str)) {
            return '';
        }
        $exp = explode('-', $str);
        return trim($exp[1]) . '-' . trim($exp[0]);
    }
}