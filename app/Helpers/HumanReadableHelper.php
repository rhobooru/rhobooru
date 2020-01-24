<?php

namespace App\Helpers;

class HumanReadableHelper
{
    /**
     * Convert a human-readable binary size string
     * to a bare number of bytes. (eg. "10 k" => 1024)
     *
     * @param string $val
     *
     * @return void
     */
    public static function toBytes(string $val): int
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        $numeric = trim(substr($val, 0, -1));

        switch ($last) {
            default:
                throw new \Exception("Unknown unit: [${val}]");
            case 't':
                $numeric *= 1024;
                // No break.
            case 'g':
                $numeric *= 1024;
                // No break.
            case 'm':
                $numeric *= 1024;
                // No break.
            case 'k':
                return $numeric * 1024;
        }
    }
}
