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

class Donate extends ControllerBase {

    // Cache the controller
    public $b_cache_controller = true;
    // Cache for 1 hour
    public $i_cache_TTL_seconds = 3600;

    public function actionIndex($s_url = '', $st_params = Array(), $st_params_url = Array(), $o_db = null) {

        $st_parameters = Array();

        $s_view = $this->getView('donate_index', $st_parameters, USER_LANGUAGE);

        return $s_view;
    }

}
