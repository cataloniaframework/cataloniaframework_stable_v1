<?php

/**
 * Creator:      Carles Mateo
 * Date Created: 2013-02-26 21:55
 * Last Updater:
 * Last Updated:
 * Filename:     translations.class.php
 * Description:
 */

namespace CataloniaFramework;

abstract class Translations
{

    public static $st_translations = Array();
    private static $st_loaded_views = Array();

    public static function loadTranslations($s_controller_name, $s_id_lang = USER_LANGUAGE) {

        $s_controller_name = strtolower($s_controller_name);
        $st_translations = Array();

        if (!in_array($s_controller_name.'_'.$s_id_lang, self::$st_loaded_views, true) &&
            file_exists(TRANSLATIONS_ROOT.$s_controller_name.'_'.$s_id_lang.'.php'))
        {
            require TRANSLATIONS_ROOT.$s_controller_name.'_'.$s_id_lang.'.php';

            if (!isset(self::$st_translations[$s_id_lang])) {
                // Init with the key to avoid array_merge returning empty array PHP 5.3.10
                self::$st_translations[$s_id_lang] = Array();
            }
            self::$st_translations[$s_id_lang] = array_merge(self::$st_translations[$s_id_lang], $st_translations);

            self::$st_loaded_views[] = $s_controller_name.'_'.$s_id_lang;
        }

    }

    public static function getTranslation($s_id_translation, $s_id_lang = USER_LANGUAGE) {
        // $s_id_translation is the text in English

        if (isset(self::$st_translations[$s_id_lang][$s_id_translation])) {
             return self::$st_translations[$s_id_lang][$s_id_translation];
         } else {
             return $s_id_translation;
         }
    }

}
