<?php
namespace App\Controller\Component;

use Cake\Controller\Component;

class UtilComponent extends Component
{

    public function convertV2E($str)
    {
        if (!$str) {
            return false;
        }
        $str = trim($str);
        $str = mb_strtoupper($str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
        $str = preg_replace("/(Đ)/", "D", $str);
        return $str;
    }
    
    public function getLastDayOfMonth($month)
    {
        return date("Y-m-t", strtotime($month));
    }

    public function convertDate($value)
    {
        return date('Y-m-d', strtotime($value));
    }

    public function reverseStr($str)
    {
        if (empty($str)) {
            return '';
        }
        $exp = explode('-', $str);
        return trim($exp[1]) . '-' . trim($exp[0]);
    }

    public function replaceDash($str)
    {
        return str_replace("-", "/", $str);
    }
}