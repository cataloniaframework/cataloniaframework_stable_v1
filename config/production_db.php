<?php
/**
 * User:        Carles Mateo
 * Date:        2013-02-07
 * Time:        21:11
 * Filename:    development.php
 * Description:
 */

use CataloniaFramework\Db as Db;

$st_server_config['database'] = Array(	'read'  => Array(   'servers'   => Array(0 => Array('connection_type'   => Db::TYPE_CONNECTION_MYSQLI,
                                                                                            'connection_method' => Db::CONNECTION_METHOD_TCPIP,
                                                                                            'server_hostname'   => '127.0.0.1',
                                                                                            'port' 				=> '3306',
                                                                                            'username'			=> 'www_cataloniafw',
                                                                                            'password'			=> 'WCaT4!<$',
                                                                                            'database'			=> 'cataloniafw',
                                                                                            'client_encoding'   => 'utf8'
                                                                                            )
                                                                                )
                                                        ),
                                        'write' => Array(   'servers'   => Array(0 => Array('connection_type'   => Db::TYPE_CONNECTION_MYSQLI,
                                                                                            'connection_method' => Db::CONNECTION_METHOD_TCPIP,
                                                                                            'server_hostname'   => '127.0.0.1',
                                                                                            'port' 				=> '3306',
                                                                                            'username'			=> 'www_cataloniafw',
                                                                                            'password'			=> 'WCaT4!<$',
                                                                                            'database'			=> 'cataloniafw',
                                                                                            'client_encoding'   => 'utf8'
                                                                                            )
                                                                                )

                                                        )


                                    );
