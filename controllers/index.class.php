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

class Index extends ControllerBase {

    // Cache the controller
    public $b_cache_controller = true;
    // Cache for 1 hour
    public $i_cache_TTL_seconds = 3600;

    public function actionIndex($s_url = '', $st_params = Array(), $st_params_url = Array(), $o_db = null) {

        $m_view = $this->getViewIfCached('index_index', USER_LANGUAGE);
        if ($m_view !== null) {
            // Page is right cached
            // Return the result and avoid querying

            // If you have to do something, like increasing a counter of visits do it here
            return $m_view;
        }


        // Parameters that will be passed
        $st_parameters = Array();

        // If needed load translations

        // Call the models and query the Db

        // Get the view passing the result of the queries
        $s_view = $this->getView('index_index', $st_parameters, USER_LANGUAGE);

        return $s_view;
    }

}
