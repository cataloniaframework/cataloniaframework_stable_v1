<?php
/**
 * Creator:      Carles Mateo
 * Date:         2013-02-09 11:53
 * Last Updater: Carles Mateo
 * Last Updated: 2013-12-23 16:52
 * Filename:     requests.class.php
 * Description:
 */

namespace CataloniaFramework;

abstract class Requests
{

    public static function isPostRequest() {
        if ($_POST) {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function getParamStringGET($s_param) {
        // Return the value of the param requested by GET as string
        // or false if is not set or is passed as array []

        $s_value = '';

        if (isset($_GET[$s_param]) && is_string($_GET[$s_param])) {
            $s_value = $_GET[$s_param];
        } else {
            $s_value = false;
        }

        return $s_value;
    }

    public static function getHttpReferer() {
        $s_referer = isset($_SERVER['HTTP_REFERER']) ? (string)$_SERVER['HTTP_REFERER'] : '';
        // Remove Sql Injections
        $s_referer = str_replace("'", '', $s_referer);

        return $s_referer;
    }

}
