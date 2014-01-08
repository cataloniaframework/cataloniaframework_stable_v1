<?php

/**
 * Creator:      Carles Mateo
 * Date Created: 2013-02-10 23:58
 * Last Updater:
 * Last Updated:
 * Filename:     core.php
 * Description:
 */

use CataloniaFramework\Views as Views;
use CataloniaFramework\Datetime as Datetime;
use CataloniaFramework\ControllerBase as ControllerBase;
use CataloniaFramework\Translations as Translations;

function getViewIfCached($s_view_name, $i_cache_TTL = ControllerBase::DEFAULT_CACHE_TTL, $s_id_language = USER_LANGUAGE) {
    $s_cache_prefix = 'cache-';
    $s_cache_file = CACHE_ROOT.$s_cache_prefix.$s_view_name.'_'.$s_id_language;

    if (file_exists($s_cache_file)) {
        // Get creation date
        $i_file_timestamp = filemtime($s_cache_file);
        $i_time_now = Datetime::getDateTime(Datetime::FORMAT_MICROTIME);
        if ($i_time_now > ($i_file_timestamp + $i_cache_TTL)) {
            return null;
        } else {
            // Up to date
            Views::addSystemVar('STATS_CACHED_CONTROLLER', 'Yes', Views::VAR_ACTION_REPLACE);
            $o_fh = fopen($s_cache_file, "rb");
            $s_html = stream_get_contents($o_fh);
            fclose($o_fh);
            if (strlen($s_html) == 0) {
                return null;
            } else {
                return $s_html;
            }
        }
    } else {
        return null;
    }

}

function getView($s_view_name, $st_view_vars = Array(), $b_cache = false, $i_cache_TTL = ControllerBase::DEFAULT_CACHE_TTL, $s_id_language = USER_LANGUAGE) {

    $b_generate_cache = false;
    $s_cache_prefix = 'cache-';
    $s_cache_file = CACHE_ROOT.$s_cache_prefix.$s_view_name.'_'.$s_id_language;

    if ($b_cache == true) {
        if (file_exists($s_cache_file)) {
            // Get creation date
            $i_file_timestamp = filemtime($s_cache_file);
            $i_time_now = Datetime::getDateTime(Datetime::FORMAT_MICROTIME);
            if ($i_time_now > ($i_file_timestamp + $i_cache_TTL)) {
                $b_generate_cache = true;
            } else {
                // Up to date
                Views::addSystemVar('STATS_CACHED_CONTROLLER', 'Yes', Views::VAR_ACTION_REPLACE);
                $o_fh = fopen($s_cache_file, "rb");
                $s_html = stream_get_contents($o_fh);
                fclose($o_fh);
                if (strlen($s_html) == 0) {
                    $b_generate_cache = true;
                } else {
                    return $s_html;
                }
            }
        } else {
            $b_generate_cache = true;
        }
    }

    ob_start();

    $s_file = VIEWS_ROOT.$s_view_name.'_'.$s_id_language.'.php';

    if (file_exists($s_file)) {
        Translations::loadTranslations(CONTROLLER, $s_id_language);
        require $s_file;
    } else {
        // On error deactivate cache
        $b_generate_cache = false;

        $i_error_code = Views::ERROR_FILE_NOT_FOUND;
        $s_error_msg  = Views::$st_ERROR_MESSAGES[Views::ERROR_FILE_NOT_FOUND];
        require VIEWS_ROOT.'errors/errorgeneric.php';
    }

    $s_html = ob_get_clean();

    if ($b_generate_cache == true) {
        $o_fp = fopen($s_cache_file, 'w');
        if (fwrite($o_fp, $s_html) === false) {
            throw new Exception('CacheCantWrite');
        }
        fclose($o_fp);
    }

    return $s_html;

}

function getErrorView($i_error_code, $s_error_msg, $s_error_view_name = 'errorgeneric') {

    ob_start();

    $s_file = VIEWS_ROOT.'errors/'.$s_error_view_name.'.php';

    if (file_exists($s_file)) {
        require $s_file;
    } else {
        // Can't find the error file
        if (file_exists(VIEWS_ROOT.'errors/errorgeneric.php')) {
            $i_error_code = Views::ERROR_TEMPLATE_ERROR_FILE_NOT_FOUND;
            $s_error_msg  = Views::$st_ERROR_MESSAGES[Views::ERROR_TEMPLATE_ERROR_FILE_NOT_FOUND];
            require VIEWS_ROOT.'errors/errorgeneric.php';
        } else {
            ob_end_clean();
            decho('Error. File '.$s_file.' not found');
            Core::end();
        }
    }

    return ob_get_clean();

}

function getFile($s_file) {

    ob_start();

    if (file_exists($s_file)) {
        require $s_file;
    } else {
        $i_error_code = Views::ERROR_FILE_NOT_FOUND;
        $s_error_msg  = Views::$st_ERROR_MESSAGES[Views::ERROR_FILE_NOT_FOUND];
        require VIEWS_ROOT.'errors/errorgeneric.php';
    }

    return ob_get_clean();
}

function decho($m_message, $b_add_br = true) {
    // It prints a debug line only if in DEVELOPMENT.
    // That saves you the shame when a Developer forgets a debug echo in Production. It will not be printed.
    if (ENVIRONMENT == DEVELOPMENT) {
        if (is_string($m_message)) {
            echo 'Debug: ';
            echo '<strong>'.$m_message."</strong>\n";
            echo ' (length: '.strlen($m_message).' chacaracters'.")\n";
        } else {
            echo 'Debug: <strong>';
            var_export($m_message)."</strong>\n";
            echo ' (approx. length: '.strlen(var_export($m_message, true)).")\n";
        }
        if ($b_add_br == true) {
            echo '<br />';
        }
    }
}

function t($s_id_translation, $s_id_language = USER_LANGUAGE) {

    return Translations::getTranslation($s_id_translation, $s_id_language);

}

