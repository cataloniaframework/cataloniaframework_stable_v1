<?php
/**
 * Creator:      Carles Mateo
 * Date Created: 2013-02-07 21:11
 * Last Updater: Carles Mateo
 * Last Updated: 2013-12-28 21:34
 * Filename:     production_db.php
 * Description:  Defines connectivity and connection properties for databases in PreProduction environment
 */

use CataloniaFramework\Db as Db;

$st_server_config['database'] = Array(	'read'  => Array(   'servers'   => Array(0 => Array('connection_type'                   => Db::TYPE_CONNECTION_MYSQLI,
                                                                                            'connection_method'                 => Db::CONNECTION_METHOD_TCPIP,
                                                                                            'server_hostname'                   => '127.0.0.1',
                                                                                            'server_port'                       => '3306',
                                                                                            'username'			                => 'www_cataloniafw',
                                                                                            'password'			                => 'yourpassword',
                                                                                            'database'			                => 'cataloniafw',
                                                                                            'client_encoding'                   => 'utf8',
                                                                                            // Use for Cassandra Universal Driver
                                                                                            'cassandra_cud_hostname'            => '127.0.0.1',
                                                                                            'cassandra_cud_port'                => '80',
                                                                                            'cassandra_cud_cluster_hostname'    => '127.0.0.1',
                                                                                            'cassandra_cud_cluster_port'        => '9906',
                                                                                            'cassandra_cud_cluster_username'    => '',
                                                                                            'cassandra_cud_cluster_password'    => ''

                                                                                            )
                                                                                )
                                                        ),
                                        'write' => Array(   'servers'   => Array(0 => Array('connection_type'                   => Db::TYPE_CONNECTION_MYSQLI,
                                                                                            'connection_method'                 => Db::CONNECTION_METHOD_TCPIP,
                                                                                            'server_hostname'                   => '127.0.0.1',
                                                                                            'server_port'                       => '3306',
                                                                                            'username'			                => 'www_cataloniafw',
                                                                                            'password'			                => 'yourpassword',
                                                                                            'database'			                => 'cataloniafw',
                                                                                            'client_encoding'                   => 'utf8',
                                                                                            // Use for Cassandra Universal Driver
                                                                                            'cassandra_cud_hostname'            => '127.0.0.1',
                                                                                            'cassandra_cud_port'                => '80',
                                                                                            'cassandra_cud_cluster_hostname'    => '127.0.0.1',
                                                                                            'cassandra_cud_cluster_port'        => '9906',
                                                                                            'cassandra_cud_cluster_username'    => '',
                                                                                            'cassandra_cud_cluster_password'    => ''
                                                                                            )
                                                                                )

                                                        )


                                    );
