<?php
/**
 * User:        Carles Mateo
 * Date:        2013-02-08
 * Time:        21:02
 * Filename:    autoload.php
 * Description:
 */

function catfw_autoload($s_class) {

    $b_error = true;

    $s_filename = strtolower($s_class).'.class.php';

    if (file_exists(CLASSES_ROOT.$s_filename)) {
        require_once CLASSES_ROOT.$s_filename;
        $b_error = false;
    }

    if ($b_error == true) {
        throw new CustomClassNotFound('CustomClassNotFound:'.$s_class);
    }

}
