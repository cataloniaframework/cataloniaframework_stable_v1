<?php
/**
 * Creator:      Carles Mateo
 * Date:         2013-02-09 11:53
 * Last Updater: Carles Mateo
 * Last Updated: 2014-01-07 14:08
 * Filename:     requests.class.php
 * Description:
 */

namespace CataloniaFramework;

abstract class Requests
{
    const MODE_IP_REQUEST_CLIENT = 'strict';    // Always Ip detected will be returned
    const MODE_IP_REQUEST_PROXY  = 'proxy';     // Ip passed from proxy will be tried to returned

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

    public static function getServerName() {
        $s_server_name = isset($_SERVER['SERVER_NAME']) ? (string)$_SERVER['SERVER_NAME'] : '';

        return $s_server_name;
    }

    public static function getClientIp($s_mode = self::MODE_IP_REQUEST_CLIENT) {
        // Please note, that if we use Load Balancer/CDN proxies we need to use X_FORWARDED_FOR instead.
        // Note too that the headers may have been modified in order to force SQL Injects or others.

        $s_client_ip = $_SERVER["REMOTE_ADDR"];
        $s_ip_returned = $s_client_ip;

        if ($s_mode == self::MODE_IP_REQUEST_PROXY) {
            $s_proxy_ips  = @getenv("HTTP_X_FORWARDED_FOR");
            $st_proxy_ips = explode(",",$s_proxy_ips);
            if (count($st_proxy_ips) > 0) {
                $s_proxy_ip = end($st_proxy_ips);
                // For Security, to prevent Sql Injections through headers
                $s_proxy_ip = str_replace("'", '', $s_proxy_ip);
                $s_ip_returned = $s_proxy_ip;
            }
        }

        // We could check for data integrity via preg_match and set an error
        return trim($s_ip_returned);
    }

    public static function getUserAgent() {
        $s_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        // Remove Sql Injections
        $s_user_agent = str_replace("'", '', $s_user_agent);

        return $s_user_agent;
    }

    public static function getRequestedUrl() {

        $s_request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

        // Remove Sql Injections
        $s_request_uri = str_replace("'", '', $s_request_uri);

        return $s_request_uri;
    }
}
