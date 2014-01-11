<?php
/**
 * Creator:      Carles Mateo
 * Date Created: 2013-02-07 21:11
 * Last Updater: Carles Mateo
 * Last Updated: 2013-12-28 21:34
 * Filename:     development.php
 * Description:  Defines paths and setups for the development environment
 */

use CataloniaFramework\Db as Db;

$st_server_config = array(  'environment' 	=> ENVIRONMENT,
							'web' 			=> array(   'http'  => 'http://devel.cataloniafw.com/',
														'http_enabled'	=> true,
														'https' => 'https://devel.cataloniafw.com/',
														'https_enabled' => false),
							'cdn' 			=> array(   'images' => array(	'http'  => 'http://images.cataloniafw.com/',
																			'https' => 'https://images.cataloniafw.com/'),
														'videos' => array(	'http'  => 'http://video.cataloniafw.com/',
																			'https' => 'https://video.cataloniafw.com/')
													),
							'storage'		=> array(	'web_root'          => '/home/carles/Escriptori/codi/catalonia/www/',
                                                        'catfw_root'        => '/home/carles/Escriptori/codi/catalonia/',
                                                        'classes_root'      => '/home/carles/Escriptori/codi/catalonia/classes/',
                                                        'cache'             => '/home/carles/Escriptori/codi/catalonia/cache/',
												 		'tmp'               => '/tmp/',
												 		'logs'              => '/var/logs/www/'
												 	),
                            'node'          => array(   'name'              => 'WEB01',
                                                        'setcookie'         => true,
                                                        'ttl_cookie'        => 0,
                                                        'cookie_name'       => 'BALANCER_ID',
                                                        'cookie_value'      => 'WEB01')
						  );

// Languages Supported http://www.loc.gov/standards/iso639-2/php/code_list.php
$p_st_languages = array ( 'ca' => array('default'   => true,
                                        'active'    => true,
                                        'http_img_flag' =>  'img/flag_ca.png',
                                        'browser_detection' => array(   0 => 'ca',
                                                                        1 => 'ca_es',
                                                                        2 => 'ca_ca',
                                                                        3 => 'ca_en')
                                        ),
                          'en' => array('default'   => true,
                                        'active'    => true,
                                        'http_img_flag' =>  'img/flag_en.png',
                                        'browser_detection' => array(   0 => 'en',
                                                                        1 => 'en_en',
                                                                        2 => 'en_us',
                                                                        3 => 'en_uk')
                                       ),
                        );

define('LANGUAGE_DEFAULT', 'en');


// If we log SQL Inserts to FILE. Normally for Debug.
define('LOG_SQL_TO_FILE',false);
define('LOG_HTTP_REQUESTS_TO_FILE', false);

// Reports everything. This is like E_ALL | E_STRICT. Note: From PHP 5.4 E_STRICT is part of E_ALL
// But with -1 PHP's future versions new errors are granted.
error_reporting(-1);
