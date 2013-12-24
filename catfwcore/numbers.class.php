<?php
/**
 * Creator:     Carles Mateo
 * Date:        09/02/13 11:46
 * Filename:    strings.class.php
 * Description:
 */

namespace CataloniaFramework;

abstract class Numbers
{

    public static function formatNumber($m_number, $i_decimals = 3, $s_language = USER_LANGUAGE) {

        $s_decimal_separator = '.';
        $s_thousands_separator = ',';

        if ($s_language == 'en') {
            $s_decimal_separator = '.';
            $s_thousands_separator = ',';
        }

        if ($s_language == 'ca') {
            $s_decimal_separator = ',';
            $s_thousands_separator = '.';
        }

        if ($s_language == 'es') {
            $s_decimal_separator = ',';
            $s_thousands_separator = '.';
        }

        $s_result = number_format($m_number, $i_decimals, $s_decimal_separator, $s_thousands_separator);

        return $s_result;

    }


}
