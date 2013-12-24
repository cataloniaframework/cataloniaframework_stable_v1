<?php

/**
 * Creator:      Carles Mateo
 * Date Created: 2013-02-18 00:53
 * Last Updater:
 * Last Updated:
 * Filename:     customexceptions.php
 * Description:
 */

namespace CataloniaFramework;

use \Exception as Exception;

class InternalError extends Exception {}
class DatabaseConnectionError extends Exception {}
class CacheCantWrite extends Exception {}
class CustomFileNotFound extends Exception {}
class CustomFileNotDefined extends Exception {}
class CustomClassNotFound extends Exception {}
class CurrencyNotFoundException extends Exception {}
class DatabaseUnableToSelectDb extends Exception {}
