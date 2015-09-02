<?php

/**
 * Creator:      Carles Mateo
 * Date Created: 2013-12-07 09:54 GMT+1
 * Last Updater: Carles Mateo
 * Last Updated: 2013-12-23 12:57 GMT+1
 * Filename:     session.class.php
 * Description:  Class for handling the Session
 */

namespace CataloniaFramework;

abstract class Session {

    const CFW_SESSION_STATUS_OPEN   = 1;
    const CFW_SESSION_STATUS_CLOSED = 0;

    public static $s_session_status = self::CFW_SESSION_STATUS_CLOSED;


    public static function sessionStart() {
        // PHP 5.4
        //if (\session_status() == PHP_SESSION_NONE) {
        // Note: We still keep compatible with PHP 5.3
        if (self::$s_session_status == self::CFW_SESSION_STATUS_CLOSED) {
            \session_start();
            self::$s_session_status = self::CFW_SESSION_STATUS_OPEN;
        }
    }

    public static function getSessionStatus() {
        return self::$s_session_status;
    }

    public static function sessionClose() {

        if (self::$s_session_status == self::CFW_SESSION_STATUS_OPEN) {
            \session_write_close();
            self::$s_session_status = self::CFW_SESSION_STATUS_CLOSED;
        }

    }

    public static function getVarFromSession($s_key) {
        $s_value_returned = null;

        if (self::$s_session_status == self::CFW_SESSION_STATUS_CLOSED) {
            self::openLockingSession();
            $s_value_returned = isset($_SESSION[$s_key]) ? $_SESSION[$s_key] : null;
            self::closeLockingSession();  // Free the session block
        } else {
            $s_value_returned = isset($_SESSION[$s_key]) ? $_SESSION[$s_key] : null;
        }

        return $s_value_returned;
    }

    public static function getDataFromSession() {
        $st_value_returned = null;

        if (self::$s_session_status == self::CFW_SESSION_STATUS_CLOSED) {
            self::openLockingSession();
            $st_value_returned = $_SESSION;
            self::closeLockingSession();  // Free the session block
        } else {
            $st_value_returned = $_SESSION;
        }

        return $st_value_returned;
    }

    public static function openLockingSession() {
        if (self::$s_session_status == self::CFW_SESSION_STATUS_CLOSED) {
            self::sessionStart();
            return \session_id();
        }

        return null;
    }

    public static function closeLockingSession() {
        self::sessionClose();
    }

    public static function setVarToSession($s_key, $m_value = null) {
        // Future PHP 5.4
        //if (\session_status() == PHP_SESSION_ACTIVE) {
        if (self::$s_session_status == self::CFW_SESSION_STATUS_OPEN) {
            $_SESSION[$s_key] = $m_value;
        } else {
            // We open, set, and write-close
            self::openLockingSession();
            $_SESSION[$s_key] = $m_value;
            self::closeLockingSession();
        }
    }

    public static function setDataToSession($st_data = null) {
        //if (\session_status() == PHP_SESSION_ACTIVE) {
        if (self::$s_session_status == self::CFW_SESSION_STATUS_OPEN) {
            $_SESSION = $st_data;
        } else {
            // We open, set, and write-close
            self::openLockingSession();
            $_SESSION = $st_data;
            self::closeLockingSession();
        }
    }

    public static function destroySession() {
        self::setDataToSession(array());
    }

}