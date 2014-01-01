<?php

/**
 * Creator:      Carles Mateo
 * Date Created: 2013-02-08 07:45
 * Last Updater: Carles Mateo
 * Last Updated: 2013-12-25 10:44
 * Filename:     form.class.php
 * Description:  Autoloader
 */

function catfw_autoload($s_class) {

    $b_error = true;

    $s_filename = strtolower($s_class).'.class.php';
    $s_filename_classbasename = end(explode('\\', $s_class)).'.class.php';    // Remove the \CataloniaFramework\yourclass or other namespace

    // Try to load your class as is, even if it has the namespace in front
    if (file_exists(CLASSES_ROOT.$s_filename)) {
        require_once CLASSES_ROOT.$s_filename;
        $b_error = false;
    } elseif (file_exists(CLASSES_ROOT.$s_filename_classbasename)) {
        require_once CLASSES_ROOT.$s_filename_classbasename;
        $b_error = false;
    }

    if ($b_error == true) {
        throw new CustomClassNotFound('CustomClassNotFound:'.$s_class);
    }

}
