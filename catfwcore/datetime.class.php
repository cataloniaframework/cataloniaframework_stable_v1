<?php

    /**
     * Creator:      Carles Mateo
     * Date Created: 2013-02-13 12:16
     * Last Updater: Carles Mateo
     * Last Updated: 2014-02-01 20:42
     * Filename:     datetime.class.php
     * Description:
     */

namespace CataloniaFramework;

abstract class Datetime
{

    const FORMAT_MYSQL_COMP         = 'N';
    const FORMAT_INT_TIME_SEP       = 'C';
    const FORMAT_INT_DASH           = 'O';
    const FORMAT_DATE_ONLY          = 'D';
    const FORMAT_TIME_ONLY          = 'T';
    const FORMAT_TIME_ONLY_NO_SEP   = 'I';
    const FORMAT_MICROTIME          = 'M';
    const FORMAT_UNIXTIME           = 'X';
    const FORMAT_NO_SEPARATORS      = 'Z';

    public static function getDateTime($s_format=self::FORMAT_MYSQL_COMP, $i_time = null)
    {
        // This function expects string but accepts array(0 => $s_format)
        // since it is used by Vars via call_user_func

        if ( $i_time === null) {
            $i_time = time();
        }

        if (is_array($s_format) && isset($s_format[0])) {
            $s_format = $s_format[0];
        }

        if ($s_format==self::FORMAT_MYSQL_COMP) {
            $s_date=date('Y-m-d H:i:s', $i_time);
        } elseif ($s_format==self::FORMAT_INT_TIME_SEP) {
            $s_date=date('Y-m-d-His', $i_time);
        } elseif ($s_format==self::FORMAT_INT_DASH)	{
            $s_date=date('Y-m-d-H-i-s', $i_time);
        } elseif ($s_format==self::FORMAT_DATE_ONLY) {
            // Only date, no time
            $s_date=date('Y-m-d', $i_time);
        } elseif ($s_format==self::FORMAT_TIME_ONLY) {
            $s_date=date('H:i:s', $i_time);
        } elseif ($s_format==self::FORMAT_TIME_ONLY_NO_SEP) {
            $s_date=date('His', $i_time);
        } elseif ($s_format==self::FORMAT_MICROTIME) {
            // Unix time with microseconds
            // This function is only available on operating systems that support the gettimeofday() system call.
            $s_date=microtime(true);
        } elseif ($s_format==self::FORMAT_UNIXTIME) {
            $s_date = $i_time;
        } elseif ($s_format==self::FORMAT_NO_SEPARATORS) {
            $s_date=date('YmdHis', $i_time);
        } else {
            $s_date=date('Y-m-d H:i:s', $i_time);
        }

        return $s_date;

    }

    // TODO: Finish this when Db is implemented
	public static function getDatabaseDateTime($o_db = null)
	{
		// Return information about the date of the database
		// Day of the week 0 - Monday http://dev.mysql.com/doc/refman/5.0/es/date-and-time-functions.html
		$s_sql='SELECT NOW() AS DB_DATETIME, TIME(NOW()) AS DB_TIME, DATE(NOW()) AS DB_DATE, WEEK(NOW(),1) AS DB_WEEK,
						 WEEKDAY(NOW()) AS DB_WEEKDAY';

		/* if (!$o_db) {
			$o_db = new DbUtils();
		} */

/*		$st_result=$o_db->do_select($s_sql);

		$l_st_database_time=Array(
                                'database_datetime' => $st_result['data'][1]['DB_DATETIME'],
                                'database_time'     => $st_result['data'][1]['DB_TIME'],
                                'database_date'     => $st_result['data'][1]['DB_DATE'],
                                'database_week'     => $st_result['data'][1]['DB_WEEK'],
                                'database_weekday'  => $st_result['data'][1]['DB_WEEKDAY'],
                                'webserver_datetime' => self::getDateTime('N')
                            );

		return $l_st_database_time;*/
	}

}
