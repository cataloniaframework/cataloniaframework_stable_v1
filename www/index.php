<?php

/**
 * Creator:      Carles Mateo
 * Date Created: 2013-02-07 12:51
 * Last Updater: Carles Mateo
 * Last Updated: 2013-12-29 22:30
 * Filename:     index.php
 * Description:  Small index.php that do all the jobs and catch the Exceptions
 */


use CataloniaFramework\Views as Views;
use CataloniaFramework\Core as Core;
use CataloniaFramework\Navigation as Navigation;

try {
    $i_start_time = microtime(true);

    require_once '../catfwcore/bootstrap.php';
    require_once CUSTOM_INIT_ROOT.'bootstrap.php';

    if (Navigation::isURLCustom(REQUESTED_PATH)) {
        // custom url
        $s_html = $o_controller->$s_action(REQUESTED_PATH, $o_db);
    } else {
        // MVC pattern
        $s_html = $o_controller->$s_action(REQUESTED_PATH, $st_params, $st_params_url, $o_db);
    }

    Views::replaceUserVars($s_html);
    // Finish time after user work
    $i_finish_time = microtime(true);
    $i_execution_time = $i_finish_time-$i_start_time;
    Views::addSystemVar('EXECUTION_TIME', $i_execution_time, Views::VAR_ACTION_REPLACE);
    // TODO: SetSystemvar finish time
    Views::replaceSystemVars($s_html);

    // Send headers
    $o_controller->sendHeaders();

} catch (DatabaseConnectionError $e) {
    // Todo: Check if in Json...
    // Error with Databases
    $s_html = getErrorView(Views::ERROR_EXCEPTION_ERROR, Views::$st_ERROR_MESSAGES[Views::ERROR_EXCEPTION_ERROR].' '.$e->getMessage());
} catch (DatabaseUnableToSelectDb $e) {
    $s_html = getErrorView(Views::ERROR_EXCEPTION_ERROR, Views::$st_ERROR_MESSAGES[Views::ERROR_EXCEPTION_ERROR].' '.$e->getMessage());
} catch (DatabaseUnableToSetCharset $e) {
    $s_html = getErrorView(Views::ERROR_EXCEPTION_ERROR, Views::$st_ERROR_MESSAGES[Views::ERROR_EXCEPTION_ERROR].' '.$e->getMessage());
} catch (CustomClassNotFound $e) {
    $s_html = getErrorView(Views::ERROR_EXCEPTION_ERROR, Views::$st_ERROR_MESSAGES[Views::ERROR_EXCEPTION_ERROR].' '.$e->getMessage());
} catch (CustomFileNotFound $e) {
    $s_html = getErrorView(Views::ERROR_EXCEPTION_ERROR, Views::$st_ERROR_MESSAGES[Views::ERROR_EXCEPTION_ERROR].' '.$e->getMessage());
} catch (CustomFileNotDefined $e) {
    $s_html = getErrorView(Views::ERROR_EXCEPTION_ERROR, Views::$st_ERROR_MESSAGES[Views::ERROR_EXCEPTION_ERROR].' '.$e->getMessage());
} catch (CurrencyNotFoundException $e) {
    $s_html = getErrorView(Views::ERROR_EXCEPTION_ERROR, Views::$st_ERROR_MESSAGES[Views::ERROR_EXCEPTION_ERROR].' '.$e->getMessage());
} catch (exception $e) {
    $s_html = getErrorView(Views::ERROR_EXCEPTION_ERROR, Views::$st_ERROR_MESSAGES[Views::ERROR_EXCEPTION_ERROR].' '.$e->getMessage());
}

// Echo page or error
echo $s_html;

Core::end();

