<?php

/**
 * Creator:      Carles Mateo
 * Date Created: 2013-02-11 20:54
 * Last Updater:
 * Last Updated:
 * Filename:     views.class.php
 * Description:
 */

namespace CataloniaFramework;

abstract class Views
{

    const ERROR_FILE_NOT_FOUND = 1;

    const ERROR_CONTROLLER_OR_ACTION_NOT_FOUND = 10;

    const ERROR_INTERNAL_ERROR = 1000;
    const ERROR_EXCEPTION_ERROR = 1001;

    const ERROR_TEMPLATE_ERROR_FILE_NOT_FOUND = 1100;

    const VAR_PREFIX    = '||*||[';
    const VAR_POSFIX    = ']||*||';
    const VAR_SEPARATOR = '|';
    const VAR_ACTION_CALL_FUNCTION = 'CALL';
    const VAR_ACTION_REPLACE = 'REPLACE';

    const SYSTEMVAR_PREFIX = 'CATFW_';

    public static $st_ERROR_MESSAGES = Array(self::ERROR_FILE_NOT_FOUND                  => 'File not found',
                                             self::ERROR_CONTROLLER_OR_ACTION_NOT_FOUND  => 'Controller or Action not found',
                                             self::ERROR_INTERNAL_ERROR                  => 'Internal error',
                                             self::ERROR_EXCEPTION_ERROR                 => 'Exception error',
                                             self::ERROR_TEMPLATE_ERROR_FILE_NOT_FOUND   => 'Error Template not found');

    public static $st_USERVARS       = Array();

    protected static $st_SYSTEMVARS = Array('CATFW_DATETIME'                => Array('action'   => self::VAR_ACTION_CALL_FUNCTION,
                                                                                     'function' => 'CataloniaFramework\Datetime::getDateTime'),
                                            'CATFW_FRAMEWORK_VERSION'       => Array('action'   => self::VAR_ACTION_REPLACE,
                                                                                     'string'   => FRAMEWORK_VERSION),
                                            'CATFW_EXECUTION_TIME'          => Array('action'   => self::VAR_ACTION_REPLACE,
                                                                                     'string'   => '0'),
                                            'CATFW_STATS_CACHED_CONTROLLER' => Array('action'   => self::VAR_ACTION_REPLACE,
                                                                                     'string'   => 'No'),
                                            'CATFW_STATS_VARS_NUMBER_USED'  => Array('action'   => self::VAR_ACTION_REPLACE,
                                                                                     'string'   => '0'),
                                            'CATFW_STATS_VARS_REPLACEMENT'  => Array('action'   => self::VAR_ACTION_REPLACE,
                                                                                     'string'   => '0')
                                          );

    public static function addUserVar($s_name, $s_function_or_string, $s_action = self::VAR_ACTION_REPLACE) {

        if (is_array($s_function_or_string)) return;

        self::addVar($s_name, $s_function_or_string, $s_action, self::$st_USERVARS);

    }

    public static function addSystemVar($s_name, $s_function_or_string, $s_action = Views::VAR_ACTION_REPLACE) {

        if (is_array($s_function_or_string)) return;

        self::addVar(self::SYSTEMVAR_PREFIX.$s_name, $s_function_or_string, $s_action, self::$st_SYSTEMVARS);

    }

    protected static function addVar($s_name, $s_function_or_string, $s_action = Views::VAR_ACTION_REPLACE, &$st_array){
        if (is_array($s_function_or_string)) return;

        if ($s_action == self::VAR_ACTION_CALL_FUNCTION) {
            $s_action_to_do = self::VAR_ACTION_CALL_FUNCTION;
            $s_key = 'function';
        } else {
            $s_action_to_do = self::VAR_ACTION_REPLACE;
            $s_key = 'string';

            if (is_numeric($s_function_or_string)) {
                // Ensure conversion to string
                $s_function_or_string = strval($s_function_or_string);
            }
        }

        $st_array[strtoupper($s_name)] = Array('action'=> $s_action_to_do,
                                               $s_key  => $s_function_or_string);

    }

    public static function getVar($s_name, $st_vars) {

        $s_result = null;

        if (isset($st_vars[$s_name])) {
            // Replaced by the return of a function
            if ($st_vars[$s_name]['action'] == self::VAR_ACTION_CALL_FUNCTION) {
                if (isset($st_vars[$s_name]['function']) && is_string($st_vars[$s_name]['function'])) {
                    $s_function = $st_vars[$s_name]['function'];
                    $s_parameters = '';
                    $s_result = call_user_func($s_function, $s_parameters);
                }
            }
            // Replace by literal
            if ($st_vars[$s_name]['action'] == self::VAR_ACTION_REPLACE) {
                if (isset($st_vars[$s_name]['string']) && is_string($st_vars[$s_name]['string'])) {
                    $s_result = $st_vars[$s_name]['string'];
                }
            }
        }

        return $s_result;
    }

    public static function getUserVar($s_name) {
        return self::getVar($s_name, self::$st_USERVARS);
    }

    public static function getSystemVar($s_name) {
        return self::getVar(self::SYSTEMVAR_PREFIX.$s_name, self::$st_SYSTEMVARS);
    }

    public static function replaceUserVars(&$s_html) {

        self::replaceVars($s_html, self::$st_USERVARS);
    }

    public static function replaceSystemVars(&$s_html) {

        self::replaceVars($s_html, self::$st_SYSTEMVARS);
    }

    public static function replaceVars(&$s_html, &$st_vars_to_replace) {
    // Note that $st_vars_to_replace is passed by reference in order to allow live updates of the vars while in the bucle
    // The best case of this is the var STATS_VARS_REPLACEMENT

        foreach($st_vars_to_replace as $s_key=>$st_vars) {
            $i_replaced = 0;

            $s_var = self::VAR_PREFIX.$s_key;
            $i_ini_pos = strpos($s_html, $s_var);
            if ($i_ini_pos !== false) {
                $i_end_pos = strpos($s_html, self::VAR_POSFIX, $i_ini_pos);
                if ($i_end_pos !== false) {
                    $i_length = $i_end_pos + strlen(self::VAR_POSFIX) - $i_ini_pos;
                    // For example: $s_to_be_replace = '||*||[APP_TITLE]||*||'
                    $s_to_be_replaced = substr($s_html, $i_ini_pos, $i_length);

                    // Replaced by the return of a function
                    if ($st_vars['action'] == self::VAR_ACTION_CALL_FUNCTION) {
                        if (isset($st_vars['function']) && is_string($st_vars['function'])) {
                            // Get the parameters
                            $st_parameters = array();
                            if (substr($s_to_be_replaced, strlen(self::VAR_PREFIX.$s_key),1) == self::VAR_SEPARATOR) {
                                // The Var has parameters
                                $s_params = substr($s_to_be_replaced, strlen(self::VAR_PREFIX) + strlen($s_key) + strlen(self::VAR_SEPARATOR), strlen($s_to_be_replaced) - strlen(self::VAR_PREFIX) - strlen($s_key) -strlen(self::VAR_SEPARATOR) - strlen(self::VAR_POSFIX));
                                $st_parameters = explode(self::VAR_SEPARATOR, $s_params);
                            }

                            $s_function = $st_vars['function'];
                            $s_to_replace = call_user_func($s_function, $st_parameters);
                            if ($s_to_replace !== false) {
                                $s_html = str_replace($s_to_be_replaced, $s_to_replace, $s_html, $i_replaced);
                            }
                        }
                    }
                    // Replace by literal
                    if ($st_vars['action'] == self::VAR_ACTION_REPLACE) {
                        if (isset($st_vars['string']) && is_string($st_vars['string'])) {
                            $s_parameters = '';
                            $s_to_replace = $st_vars['string'];
                            $s_html = str_replace($s_to_be_replaced, $s_to_replace, $s_html, $i_replaced);
                        }
                    }

                    // Get number of replacements
                    // Increase the System Stats on Vars replaced
                    if ($i_replaced>0) {
                        $i_total_replaced_vars = intval(self::getSystemVar('STATS_VARS_REPLACEMENT')) + $i_replaced;
                        self::addSystemVar('STATS_VARS_REPLACEMENT', strval($i_total_replaced_vars),self::VAR_ACTION_REPLACE);
                        $i_total_vars_used = intval(self::getSystemVar('STATS_VARS_NUMBER_USED')) + 1;
                        self::addSystemVar('STATS_VARS_NUMBER_USED', strval($i_total_vars_used),self::VAR_ACTION_REPLACE);

                        // Ok, we replaced at least one occurrence so we perform a recursive call until found none
                        self::replaceVars($s_html, $st_vars_to_replace[$s_key]);

                    }

                }
            }
        }

    }

}
