<?php

/**
 * Creator:      Carles Mateo
 * Date Created: 2013-03-06 08:06
 * Last Updater:
 * Last Updated:
 * Filename:     cache.class.php
 * Description:
 */

namespace CataloniaFramework;

abstract class CacheModel extends ModelBase
{

    public static function deleteCache() {

        $s_mask = 'cache-*';
        $st_files_to_delete = glob(CACHE_ROOT.$s_mask);

        $i_files_to_delete = count($st_files_to_delete);
        $i_files_deleted = 0;
        $i_files_not_deleted = 0;

        foreach($st_files_to_delete as $i_key => $s_file_to_delete) {
            if (unlink($s_file_to_delete) == false) {
                // TODO: Log
                $i_files_not_deleted++;
            } else {
                $i_files_deleted++;
            }
        }

        return true;
    }

}
