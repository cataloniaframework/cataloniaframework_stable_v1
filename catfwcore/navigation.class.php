<?php

    /**
     * Creator:      Carles Mateo
     * Date Created: 2013-02-23 11:05
     * Last Updater:
     * Last Updated:
     * Filename:     navigation.class.php
     * Description:  For custom user's Urls navigation
     */

namespace CataloniaFramework;

use CataloniaFramework\ControllerBase as ControllerBase;

abstract class Navigation
{
    const ACTION_REQUIRE_FILE   = 'require';
    const ACTION_REQUIRE_URL    = 'url';
    const ACTION_CALL_FUNCTION  = 'call';

    const SEARCH_MODE_STRICT    = true;

    public static $st_NAVIGATION = Array();

    public static function addURL($s_path, $s_file_or_url, $s_action = self::ACTION_REQUIRE_FILE,
                                  $s_content_type = ControllerBase::RESPONSE_TEXTHTML, $st_headers = Array()) {

        // Add custom URL that will not be processed as controller/action

        if ($s_action == self::ACTION_REQUIRE_FILE) {
            // TODO: require_file
            self::$st_NAVIGATION[$s_path] = Array(  'action'        => $s_action,
                                                    'file'          => $s_file_or_url,
                                                    'content-type'  => $s_content_type,
                                                    'headers'       => $st_headers);

        }

        if ($s_action == self::ACTION_REQUIRE_URL) {
            self::$st_NAVIGATION[$s_path] = Array(  'action'        => $s_action,
                                                    'url'           => $s_file_or_url,
                                                    'content-type'  => $s_content_type,
                                                    'headers'       => $st_headers);
        }

        if ($s_action == self::ACTION_CALL_FUNCTION) {
            self::$st_NAVIGATION[$s_path] = Array(  'action'        => $s_action,
                                                    'function'      => $s_file_or_url,
                                                    'content-type'  => $s_content_type,
                                                    'headers'       => $st_headers);
        }

    }

    public static function isURLCustom($s_path = '') {

        if ($s_path == '/') {
            $s_path = '';
        }

        $b_found = in_array($s_path, array_keys(self::$st_NAVIGATION), self::SEARCH_MODE_STRICT);

        return $b_found;

    }

    public static function getCustomData($s_path = '') {

        if ($s_path == '/') {
            $s_path = '';
        }

        if (isset(self::$st_NAVIGATION[$s_path])) {
            $s_action = self::$st_NAVIGATION[$s_path]['action'];
            if ($s_action == self::ACTION_REQUIRE_FILE) {
                if (!isset(self::$st_NAVIGATION[$s_path]['file'])) {
                    throw new Exception('CustomFileNotDefined');
                }
                $s_file = self::$st_NAVIGATION[$s_path]['file'];
                if (!file_exists($s_file)) {
                    throw new Exception('CustomFileNotFound');
                }

                $s_html = getFile($s_file);

                return $s_html;
            }
            // TODO: Other methods
        }

    }

    public static function getCustomDataContentType($s_path = '') {
        if ($s_path == '/') {
            $s_path = '';
        }

        if (isset(self::$st_NAVIGATION[$s_path])) {
            $s_header = self::$st_NAVIGATION[$s_path]['content-type'];

            return $s_header;
        }

        throw new Exception('CustomFileNotDefined');

    }


    public static function getCustomDataHeaders($s_path = '') {
        if ($s_path == '/') {
            $s_path = '';
        }

        if (isset(self::$st_NAVIGATION[$s_path])) {
            $st_headers = self::$st_NAVIGATION[$s_path]['headers'];

            return $st_headers;
        }

        throw new Exception('CustomFileNotDefined');

    }

}
