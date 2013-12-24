<?php
/**
 * Creator:     Carles Mateo
 * Date:        09/02/13 11:46
 * Filename:    strings.class.php
 * Description:
 */

namespace CataloniaFramework;

abstract class Strings
{

    public static function getParamsFromURL($s_params) {

        $st_params = Array();

        if (!is_string($s_params) || $s_params == '') {
            return $st_params;
        } else {
            if ($s_params[strlen($s_params)-1] == '/') {
                $s_params = substr($s_params, 0, -1);
            }
        }

        // Remove Array injections
        $st_params = explode('/', $s_params);
        foreach($st_params as $i_key=>$s_value) {
            if (is_array($st_params[$i_key])) {
                unset($st_params[$i_key]);
            }
        }

        return $st_params;

    }

    public static function getSanitizedControllerName($s_name) {
        $s_name = strtolower($s_name);
        $s_sanitized= preg_replace('/([^a-z0-9])/', '', $s_name);

        $s_sanitized = ucfirst($s_sanitized);

        return $s_sanitized;
    }

}
