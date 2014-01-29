<?php

 /**
 * Creator:      Carles Mateo
 * Date Created: 2014-01-09 00:08
 * Last Updater: Carles Mateo
 * Last Updated: 2014-01-28 18:19
 * Filename:     file.class.php
 * Description:
 */

namespace CataloniaFramework;

abstract class File {

    const LOG_MODE_EXTENDED = 'extended';
    const LOG_MODE_STANDARD = 'standard';

    const LOG_FILE_MODE_APPEND    = 'a';
    const LOG_FILE_MODE_READ      = 'r';
    const LOG_FILE_MODE_READWRITE = 'r+';
    const LOG_FILE_MODE_WRITE     = 'w';

	public static function logToFile($m_message, $s_file, $s_file_mode = self::LOG_FILE_MODE_APPEND, $s_type_of_report = self::LOG_MODE_STANDARD, $m_additional_info='')
	{

        $s_datetime = Datetime::getDateTime(Datetime::FORMAT_MYSQL_COMP);

        if (!is_string($m_message)) {
            $s_message = serialize($m_message);
        } else {
            $s_message = $m_message;
        }

        if (!is_string($m_additional_info)) {
            $s_additional_info = serialize($m_additional_info);
        } else {
            $s_additional_info = $m_additional_info;
        }

		try {
			$o_fh = fopen($s_file, $s_file_mode) or die("can't open file");
			if ($s_type_of_report == self::LOG_MODE_EXTENDED)
			{
				fwrite($o_fh, "/* ============================================ */\n");
				fwrite($o_fh, $s_datetime." $s_additional_info\n");
				fwrite($o_fh, "/* -------------------------------------------- */\n");
				// NOTE: We defined an alias to DateTimeUtils::what_time_is_it('N') in debug.inc.php as wt()
			}
			fwrite($o_fh, "$s_datetime $s_message\n");
			fclose($o_fh);
		}
		catch(\Exception $e){
			die('Error:'.$e->getMessage());
		}

	}

	public static function writeToFile($s_message, $s_file, $s_file_mode = self::LOG_FILE_MODE_APPEND, $b_add_enter = true)
	{

        $b_result = true;

        if ($b_add_enter == true) {
            $s_message .= "\n";
        }


		try {
			$o_fh = fopen($s_file, $s_file_mode) or die("can't open file");
			fwrite($o_fh, $s_message);
			fclose($o_fh);
		}
		catch(\Exception $e) {
			//die('Error:'.$e->getMessage());
            $b_result = false;
		}

        return $b_result;
	}

    public static function deleteFile($s_file) {

        $b_result = false;

        try {
            $b_result = unlink($s_file);
        } catch (\Exception $e) {
            //die('Error:'.$e->getMessage());
        }

        return $b_result;
    }

}