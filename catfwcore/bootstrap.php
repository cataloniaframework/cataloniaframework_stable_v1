<?php
/**
 * Creator:      Carles Mateo
 * Date:         2013-02-07 12:53
 * Last Updater: Carles Mateo
 * Last Updated: 2013-09-25 11:36
 * Filename:     bootstrap.php
 * Description:
 */

require_once 'requests.class.php';  // For Multi-Development environments in development config
use CataloniaFramework\Requests as Requests;

require_once '../config/general.php';
// Db is required in general
use CataloniaFramework\Db as Db;

// Define Autoload
require_once 'autoload.php';

// Register our CatFw Autoload
spl_autoload_register('catfw_autoload');

require_once CATFW_CORE_ROOT.'strings.class.php';
use CataloniaFramework\Strings as Strings;
require_once CATFW_CORE_ROOT.'section.class.php';
use CataloniaFramework\Section as Section;
require_once CATFW_CORE_ROOT.'core.class.php';
use CataloniaFramework\Core as Core;
require_once CATFW_CORE_ROOT.'core.php';
require_once CATFW_CORE_ROOT.'controllerbase.class.php';
use CataloniaFramework\ControllerBase as ControllerBase;
require_once CATFW_CORE_ROOT.'modelbase.class.php';
require_once CATFW_CORE_ROOT.'views.class.php';
use CataloniaFramework\Views as Views;
require_once CATFW_CORE_ROOT.'datetime.class.php';
use CataloniaFramework\DateTime as DateTime;
require_once CATFW_CORE_ROOT.'customexceptions.class.php';
require_once CATFW_CORE_ROOT.'navigation.class.php';
use CataloniaFramework\Navigation as Navigation;
require_once CATFW_CORE_ROOT.'section.class.php';
require_once CATFW_CORE_ROOT.'menu.class.php';
use CataloniaFramework\Menu as Menu;
require_once CATFW_CORE_ROOT.'translations.class.php';
use CataloniaFramework\Translations as Translations;
require_once CATFW_CORE_ROOT.'numbers.class.php';
use CataloniaFramework\Numbers as Numbers;
require_once CATFW_CORE_ROOT.'currency.class.php';
use CataloniaFramework\Currency as Currency;
// Register common to all pages, f.e. user vars
require_once CUSTOM_INIT_ROOT.'commonrequests.class.php';
use CataloniaFramework\CommonRequests as CommonRequests;
require_once CATFW_CORE_ROOT.'form.class.php';
use CataloniaFramework\Form as Form;
require_once CATFW_CORE_ROOT.'session.class.php';
use CataloniaFramework\Session as Session;

$s_user_language = LANGUAGE_DEFAULT;

$o_db = new Db($st_server_config['database']);

$b_is_custom_section = false;

// Define user custom hardcoded URLs
CommonRequests::registerURLS();

$s_params = Requests::getParamStringGET('params');
$st_params = Strings::getParamsFromURL($s_params);

define('REQUESTED_PATH', $s_params);

if (Navigation::isURLCustom(REQUESTED_PATH) == true) {
    // We use a special method to load Custom Content
    $s_controller = 'Index';
    $s_action = 'CATFW_getCustomContent';

    if (!Core::loadController($s_controller)) {
        echo getErrorView(Views::ERROR_CONTROLLER_OR_ACTION_NOT_FOUND, Views::$s_ERROR_MESSAGES[Views::ERROR_CONTROLLER_OR_ACTION_NOT_FOUND]);
        Core::end();
    }

    Core::loadController($s_controller);

    $s_controller_with_namespace = '\\CataloniaFramework\\'.$s_controller;
    $o_controller = new $s_controller_with_namespace;

} else {
    // Is not custom url, so we will get the class and method to execute

    // This is a special index for the case of the basic domain call without any controller specified
    $s_controller = 'Index';
    // Default Action name
    $s_action = 'Index';

    if (MULTILANG == true) {
        if (isset($st_params[0])) {
            if (Core::isLanguageActive($st_params[0]) == false) {
                Core::redirectUserToUrl('/'.$s_user_language.'/');
                Core::end();
            }
            // The URL language is correct
            $s_user_language = $st_params[0];
            define('USER_LANGUAGE', $s_user_language);
            unset($st_params[0]);
        } else {
            // Got http://domain/ but the right thing would be http://domain/lang/ , so redirect
            Core::redirectUserToUrl('/'.$s_user_language.'/');
            Core::end();
        }

        // Register user sections
        CommonRequests::registerSections($o_db);

        if (isset($st_params[1]) && !empty($st_params[1])) {
            $st_possible_section = Section::getSectionInfoByPath('/'.$s_user_language.'/'.$st_params[1]);
            //var_export($st_possible_section); echo $st_params[1];
            if ($st_possible_section !== null) {
                // Section Found
                $b_is_custom_section = true;
                $s_controller   = $st_possible_section['controller'];
                $s_action       = $st_possible_section['action'];
            } else {
                // Common structure /lang/controller/action
                if (isset($st_params[1]) && !empty($st_params[1])) {
                    $s_controller = Strings::getSanitizedControllerName($st_params[1]);
                    unset($st_params[1]);
                }
                if (isset($st_params[2]) && !empty($st_params[2])) {
                    $s_action = Strings::getSanitizedControllerName($st_params[2]);
                    unset($st_params[2]);
                }
            }

        }

        Section::registerSection('index', '/'.$s_user_language.'/');
    } else {

        define('USER_LANGUAGE', LANGUAGE_DEFAULT);
        if (isset($st_params[0]) && !empty($st_params[0])) {
            $st_possible_section = Section::getSectionInfoByPath($st_params[0]);
            if ($st_possible_section !== null) {
                // Section Found
                $b_is_custom_section = true;
                $s_controller   = $st_possible_section['controller'];
                $s_action       = $st_possible_section['action'];
            } else {
                // Common structure /controller/action
                if (isset($st_params[0]) && !empty($st_params[0])) {
                    $s_controller = Strings::getSanitizedControllerName($st_params[0]);
                    unset($st_params[0]);
                }
                if (isset($st_params[1]) && !empty($st_params[1])) {
                    $s_action = Strings::getSanitizedControllerName($st_params[1]);
                    unset($st_params[1]);
                }
            }
        }

        Section::registerSection('index', '/', 'Index', 'Index');
    }

    // Check security for controller and name
    if (!Core::isValidName($s_controller) || !Core::isValidName($s_action) ||
        !Core::loadController($s_controller)) {
        // Invalid name of controller or action
        echo getErrorView(Views::ERROR_CONTROLLER_OR_ACTION_NOT_FOUND, Views::$s_ERROR_MESSAGES[Views::ERROR_CONTROLLER_OR_ACTION_NOT_FOUND]);
        Core::end();
    }

    // Load the Controller Class into memory
    $s_controller_with_namespace = '\\CataloniaFramework\\'.$s_controller;
    $o_controller = new $s_controller_with_namespace;

    // For example: actionIndex
    $s_action = 'action'.$s_action;

    if (!method_exists($o_controller, $s_action)) {
        // Invalid name of controller or action
        echo getErrorView(Views::ERROR_CONTROLLER_OR_ACTION_NOT_FOUND, Views::$s_ERROR_MESSAGES[Views::ERROR_CONTROLLER_OR_ACTION_NOT_FOUND]);
        Core::end();
    }

}

define('CONTROLLER', $s_controller);
define('ACTION', $s_action);

// Create parameter list
$st_params = array_values($st_params);
$st_params_url = Array();
for ($i_count=0; $i_count<=count($st_params); $i_count = $i_count+2) {
    if (isset($st_params[$i_count+1])) {
        $st_params_url[$st_params[$i_count]] = $st_params[$i_count +1];
    }
}

// Load Additional custom content
CommonRequests::registerUserVars($o_db);
