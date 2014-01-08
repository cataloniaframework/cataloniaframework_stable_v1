<?php
/**
 * Creator:      Carles Mateo
 * Date Created: 2013-02-09 16:19
 * Last Updater: Carles Mateo
 * Last Updated: 2014-01-08 02:05
 * Filename:     Core.class.php
 * Description:
 */

namespace CataloniaFramework;

abstract class Core
{

    public static function end($o_db = null) {

        // Call Custom End
        CommonRequests::endRequest($o_db);

        exit();
    }

    public static function redirectUserToErrorPage() {

    }

    public static function redirectUserToUrl($s_url) {
        header("Location: $s_url");
    }

    public static function isLanguageActive($s_language) {
        global $p_st_languages;

        if (array_key_exists($s_language, $p_st_languages)) {
            if ($p_st_languages[$s_language]['active'] == true) {
                return true;
            }
        }

        return false;
    }

    public static function loadController($s_controller) {

        $s_controller = strtolower($s_controller);

        if (file_exists(CONTROLLERS_ROOT.$s_controller.'.class.php')) {
            require_once CONTROLLERS_ROOT.$s_controller.'.class.php';
            return true;
        } else {
            return false;
        }
    }

    public static function loadModel($s_model) {

        $s_model = strtolower($s_model);

        if (file_exists(MODELS_ROOT.$s_model.'.class.php')) {
            require_once MODELS_ROOT.$s_model.'.class.php';
            return true;
        } else {
            return false;
        }

    }

    public static function isValidName($s_string) {
        return ctype_alnum($s_string);
    }

}
