<?php

/**
 * Creator:      Carles Mateo
 * Date Created: 2013-03-07 14:25
 * Last Updater:
 * Last Updated:
 * Filename:     currency.class.php
 * Description:
 */

namespace CataloniaFramework;

class Currency
{

	const CURRENCY_SYMBOL_POSITION_NO_SHOW               = 0;
	const CURRENCY_SYMBOL_POSITION_IN_FRONT_SPACED       = 10;
	const CURRENCY_SYMBOL_POSITION_IN_FRONT_NOT_SPACED   = 11;
	const CURRENCY_SYMBOL_POSITION_IN_BACK_SPACED        = 20;
	const CURRENCY_SYMBOL_POSITION_IN_BACK_NOT_SPACED    = 21;

	protected $s_id_currency = '';

    // ISO 4217 for currencies
	// http://www.xe.com/iso4217.php/
    // http://en.wikipedia.org/wiki/ISO_4217
	protected $h_currencies = array(   'EUR' => array( 'name' => 'Euro',
														'symbol' => '€',
														'decimal_units' => 2,
														'decimal_separator' => ',',
														'thousand_separator' => '.',
														'symbol_position' => self::CURRENCY_SYMBOL_POSITION_IN_BACK_SPACED,
														'change_to_eur' => 1,
                                                        'datetime_updated'  => '2013-03-07 14:25'
													),
										'USD' => array( 'name' => 'US Dollar',
														'symbol' => '$',
														'decimal_units' => 2,
														'decimal_separator' => '.',
														'thousand_separator' => ',',
														'symbol_position' => self::CURRENCY_SYMBOL_POSITION_IN_FRONT_NOT_SPACED,
														'change_to_eur' => 0.76863,  // 1 USD = x EUR
                                                        'datetime_updated'  => '2013-03-07 14:25'
													),
                                        'GBP' => array( 'name' => 'Pound sterling',
                                                        'symbol' => '£',
                                                        'decimal_units' => 2,
                                                        'decimal_separator' => '.',
                                                        'thousand_separator' => ',',
                                                        'symbol_position' => self::CURRENCY_SYMBOL_POSITION_IN_FRONT_NOT_SPACED,
                                                        'change_to_eur' => 1.15795,
                                                        'datetime_updated'  => '2013-03-07 14:25'
                                                    ),
                                        'JPY' => array( 'name' => 'Japan Yen',
                                                        'symbol' => '¥',
                                                        'decimal_units' => 2,
                                                        'decimal_separator' => '.',
                                                        'thousand_separator' => ',',
                                                        'symbol_position' => self::CURRENCY_SYMBOL_POSITION_IN_FRONT_NOT_SPACED,
                                                        'change_to_eur' => 0.00821,  // 1 JPY = x EUR
                                                        'datetime_updated'  => '2013-03-07 14:25'
                                                    ),
                                   );
/*                                                                      Other currencies
                                                                        I would love to add Catalan currency here some day :)
																		 'CAD' => 0,    // Canada Dollar
																	     'AUD' => 0,    // Australia Dollar
																		 'CLP' => 0,    // Chile Peso
																		 'MXN' => 0,    // Mexico Peso
																		 'BRL' => 0,    // Brazil Real */

	public function __construct($s_id_currency = 'EUR') {
		// Load currencies

		if (!in_array($s_id_currency,array_keys($this->h_currencies))) {
			// The money doesn't exist. I stop now
			throw new CurrencyNotFoundException('Invalid id currency');
		}
		$this->s_id_currency = $s_id_currency;

/*		$s_sql = "SELECT * FROM `currencies` WHERE active=1";
		$h_db_currencies = db::query_cms($s_sql, 'arrayhash');

		// We update the array with the prices from Db
		foreach($h_db_currencies as $i_autonum=>$h_db_currency_values) {
			if (in_array($h_db_currency_values['code_currency'],array_keys($this->h_currencies))) {
				$this->h_currencies[$h_db_currency_values['code_currency']]['change_to_eur'] = $h_db_currency_values['change_to_eur'];
			}
		}*/

	}

	public function getInEuros($f_amount) {

		$f_change_to_EUR = $this->h_currencies[$this->s_id_currency]['change_to_eur'];

		$f_amount_in_eur = $f_amount * $f_change_to_EUR;

		return $f_amount_in_eur;
	}

	public function getFromEurosToCurrency($f_amount) {
		$f_change_to_EUR = $this->h_currencies[$this->s_id_currency]['change_to_eur'];

		$f_amount_in_currency = $f_amount / $f_change_to_EUR;

		return $f_amount_in_currency;
	}

	// Returns the amount formatted
	public function getFormattedAmount($f_amount, $b_return_symbol = true) {
		try {
			$s_prefix=''; $s_postfix='';
			$h_currency_properties = $this->getCurrencyProperties($this->s_id_currency);

			$s_amount = number_format(  $f_amount, $h_currency_properties['decimal_units'], $h_currency_properties['decimal_separator'],
										$h_currency_properties['thousand_separator']);

			if ($h_currency_properties['symbol_position'] == self::CURRENCY_SYMBOL_POSITION_IN_FRONT_SPACED) {
				$s_prefix = $h_currency_properties['symbol'].' ';
			}
			if ($h_currency_properties['symbol_position'] == self::CURRENCY_SYMBOL_POSITION_IN_FRONT_NOT_SPACED) {
				$s_prefix = $h_currency_properties['symbol'];
			}
			if ($h_currency_properties['symbol_position'] == self::CURRENCY_SYMBOL_POSITION_IN_BACK_SPACED) {
				$s_postfix = ' '.$h_currency_properties['symbol'];
			}
			if ($h_currency_properties['symbol_position'] == self::CURRENCY_SYMBOL_POSITION_IN_BACK_NOT_SPACED) {
				$s_postfix = $h_currency_properties['symbol'];
			}

			if ($b_return_symbol == true) {
				$s_amount_formatted = $s_prefix.$s_amount.$s_postfix;
			}
			else
			{
				$s_amount_formatted = $s_amount;
			}



		} catch (NotFoundException $e) {
			return '';
		} catch (Exception $e) {
			return '';
		}

		return $s_amount_formatted;
	}

	public function getCurrencyProperties($s_id_currency) {
		if (isset($this->h_currencies[$s_id_currency])) {
			return $this->h_currencies[$s_id_currency];
		}

		throw new NotFoundException('Invalid id currency');
	}


}
