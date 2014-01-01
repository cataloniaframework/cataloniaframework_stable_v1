<?php

/**
 * Creator:      Carles Mateo
 * Date Created: 2013-02-17 23:46
 * Last Updater: Carles Mateo
 * Last Updated: 2013-12-28 21:34
 * Filename:     Db.class.php
 * Description:  Handles interactions with the Databases
 */

namespace CataloniaFramework;

class Db
{

    const TYPE_CONNECTION_MYSQL     = 'mysql';
    const TYPE_CONNECTION_MYSQLI    = 'mysqli';
    const TYPE_CONNECTION_POSTGRE   = 'pg';
    const TYPE_CONNECTION_CASSANDRA = 'cassandra';  // NoSQL Cassandra

    const SERVER_LOCALHOST = 'localhost';
    const CONNECTION_METHOD_UNIX_SOCKETS    = 'unix_sockets';
    const CONNECTION_METHOD_TCPIP           = 'tcpip';

    const CONNECTION_READ   = 'read';
    const CONNECTION_WRITE  = 'write';

    const QUERY_TYPE_WRITE  = 'write';
    const QUERY_TYPE_READ   = 'read';

    const DATA_TYPE_STRING      = 'string';
    const DATA_TYPE_INT         = 'int';
    const DATA_TYPE_FLOAT       = 'float';
    const DATA_TYPE_DATE        = 'date';
    const DATA_TYPE_DATETIME    = 'datetime';

    private $o_connection_read  = null;
    private $o_connection_write = null;

    // Servers and config used for the connection
    private $st_server_read  = null;
    private $st_server_write = null;

    private $b_same_server = false;

    // Connection
    private $s_server_hostname_read  = null;
    private $s_server_hostname_write = null;

    private $s_server_port_read  = null;
    private $s_server_port_write = null;

    private $s_server_username_read  = null;
    private $s_server_username_write = null;

    private $s_server_password_read  = null;
    private $s_server_password_write = null;

    private $s_server_database_read = null;
    private $s_server_database_write = null;

    private $s_type_connection_read  = null;
    private $s_type_connection_write = null;

    private $s_connection_method_read  = null;
    private $s_connection_method_write = null;

    private $s_connection_client_encoding_read  = null;
    private $s_connection_client_encoding_write = null;

    private $s_error_read   = null;
    private $s_error_write  = null;

    public function __construct($st_database) {

        // Get Server for read
        // TODO: Create an algorithm to support more servers, so load balancer can be saved
        $this->st_server_read  = $st_database['read']['servers'][0];
        $this->st_server_write = $st_database['write']['servers'][0];

        $this->s_server_hostname_read  = $st_database['read']['servers'][0]['server_hostname'];
        $this->s_server_hostname_write = $st_database['write']['servers'][0]['server_hostname'];

        $this->s_server_port_read  = $st_database['read']['servers'][0]['server_port'];
        $this->s_server_port_write = $st_database['write']['servers'][0]['server_port'];

        $this->s_server_username_read  = $st_database['read']['servers'][0]['username'];
        $this->s_server_username_write = $st_database['write']['servers'][0]['username'];

        $this->s_server_password_read  = $st_database['read']['servers'][0]['password'];
        $this->s_server_password_write = $st_database['write']['servers'][0]['password'];

        $this->s_server_database_read  = $st_database['read']['servers'][0]['database'];
        $this->s_server_database_write = $st_database['write']['servers'][0]['database'];

        $this->s_type_connection_read    = $st_database['read']['servers'][0]['connection_type'];
        $this->s_type_connection_write   = $st_database['write']['servers'][0]['connection_type'];

        $this->s_connection_method_read  = $st_database['read']['servers'][0]['connection_method'];
        $this->s_connection_method_write = $st_database['write']['servers'][0]['connection_method'];

        if (isset($st_database['read']['servers'][0]['client_encoding'])) {
            $this->s_connection_client_encoding_read = $st_database['read']['servers'][0]['client_encoding'];
        }
        if (isset($st_database['write']['servers'][0]['client_encoding'])) {
            $this->s_connection_client_encoding_write = $st_database['write']['servers'][0]['client_encoding'];
        }

        if ($this->s_connection_method_read == $this->s_connection_method_write &&
            $this->s_server_hostname_read == $this->s_server_hostname_write &&
            $this->s_server_port_read == $this->s_server_port_write) {
            // Write is the same than read
            // We use references so we avoid making another connection (so we avoid the delays)
            $this->o_connection_write = & $this->o_connection_read;
            $this->b_same_server = true;

        }


    }

    // destructor
    public function __destruct() {
        try {
            if ($this->o_connection_read !== null) {
                if ($this->s_type_connection_read == self::TYPE_CONNECTION_MYSQL) {
                    @mysql_close($this->o_connection_read);
                }
                if ($this->s_type_connection_read == self::TYPE_CONNECTION_MYSQLI) {
                    @mysqli_close($this->o_connection_read);
                }
                if ($this->s_type_connection_read == self::TYPE_CONNECTION_POSTGRE) {
                    @pg_close($this->o_connection_read);
                }

            }

            if ($this->b_same_server == false) {
                if ($this->o_connection_write !== null) {
                    if ($this->s_type_connection_write == self::TYPE_CONNECTION_MYSQL) {
                        @mysql_close($this->o_connection_write);
                    }
                    if ($this->s_type_connection_write == self::TYPE_CONNECTION_MYSQLI) {
                        @mysqli_close($this->o_connection_write);
                    }
                    if ($this->s_type_connection_write == self::TYPE_CONNECTION_POSTGRE) {
                        @pg_close($this->o_connection_write);
                    }
                }
            }

        } catch (Exception $e) {
            //$this->log_error('Error:'.$e->getMessage(), 'ERROR');
        }
    }


    private function connect($s_init_connection = self::CONNECTION_READ) {

        $s_server_hostname              = '';
        $s_type_connection              = '';
        $s_server_port                  = '';
        $s_server_username              = '';
        $s_server_password              = '';
        $s_connection_client_encoding   = '';

        if ($s_init_connection == self::CONNECTION_READ) {
            // If the connection is init do not init again, just exit
            if ($this->o_connection_read != null) {
                return;
            }

            $s_error = &$this->s_error_read;

            $s_type_connection  = $this->s_type_connection_read;
            $s_server_hostname  = $this->s_server_hostname_read;
            $s_server_port      = $this->s_server_port_read;
            $s_server_username  = $this->s_server_username_read;
            $s_server_password  = $this->s_server_password_read;
            $s_server_database  = $this->s_server_database_read;
            $s_connection_client_encoding = $this->s_connection_client_encoding_read;
            $o_connection       = &$this->o_connection_read;

        }

        if ($s_init_connection == self::CONNECTION_WRITE) {
            // If the connection is init do not init again
            if ($this->b_same_server == true) {
                if ($this->o_connection_read != null) {
                    return;
                } else {
                    $s_error = &$this->s_error_read;
                    $o_connection = &$this->o_connection_read;
                }
            } else {
                if ($this->o_connection_write != null) {
                    return;
                } else {
                    $s_error = &$this->s_error_write;
                    $o_connection       = &$this->o_connection_write;
                }
            }

            $s_type_connection  = $this->s_type_connection_write;
            $s_server_hostname  = $this->s_server_hostname_write;
            $s_server_port      = $this->s_server_port_write;
            $s_server_username  = $this->s_server_username_write;
            $s_server_password  = $this->s_server_password_write;
            $s_server_database  = $this->s_server_database_write;
            $s_connection_client_encoding = $this->s_connection_client_encoding_write;
        }

        if ($s_type_connection==self::TYPE_CONNECTION_MYSQL) {

            $o_connection = mysql_connect($s_server_hostname.':'.$s_server_port, $s_server_username, $s_server_password);
            if (!mysql_set_charset($s_connection_client_encoding, $o_connection)) {
                $s_error = 'Unable to set charset';
                throw new DatabaseUnableToSetCharset('Unable to set charset: '.$s_connection_client_encoding);
            }
            if (!@mysql_select_db($s_server_database, $o_connection)) {
                $s_error = 'Unable to select database';
                //die( "Unable to select database");
                throw new DatabaseUnableToSelectDb('Unable to select database: '.$s_server_database);
            }
        }

        if ($s_type_connection==self::TYPE_CONNECTION_MYSQLI) {

            $o_connection = mysqli_connect($s_server_hostname, $s_server_username, $s_server_password, $s_server_database, $s_server_port);
            if (!mysqli_set_charset($o_connection, $s_connection_client_encoding)) {
                $s_error = 'Unable to set charset';
                throw new DatabaseUnableToSetCharset('Unable to set charset: '.$s_connection_client_encoding);
            }
            if (!@mysqli_select_db($o_connection, $s_server_database)) {
                $s_error = 'Unable to select database';
                //die( "Unable to select database");
                throw new DatabaseUnableToSelectDb('Unable to select database: '.$s_server_database);
            }
        }

        if ($s_type_connection == self::TYPE_CONNECTION_POSTGRE) {
            $s_conn_string = "host=$s_server_hostname port=$s_server_port dbname=$s_server_database user=$s_server_username password=$s_server_password";
            if (isset($s_connection_client_encoding) && $s_connection_client_encoding != '') {
                $s_conn_string .= " options='--client_encoding=UTF8'";
            }

            $o_connection = pg_connect($s_conn_string, PGSQL_CONNECT_FORCE_NEW);
        }

    }

    public function queryRead($s_sql) {
        $this->connect(self::CONNECTION_READ);
        return $this->queryConnection($s_sql, $this->s_type_connection_read, $this->o_connection_read);
    }

    public function queryWrite($s_sql) {
        $this->connect(self::CONNECTION_WRITE);
        return $this->queryConnection($s_sql, $this->s_type_connection_write, $this->o_connection_write, self::QUERY_TYPE_WRITE);
    }

    public function queryConnection($s_sql, &$s_type_connection, &$o_connection, $s_query_type = self::QUERY_TYPE_READ)
    {

        $i_num = 0;

        $st_result = Array('result' => Array(   'status'                    => 0,
            'error'                     => 0,
            'error_description'         => 'Not executed yet',
            'numrows'                   => 0,
            'insert_id'                 => null,
            'query'                     => $s_sql,
            'query_type'                => null,
            'profiler_request_start'    => Datetime::getDateTime(Datetime::FORMAT_MICROTIME),
            'profiler_request_end'      => 0),
            'data'=>Array()
        );

        // Extra security
        try {
            if (!$o_connection) {
                //$this->log_error('It was unable to connect to the database.', 'WEB');
                $st_result['result']['numrows'] = 0;
                $st_result['result']['status'] = 1;
                $st_result['result']['error'] = 1;
                $st_result['result']['error_description'] = 'Unable to connect to the database';
            }
            else
            {
                if ($s_type_connection == self::TYPE_CONNECTION_MYSQL) {
                    $o_result = mysql_query($s_sql, $o_connection);

                    if ($o_result === false) {
                        // Query Failed
                        $i_num = 0;
                        $st_result['result']['error'] = $st_result['result']['error'] + 1;
                        $st_result['result']['error_description'] = mysql_error($o_connection);

                    } elseif ($o_result === true) {
                        // Query successful of type different to SELECT, SHOW, DESCRIBE, or EXPLAIN
                        $i_num = 0;
                        if ($s_query_type == self::QUERY_TYPE_WRITE) {
                            $m_insert_id = mysql_insert_id($o_connection);
                            $st_result['result']['insert_id'] = $m_insert_id;
                        }

                        // TODO: Affected rows
                    } else {
                        // Query was SELECT, SHOW, DESCRIBE or EXPLAIN and successful
                        $i_num = mysql_num_rows($o_result);
                    }
                }

                if ($s_type_connection == self::TYPE_CONNECTION_MYSQLI) {
                    $o_result = mysqli_query($o_connection, $s_sql);

                    if ($o_result === false) {
                        // Query Failed
                        $i_num = 0;
                        $st_result['result']['error'] = $st_result['result']['error'] + 1;
                        $st_result['result']['error_description'] = mysqli_error($o_connection);

                    } elseif ($o_result === true) {
                        // Query successful of type different to SELECT, SHOW, DESCRIBE, or EXPLAIN
                        $i_num = 0;
                        if ($s_query_type == self::QUERY_TYPE_WRITE) {
                            $m_insert_id = mysqli_insert_id($o_connection);
                            $st_result['result']['insert_id'] = $m_insert_id;
                        }

                        // TODO: Affected rows
                    } else {
                        // Query was SELECT, SHOW, DESCRIBE or EXPLAIN and successful
                        $i_num = mysqli_num_rows($o_result);
                    }

                }

                if ($s_type_connection == self::TYPE_CONNECTION_POSTGRE) {
                    $o_result = pg_query($o_connection, $s_sql);

                    if ($o_result == false) {
                        $st_result['result']['error'] = $st_result['result']['error'] +1;
                        $st_result['result']['error_description'] = pg_last_error($o_connection);
                    }

                    $i_num = pg_num_rows($o_result);
                }

                $i_pointer=0;

                if ($i_num>0) {
                    while ($i_pointer<$i_num)
                    {
                        // Note: Uncomment in case you want to fill the array starting from 1 not from 0. I prefer this way for teaching
                        //       juniors since count gives the position of the last item, and in for bucles
                        //       there is no need to substract 1 to get the last one.
                        //$i_pointer++;
                        if ($s_type_connection == self::TYPE_CONNECTION_MYSQL) {
                            $st_result['data'][$i_pointer] = mysql_fetch_array($o_result, MYSQL_ASSOC);
                        }
                        if ($s_type_connection == self::TYPE_CONNECTION_POSTGRE) {
                            $st_result['data'][$i_pointer] = pg_fetch_array($o_result, PGSQL_ASSOC);
                        }
                        if ($s_type_connection == self::TYPE_CONNECTION_MYSQLI) {
                            $st_result['data'][$i_pointer] = mysqli_fetch_array($o_result, MYSQLI_ASSOC);
                        }
                        $i_pointer++;
                    }
                    $st_result['result']['error_description'] = 'Results returned Ok';
                }
                else
                {
                    $st_result['result']['error_description'] = 'No results returned';
                }

                $st_result['result']['numrows'] = $i_num;
                $st_result['result']['status'] = 1;
                $st_result['result']['error'] = 0;
                $st_result['result']['profiler_request_end'] = Datetime::getDateTime(DateTime::FORMAT_MICROTIME);

            }

            if (LOG_SQL_TO_FILE == true){
                //FileUtils::log_to_file($s_sql, LOG_SQL_FILE, 'a', 'extended', 'Report from Db.class Profile: '.$st_result['result']['profiler_request_start'].'-'.$st_result['result']['profiler_request_end']);
            }

        }
        catch(Exception $e){
            //$this->log_error('Error:'.$e->getMessage(), 'ERROR');

            $st_result['result']['numrows'] = $i_num;
            $st_result['result']['status'] = 1;
            $st_result['result']['error'] = 1;
            $st_result['result']['error_description'] = $e->getMessage();

        }

        return $st_result;
    }

    /*
     * Creator:      M.
     * Date Created: 2013-12-18 21:52
     * Last Updater: Carles Mateo
     * Last Updated: 2013-12-21 14:30
     * Description:  Static function for preparing data (escape, transform, etc)
     *               query single data (String, Integer, Float, Date, etc)
     */
    public static function prepareInsert($m_data, $type = self::DATA_TYPE_STRING)
    {
        $s_prepared_data = '';

        switch($type){
            case self::DATA_TYPE_STRING:
                // NOTE: Remove ' Sql injection
                // You can json_encode or what your DB Needs
                $s_prepared_data = str_replace("'", "\'", $m_data);
                break;

            case self::DATA_TYPE_INT:
                // Ensure it is an integer. Convert Sql injections to numeric or 0
                $s_prepared_data = intval($m_data);
                break;

            case self::DATA_TYPE_FLOAT:
                $s_prepared_data = floatval($m_data);
                break;

            /* TODO: Future feature, ensure Dates are compliant, valid, and convert from/to unix time
            case self::DATA_TYPE_DATE:
                break;

            case self::DATA_TYPE_DATETIME:
                break;
            */
            default:
                $s_prepared_data = $m_data;
        }

        return $s_prepared_data;
    }
}
