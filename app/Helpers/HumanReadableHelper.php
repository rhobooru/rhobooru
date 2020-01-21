<?php

namespace App\Helpers;

class HumanReadableHelper
{
    public static function toBytes($val) 
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);

        $numeric = trim(substr($val, 0, -1));
        
        switch($last) 
        {
            default:
                throw new \Exception('Unknown unit: [' . $val . ']');

            case 't':
                $numeric *= 1024;

            case 'g':
                $numeric *= 1024;

            case 'm':
                $numeric *= 1024;

            case 'k':
                $numeric *= 1024;

        }

        return $numeric;
    }
}