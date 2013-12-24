<?php

/**
 * Creator:      Carles Mateo
 * Date Created: 2013-02-11 00:21
 * Last Updater:
 * Last Updated:
 * Filename:     controllerbase.class.php
 * Description: This is a class that is extended by the controllers
 */

namespace CataloniaFramework;

use CataloniaFramework\Navigation as Navigation;

// Note: This classes are instantiated when used only instead of abstract in order to save memory
//       And to be able to perform actions on __construct()
class ControllerBase
{

    const RESPONSE_TEXTHTML = 'text/html';
    const RESPONSE_TEXT     = 'text/plain';
    const RESPONSE_JSON     = 'application/json';

    const CACHE_DO_NOT_SEND = 'DO_NOT_SEND';
    const CACHE_NO_CACHE    = 'NO_CACHE';

    const DEFAULT_CACHE_TTL = 600;      // 10 minutes

    public $st_headers  = Array();
    public $s_content_type  = ControllerBase::RESPONSE_TEXTHTML;
    public $s_cache_type    = ControllerBase::CACHE_NO_CACHE;
    // TODO: Add more variety

    public $b_cache_controller  = false;
    public $i_cache_TTL_seconds = self::DEFAULT_CACHE_TTL;

    public function __construct() {

    }

    public function actionIndex($s_url = '', $st_params = Array(), $st_params_url = Array(), $o_db = null) {
    // This is the default action called if the controller is called with no other provided

    }

    public function sendHeaders() {

        if ($this->s_content_type == ControllerBase::RESPONSE_TEXTHTML) {
            $s_content_header = 'Content-Type: text/html';

        }

        if ($this->s_content_type == ControllerBase::RESPONSE_TEXT) {
            $s_content_header = 'Content-Type: text/plain';
        }

        if ($this->s_content_type == ControllerBase::RESPONSE_JSON) {
            $s_content_header = 'Content-Type: application/json';
        }

        // Cache
        if ($this->s_cache_type == ControllerBase::CACHE_NO_CACHE) {
            $s_cache_header = session_cache_limiter('nocache');
        }


        // Send Content-type
        if ($s_content_header) {
            header($s_content_header);
        }

        if (isset($s_cache_header)) {
            header($s_cache_header);
        }

        // Send user Headers
        foreach($this->st_headers as $i_key=>$s_header_value) {
            header($s_header_value);
        }

    }

    public function CATFW_getCustomContent($s_path = '', $o_db = null) {
        $s_html = Navigation::getCustomData($s_path);

        $this->s_content_type = Navigation::getCustomDataContentType($s_path);
        $this->st_headers = Navigation::getCustomDataHeaders($s_path);

        return $s_html;
    }

    public function getViewIfCached($s_view_name, $s_id_language) {

        if ($this->b_cache_controller == false) {
            // If the controller is not set as to cache avoid checking for cache files

            return null;
        }

        $i_cache_TTL = $this->i_cache_TTL_seconds;

        $m_html = getViewIfCached($s_view_name, $i_cache_TTL, $s_id_language);

        return $m_html;

    }

    public function getView($s_view_name, $st_view_vars = Array(), $s_id_language = USER_LANGUAGE) {

        $b_cache_controller = $this->b_cache_controller;
        $i_cache_TTL = $this->i_cache_TTL_seconds;

        $s_html = getView($s_view_name, $st_view_vars, $b_cache_controller, $i_cache_TTL, $s_id_language);

        return $s_html;
    }

}
