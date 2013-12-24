<?php

/**
 * Creator:      Carles Mateo
 * Date Created: 2013-02-11 00:05
 * Last Updater:
 * Last Updated:
 * Filename:     index.php
 * Description:
 */

namespace CataloniaFramework;

class Json extends ControllerBase {

    // Cache the controller
    public $b_cache_controller = false;
    // Cache for 1 hour
    public $i_cache_TTL_seconds = 3600;

    public $s_content_type = ControllerBase::RESPONSE_JSON;

    public function actionPhpVersion($s_url = '', $st_params = Array(), $st_params_url = Array(), $o_db = null) {

        $s_php_version = phpversion();

        $st_response = Array('php_version' => $s_php_version);

        $s_view = json_encode($st_response);

        return $s_view;
    }

    public function actionDeleteCache($s_url = '', $st_params = Array(), $st_params_url = Array(), $o_db = null) {

        // TODO: Delete

        Translations::loadTranslations(CONTROLLER, USER_LANGUAGE);

        if (Core::loadModel('cachemodel') === false) {
            // TODO: Throw exception an control if it's JSON
            // Right now return problem
            $s_message = t('Failed');
            $s_error_code = '10';

        } else {
            $s_message = t('Success');
            $s_error_code = '0';

            if (CacheModel::deleteCache() == false) {
                $s_message = t('Failed');
                $s_error_code = '1';
            }

        }

        $st_response = Array('operation_result' => $s_message,
                             'error_code'       => $s_error_code);

        $s_view = json_encode($st_response);

        return $s_view;
    }

    public function actionOsVersion($s_url = '', $st_params = Array(), $st_params_url = Array(), $o_db = null) {

        $s_result = exec('uname -a');

        $st_response = Array('os_version' => $s_result);

        $s_view = json_encode($st_response);

        return $s_view;

    }

    public function actionSpaceCache($s_url = '', $st_params = Array(), $st_params_url = Array(), $o_db = null) {
        $f_cache_path_total_space = (disk_total_space(CACHE_ROOT) / 1024) / 1024;
        $f_cache_path_free_space  = (disk_free_space(CACHE_ROOT) / 1024) / 1024;

        $s_cache_path_total_space = Numbers::formatNumber($f_cache_path_total_space, 2, USER_LANGUAGE);
        $s_cache_path_free_space = Numbers::formatNumber($f_cache_path_free_space, 2, USER_LANGUAGE);

        $st_response = Array('total_space_cache'  => $s_cache_path_total_space,
                             'free_space_cache'   => $s_cache_path_free_space);

        $s_view = json_encode($st_response);

        return $s_view;
    }

}
