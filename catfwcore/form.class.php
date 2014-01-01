<?php

/**
 * Creator:      Carles Mateo
 * Date Created: 2013-03-01 00:55
 * Last Updater: 2013-12-20 10:10
 * Last Updated: Carles Mateo
 * Filename:     form.class.php
 * Version:      1.097
 * Description:
 */

namespace CataloniaFramework;

class Form
{
	// define properties
    const ERROR_EMPTY_REQUEST = 1;
    const ERROR_REQUIRED_FIELD_NOT_PROVIDED = 5;
    const ERROR_INVALID_LENGTH = 10;
    const ERROR_MAX_OCCURRENCES = 15;
    const ERROR_NOT_MATCHING_EXPRESSION = 20;   // Invalid characters or not matching the regular expression
    const ERROR_INVALID_VERB_RESPONSE = 21;
	const ERROR_VALUE_NOT_IN_ACCEPTED_VALUES = 30;
    const ERROR_INVALID_NUMBER = 40;            // Invalid number. Is not numeric
    const ERROR_NUMERIC_NOT_INTEGER = 41;       // Not integer numeric value
    const ERROR_NOT_VALID_EMAIL = 44;           // Invalid email
    const ERROR_DATES_MISMATCH = 130;
    const ERROR_DATE_NOT_VALID = 135;           // Date not valid (gregorian calendar)
    const ERROR_PASSWORDS_DONT_MATCH = 401;     // Passwords don't match
    const ERROR_INJECTION_ATTACK = 500;
    const ERROR_OTHER = 999;                    // Other error

	const API_ERROR_CRITICAL = 1;
	const API_ERROR_WARNING = 5;

	const MODE_EXPECTED_VALUES = 1;

	const IS_PASSWORD = true;
	const IS_NOT_PASSWORD = false;
	const IS_EMAIL = true;
	const IS_NOT_EMAIL = false;
	const IS_CHECK = true;
	const IS_NOT_CHECK = false;
	const IS_READONLY = true;
	const IS_NOT_READONLY = false;
	const IS_DISABLED = true;
	const IS_NOT_DISABLED = false;
	const IS_REQUIRED = true;
	const IS_NOT_REQUIRED = false;

	const HTML_TYPE_HIDDEN = 'HIDDEN';
	const HTML_TYPE_TEXT = 'TEXT';
	const HTML_TYPE_PASSWORD = 'PASSWORD';
	const HTML_TYPE_CHECK = 'CHECKBOX';
	const HTML_TYPE_TEXTAREA = 'TEXTAREA';
	const HTML_TYPE_SELECT = 'SELECT';
	const HTML_TYPE_SUBMIT = 'SUBMIT';

	const HTML_CHECKBOX_CHECKED = true;
	const HTML_CHECKBOX_NOT_CHECKED = false;
	const HTML_SELECT_MULTISELECT = true;
	const HTML_SELECT_NOT_MULTISELECT = false;

	const HTML_NO_CLASS = '';

	const DATATYPE_STRING = 'STRING';
	const DATATYPE_INTEGER = 'INTEGER';
	//const DATATYPE_CHECKBOX = 'CHECKBOX';	// We convert to 1, what we get as 'on'
	const DATATYPE_DATE = 'DATE';
	const DATATYPE_EMAIL = 'EMAIL';
	// Future
	const DATATYPE_IPV4 = 'IPV4';
	const DATATYPE_IPV6 = 'IPV6';
	const DATATYPE_VHOST = 'VHOST';

	const REGEXP_EMPTY = '';

	const HELP_FIELD_NOT_HELP = '';

	const INPUT_DECODE_HTML_ENTITIES = true;
	const INPUT_NOT_DECODE_HTML_ENTITIES = false;

	const ARRAY_MODE_SEARCH_STRICT = true;	// Fix PHP bug

	protected $s_user_id = '';
	protected $s_action_id = '';
	protected $s_form_id = '';
	// The data, after being parsed is stored here
	protected $st_request_parsed = array();

	// Fields for html render
	protected $s_form_html_id = '';             // Id auto for HTML DOM
	protected $s_form_html_action = '';
	protected $s_form_html_method = '';			// GET/POST

	protected $st_errors = array(	'num_errors' => 0,
									'num_errors_critical' => 0,
									'num_errors_warning' => 0,
							  	    'errors' => array());

    // This is our Mask for validating parameters for the API variables
    protected $st_parameters_mask = array();

	protected $b_request_parsed_done = false;

	function __construct()
	{

	}

    function __destruct()
    {

    }

	/**
	 * @author carles.mateo@gmail.com
	 * @description return true if $st_request, normally $_GET or $_POST is empty
	 * @since 2012-08-28
	 */
	public function isEmptyParams($st_request) {
		if (empty($st_request)) {
			//$this->addError(RequestUtils::ERROR_EMPTY_REQUEST, self::API_ERROR_CRITICAL, '', 'Empty request');
			return true;
		}

		return false;
	}

	/**
	 * @author carles.mateo@gmail.com
	 * @description Check the parameters passed against the mask, and inform the errors.
	 * @since 2012-04-01
	 */
    public function checkParams($st_request = array()) {
		// We use the engine
		$st_request_parsed = $this->parseParams($st_request, $this->st_parameters_mask);
		// Set the var with the field-data
		$this->st_request_parsed = $st_request_parsed;

		if ($st_request_parsed['failed'] == true) {
			$this->addError(self::ERROR_INVALID_VERB_RESPONSE, self::API_ERROR_CRITICAL, '', 'Invalid combination verb/response');
		}
		// Take the specific field errors and add them to the superior error reporting response
		foreach ($st_request_parsed['data'] as $s_key => $st_parameter_value) {
			// We do not validate fields not declared

			// Moved to checkparams
			/*	// We set in the object the value sent
				if (isset($st_request_parsed['data'][$s_key]['value_sent'][0][$s_key]) && count($st_request_parsed['data'][$s_key]['value_sent'][0][$s_key]) >0 ) {
					$s_current_value = $st_request_parsed['data'][$s_key]['value_sent'][0][$s_key];
				} else {
					$s_current_value = '';
				}

				$st_request_parsed['data'][$s_key]['current_value'] = $s_current_value;
			*/
				// Process Errors
				if (isset($st_parameter_value['error_codes'])) {
					foreach($st_parameter_value['error_codes'] as $i_error_index=>$i_error_number) {
						$this->addError(1000+$i_error_number, self::API_ERROR_CRITICAL, $s_key, $s_key, $st_parameter_value['html_type']);
					}
				}

//				}
		}

		// Store the object, with current_value updated
		$this->st_request_parsed = $st_request_parsed;
		$this->b_request_parsed_done = true;

    }

	public function addError($i_error_number, $i_error_type, $s_error_field, $s_error_description_en, $s_error_html_type = '')
	{
		// html_type is so the front knows if it can set the focus or not
		$this->st_errors['errors'][] = array(	'error_code' => $i_error_number,
												'error_type' => $i_error_type,
                                                'error_field' => $s_error_field,
												'error_description_en' => $s_error_description_en,
												'error_html_type' => $s_error_html_type);

		if ($i_error_type == self::API_ERROR_CRITICAL) {
			$this->st_errors['num_errors']++;
			$this->st_errors['num_errors_critical']++;
		}
		if ($i_error_type == self::API_ERROR_WARNING) {
			$this->st_errors['num_errors']++;
			$this->st_errors['num_errors_warning']++;
		}
	}

	/*
	 * getErrors
	 * Return the list of errors found
	*/
	public function getErrors()
	{
		return $this->st_errors;
	}

	/*
	 * mayContinue
	 * Return true if no critical errors detected, otherwise false in order to save CPU
	*/
	function mayContinue()
	{

		if ($this->st_errors['num_errors_critical'] == 0 ) {
			return true;
		}
		else
		{
			return false;
		}

	}

	/*
	 * getRequestParsed
	 * Return the parsed request
	*/
	public function getRequestParsed() {
		return $this->st_request_parsed;
	}

	/**
	 * @author carles.mateo@gmail.com
	 * @description Return the fields of the group indicated
	 * @since 2012-04-01
	 */
    public function getFormFieldsOfGroup($s_group, $s_operation_mode = 'extended') {
        // operation_mode = simple or extended
        $st_form = $this->st_request_parsed['parameters'];
        $st_mask = $this->st_parameters_mask;   // The rules
        $st_form_fields = array();

        foreach ($st_mask as $s_field_name => $st_field_properties)
        {
            if ($s_group == $st_field_properties['group']) {
                if ($s_operation_mode == 'simple') {
                    $st_form_fields[$s_field_name] = $st_form[$s_field_name];
                }
                else
                {
					// TODO: think all this
                    $s_data_type = 'string';
                    if ($st_field_properties['type'] == self::DATATYPE_STRING) {
                        $s_data_type = 'string';
                    }
                    if ($st_field_properties['type'] == self::DATATYPE_INTEGER) {
                        $s_data_type = 'int';
                    }
/*                    if ($st_field_properties['type'] == self::DATATYPE_CHECKBOX) {
                        $s_data_type = 'int';
                    } */
                    if ($st_field_properties['is_password'] == true) {
                        $s_data_type = 'password';
                    }
                    if ($st_field_properties['is_check'] == true) {
                        $s_data_type = 'check';
                        // Els checks no es carreguen com a paràmetre si no els han clicat
                        if (!isset($st_form[$s_field_name])) {
                            $st_form[$s_field_name] = '';
                        }
                    }
                    $st_form_fields[$s_field_name] = array( 'value' => $st_form[$s_field_name],
                                                            'type' => $s_data_type);
                }

            }
        }

        return $st_form_fields;
    }

	/*
	 * getParamsWildacardParams
	 * Return the params that were passed as key/value to the exercise
	*/
	public function getParamsValidated($s_mode_parameters = self::MODE_EXPECTED_VALUES, $b_simple_array_mode = true) {
		$st_result=array();

		if ($s_mode_parameters == self::MODE_EXPECTED_VALUES) {
			foreach($this->st_request_parsed['data'] as $s_field_id => $st_sub_request) {
				if ($s_field_id != '*') {
					// We exclude non expected vars
					//foreach($this->st_request_parsed['data'][$s_field_id]['value_sent'] as $i_key=>$st_params) {
					// Current_value és un sol valor mentre que value_sent contempla diversos camps repetits
					//foreach($this->st_request_parsed['data'][$s_field_id]['current_value'] as $i_key=>$st_params) {

						if ($b_simple_array_mode == true) {
							//$st_result[$s_field_id] = $st_params[$s_field_id];
							$st_result[$s_field_id] = $this->st_request_parsed['data'][$s_field_id]['current_value'];
						} else {
							//$st_result[]=$st_params;
							$st_result[] = array($s_field_id => $this->st_request_parsed['data'][$s_field_id]['current_value']);
						}
					//}
				}
			}

			return $st_result;
		}

		// We send the non expected params
		// Note: For * we don't keep current_value, only value_sent
		// If we have keys to add and the parser didn't detect errors...
		if (isset($this->st_request_parsed['data']['*']) && $this->mayContinue()) {
			foreach($this->st_request_parsed['data']['*']['value_sent'] as $i_key=>$st_params) {
				if ($b_simple_array_mode == true) {
					foreach($st_params as $s_field_id=>$s_field_value) {
						$st_result[$s_field_id] = $s_field_value;
					}
				} else {
					$st_result[]=$st_params;
				}

			}
		}

		return $st_result;

	}

	public function getFormHtmlAction() {
		return $this->s_form_html_action;
	}

	public function getFormHtmlMethod() {
		return $this->s_form_html_method;
	}

	public function setFormHtmlAction($s_form_html_action) {
		$this->s_form_html_action = $s_form_html_action;
	}

	public function setFormHtmlMethod($s_form_html_method) {
		$this->s_form_html_action = $s_form_html_method;
	}

	public function getVersionId() {
		return $this->s_version_id;
	}

	public function getActionId() {
		return $this->s_action_id;
	}

	public function getFormId() {
		return $this->s_form_id;
	}

	public function getUserId() {
		return $this->s_user_id;
	}

	public function getFieldCurrentValue($s_field) {

		$s_field_value = '';

		if ($this->b_request_parsed_done==true) {
			if (isset($this->st_request_parsed['data']) && isset($this->st_request_parsed['data'][$s_field]['current_value'])) {
			//if (isset($this->st_request_parsed['parameters']) && isset($this->st_request_parsed['parameters'][$s_field])) {
			//	$s_field_value = $this->st_request_parsed['parameters'][$s_field];
				$s_field_value = $this->st_request_parsed['data'][$s_field]['current_value'];
				//echo 'Debug:'.$s_field.' = '.$s_field_value."<br />";
			} /*else {
				echo 'undefined:'.$s_field.'<br />';ds($this->st_request_parsed);
			}*/
		} else {
			if (isset($this->st_parameters_mask[$s_field]) && isset($this->st_parameters_mask[$s_field]['current_value'])) {
				$s_field_value = $this->st_parameters_mask[$s_field]['current_value'];
			}
		}

		return $s_field_value;

	}

	public function getFieldDefaultValue($s_field) {
		$s_field_value = '';

		if (isset($this->st_parameters_mask[$s_field]) && isset($this->st_parameters_mask[$s_field]['default_value'])) {
				$s_field_value = $this->st_parameters_mask[$s_field]['default_value'];
		}

		return $s_field_value;
	}

	public function getFieldCheckboxChecked($s_field) {
		$b_field_value = false;

		if ($this->b_request_parsed_done==true) {
			if (isset($this->st_request_parsed['data']) && isset($this->st_request_parsed['data'][$s_field]['html_checkbox_checked'])) {
				$b_field_value = $this->st_request_parsed['data'][$s_field]['html_checkbox_checked'];
			}
		} else {
			if (isset($this->st_parameters_mask[$s_field]) && isset($this->st_parameters_mask[$s_field]['html_checkbox_checked'])) {
				$b_field_value = $this->st_parameters_mask[$s_field]['html_checkbox_checked'];
			}
		}

		return $b_field_value;

	}

	public function getFieldSelectMultiselect($s_field) {
		$b_field_value = false;

		if ($this->b_request_parsed_done==true) {
			if (isset($this->st_request_parsed['data']) && isset($this->st_request_parsed['data'][$s_field]['html_select_multiselect'])) {
				$b_field_value = $this->st_request_parsed['data'][$s_field]['html_select_multiselect'];
			}
		} else {
			if (isset($this->st_parameters_mask[$s_field]) && isset($this->st_parameters_mask[$s_field]['html_select_multiselect'])) {
				$b_field_value = $this->st_parameters_mask[$s_field]['html_select_multiselect'];
			}
		}

		return $b_field_value;

	}

	public function getFieldValuesAccepted($s_field) {

		$st_field_value = array();

		if ($this->b_request_parsed_done==true) {
			if (isset($this->st_request_parsed['data']) && isset($this->st_request_parsed['data'][$s_field]['values_accepted'])) {
				$st_field_value = $this->st_request_parsed['data'][$s_field]['values_accepted'];
			}
		} else {
			if (isset($this->st_parameters_mask[$s_field]) && isset($this->st_parameters_mask[$s_field]['values_accepted'])) {
				$st_field_value = $this->st_parameters_mask[$s_field]['values_accepted'];
			}
		}

		return $st_field_value;

	}

	/**
	 * @author carles.mateo@gmail.com
	 * @description It returns the class depending if it has errors or not
	 * @since 2012-08-29
	 */
	public function getFieldClass($s_field) {
		$s_field_value = '';

		if ($this->b_request_parsed_done==true) {
			if (isset($this->st_request_parsed['data']) && isset($this->st_request_parsed['data'][$s_field]['html_class'])) {
				$s_html_class = $this->st_request_parsed['data'][$s_field]['html_class'];
				$s_field_value = $s_html_class;

				$st_errors = $this->getFieldErrors($s_field);
				if (!empty($st_errors)) {
					// There are errors, so we send the class of the error
					$s_html_class_error = $this->st_request_parsed['data'][$s_field]['html_class_error'];
					if ($s_html_class_error != '') {
						// Only if it is not blank, we will use it
						$s_field_value = $s_html_class_error;
					}
				}
			}
		} else {
			if (isset($this->st_parameters_mask[$s_field]) && isset($this->st_parameters_mask[$s_field]['html_class'])) {
				$s_field_value = $this->st_parameters_mask[$s_field]['html_class'];
			}
		}

		return $s_field_value;

	}

	public function addHiddenToForm($s_param_name, $s_type = self::DATATYPE_STRING,
								  	$b_required = false, $i_length_min_or_value_min, $i_length_max_or_value_max, $s_preg_match_mask = '', $st_values_accepted = array(),
								  	$s_group = '',
								  	$s_default_value = '', $s_current_value = '') {
		$s_html_type = self::HTML_TYPE_HIDDEN;

		if ($s_type == self::DATATYPE_STRING || $s_type == self::DATATYPE_DATE || $s_type == self::DATATYPE_EMAIL ||
			$s_type == self::DATATYPE_IPV4 || $s_type == self::DATATYPE_IPV6 || $s_type == self::DATATYPE_VHOST) {
			$i_length_max = $i_length_max_or_value_max;
			$i_length_min = $i_length_min_or_value_min;
			$i_value_min=0; $i_value_max=0;
		} else {
			$i_value_max=$i_length_max_or_value_max;
			$i_value_min=$i_length_min_or_value_min;
			$i_length_max = 0; $i_length_min = 0;
		}

		// Unnecessary for this html type / data type
		$b_html_checkbox_checked = false;
		$s_html_size_cols = 14; $s_help_field ='';
		$s_label = ''; $i_max_ocurrences = 1;
		$b_html_select_multiselect = false;
		$b_is_readonly = false; $b_is_disabled = false;
		$s_html_class=''; $s_html_class_error =''; $s_html_size_rows = 0;
		// Probably will be refactored
		$b_is_password = false; $b_is_email = false; $b_is_check = false;

		$this->addParameterToForm(	$s_param_name, $s_label, $s_help_field, $s_html_type, $s_html_class, $s_html_class_error, $s_html_size_cols, $s_html_size_rows, $s_type, $b_required, $i_length_min, $i_length_max, $i_value_min, $i_value_max,
                             		$s_preg_match_mask, $st_values_accepted, $b_html_checkbox_checked, $b_html_select_multiselect, $i_max_ocurrences, $b_is_password, $b_is_email, $b_is_check,
                                    $b_is_readonly, $b_is_disabled, $s_group,
                                 	$s_default_value, $s_current_value);
	}

	public function addTextToForm($s_param_name, $s_label, $s_help_field = '', $s_html_size_cols = 25, $s_type = self::DATATYPE_STRING, $s_html_class = self::HTML_NO_CLASS, $s_html_class_error = self::HTML_NO_CLASS,
								  $b_required = false, $i_length_min, $i_length_max, $s_preg_match_mask = '', $st_values_accepted = array(),
								  $b_is_readonly = false, $b_is_disabled = false, $s_group = '',
								  $s_default_value = '', $s_current_value = '') {
		$s_html_type = self::HTML_TYPE_TEXT;
		$i_max_ocurrences = 1;

		// Unnecessary for this html type / data type
		$s_html_size_rows = 0;$i_value_min=0; $i_value_max=0; $b_html_checkbox_checked = false;
		// Probably will be refactored
		$b_is_password = false; $b_is_email = false; $b_is_check = false; $b_html_select_multiselect = false;

        $this->addParameterToForm(	$s_param_name, $s_label, $s_help_field, $s_html_type, $s_html_class, $s_html_class_error, $s_html_size_cols, $s_html_size_rows, $s_type, $b_required, $i_length_min, $i_length_max, $i_value_min, $i_value_max,
            $s_preg_match_mask, $st_values_accepted, $b_html_checkbox_checked, $b_html_select_multiselect, $i_max_ocurrences, $b_is_password, $b_is_email, $b_is_check,
            $b_is_readonly, $b_is_disabled, $s_group,
            $s_default_value, $s_current_value);
    }

    public function addPasswordToForm($s_param_name, $s_label, $s_help_field = '', $s_html_size_cols = 25, $s_type = self::DATATYPE_STRING, $s_html_class = self::HTML_NO_CLASS, $s_html_class_error = self::HTML_NO_CLASS,
                                      $b_required = false, $i_length_min, $i_length_max, $s_preg_match_mask = '', $st_values_accepted = array(),
                                      $b_is_readonly = false, $b_is_disabled = false, $s_group = '',
                                      $s_default_value = '', $s_current_value = '') {
        $s_html_type = self::HTML_TYPE_PASSWORD;
        $i_max_ocurrences = 1;

        // Unnecessary for this html type / data type
        $s_html_size_rows = 0;$i_value_min=0; $i_value_max=0; $b_html_checkbox_checked = false;
        // Probably will be refactored
        $b_is_password = true; $b_is_email = false; $b_is_check = false; $b_html_select_multiselect = false;

		$this->addParameterToForm(	$s_param_name, $s_label, $s_help_field, $s_html_type, $s_html_class, $s_html_class_error, $s_html_size_cols, $s_html_size_rows, $s_type, $b_required, $i_length_min, $i_length_max, $i_value_min, $i_value_max,
                             		$s_preg_match_mask, $st_values_accepted, $b_html_checkbox_checked, $b_html_select_multiselect, $i_max_ocurrences, $b_is_password, $b_is_email, $b_is_check,
                                    $b_is_readonly, $b_is_disabled, $s_group,
                                 	$s_default_value, $s_current_value);
	}

	public function addTextAreaToForm($s_param_name, $s_label, $s_help_field = '', $s_html_size_cols = 25, $s_html_size_rows = 5, $s_type = self::DATATYPE_STRING, $s_html_class = self::HTML_NO_CLASS, $s_html_class_error = self::HTML_NO_CLASS,
									  $b_required = false, $i_length_min, $i_length_max, $s_preg_match_mask = '', $st_values_accepted = array(),
									  $b_is_readonly = false, $b_is_disabled = false, $s_group = '',
									  $s_default_value = '', $s_current_value = '') {
		$s_html_type = self::HTML_TYPE_TEXTAREA;
		$i_max_ocurrences = 1;

		// Unnecessary for this html type / data type
		$i_value_min=0; $i_value_max=0; $b_html_checkbox_checked = false;
		// Probably will be refactored
		$b_is_password = false; $b_is_email = false; $b_is_check = false; $b_html_select_multiselect = false;

		$this->addParameterToForm(	$s_param_name, $s_label, $s_help_field, $s_html_type, $s_html_class, $s_html_class_error, $s_html_size_cols, $s_html_size_rows, $s_type, $b_required, $i_length_min, $i_length_max, $i_value_min, $i_value_max,
                             		$s_preg_match_mask, $st_values_accepted, $b_html_checkbox_checked, $b_html_select_multiselect, $i_max_ocurrences, $b_is_password, $b_is_email, $b_is_check,
                                    $b_is_readonly, $b_is_disabled, $s_group,
                                 	$s_default_value, $s_current_value);
	}

	public function addSelectToForm($s_param_name, $s_label, $s_help_field = '', $s_type = self::DATATYPE_STRING, $s_html_class = self::HTML_NO_CLASS, $s_html_class_error = self::HTML_NO_CLASS, $s_html_size_rows = 5,
								  	$b_required = false, $i_length_min, $i_length_max, $s_preg_match_mask = '', $st_values_accepted = array(), $b_html_select_multiselect = false, $i_max_ocurrences = 1,
								  	$b_is_readonly = false, $b_is_disabled = false, $s_group = '',
								  	$s_default_value = '', $s_current_value = '') {
		$s_html_type = self::HTML_TYPE_SELECT;

		// Unnecessary for this html type / data type
		$i_value_min=0; $i_value_max=0; $b_html_checkbox_checked = false;
		$s_html_size_cols = 14;
		// Probably will be refactored
		$b_is_password = false; $b_is_email = false; $b_is_check = false;

		$this->addParameterToForm(	$s_param_name, $s_label, $s_help_field, $s_html_type, $s_html_class, $s_html_class_error, $s_html_size_cols, $s_html_size_rows, $s_type, $b_required, $i_length_min, $i_length_max, $i_value_min, $i_value_max,
                             		$s_preg_match_mask, $st_values_accepted, $b_html_checkbox_checked, $b_html_select_multiselect, $i_max_ocurrences, $b_is_password, $b_is_email, $b_is_check,
                                    $b_is_readonly, $b_is_disabled, $s_group,
                                 	$s_default_value, $s_current_value);
	}

    public function addParameterToForm($s_param_name, $s_label, $s_help_field = '', $s_html_type = self::HTML_TYPE_TEXT,
									   $s_html_class = '', $s_html_class_error = '', $s_html_size_cols = 25, $s_html_size_rows = 1, $s_type, $b_required, $i_length_min, $i_length_max, $i_value_min, $i_value_max,
                                       $s_preg_match_mask = '', $st_values_accepted = array(), $b_html_checkbox_checked = false, $b_html_select_multiselect = self::HTML_SELECT_NOT_MULTISELECT, $i_max_ocurrences = 1, $b_is_password = false, $b_is_email = false, $b_is_check = false,
                                       $b_is_readonly = false, $b_is_disabled = false, $s_group = '',
                                       $s_default_value = '', $s_current_value = '', $b_input_decode_html_entities = self::INPUT_DECODE_HTML_ENTITIES)
    {
        // html_type: text, password, textarea, hidden, checkbox, select

        $this->st_parameters_mask[$s_param_name] = array(   'html_type' 					=> $s_html_type,
                                                            'html_size_cols' 				=> $s_html_size_cols,
                                                            'html_size_rows' 				=> $s_html_size_rows,
                                                            'type'  						=> $s_type,
                                                            'required' 						=> $b_required,
                                                            'length_min' 					=> $i_length_min,
                                                            'length_max' 					=> $i_length_max,
                                                            'value_min' 					=> $i_value_min,
                                                            'value_max' 					=> $i_value_max,
															'values_accepted'				=> $st_values_accepted,
                                                            'preg_match_mask' 				=> $s_preg_match_mask,
															'html_checkbox_checked' 		=> $b_html_checkbox_checked,
															'html_select_multiselect'		=> $b_html_select_multiselect,
															'html_class'					=> $s_html_class,
															'html_class_error'				=> $s_html_class_error,
                                                            'max_occurrences' 				=> $i_max_ocurrences,
                                                            'is_password' 					=> $b_is_password,// TODO remove
                                                            'is_email' 						=> $b_is_email,   // TODO remove
                                                            'is_check' 						=> $b_is_check,   // TODO remove
                                                            'is_readonly' 					=> $b_is_readonly,
                                                            'is_disabled' 					=> $b_is_disabled,
                                                            'group' 						=> $s_group,
															'default_value' 				=> $s_default_value,
                                                            'current_value' 				=> $s_current_value,
                                                            'label' 						=> $s_label,
	                                                        'help_field' 					=> $s_help_field,
															'input_decode_html_entities'	=> $b_input_decode_html_entities
                                                        );

    }

    public function getParametersAsHtmlControls($s_group = null) {

        $st_return_html_controls_array = array();

/*		if (isset($this->st_request_parsed)) {
			$st_params = $this->st_request_parsed['data'];
		} else { */
			$st_params = $this->st_parameters_mask;
//		}
//wd($this->st_request_parsed['data']); // TODO: Funció per a saber el current_value d'un camp
        foreach ($st_params as $s_param_name => $st_param_value) {
		//foreach ($this->st_parameters_mask as $s_param_name => $st_param_value) {

			$s_html_control = '';

			// If passed $s_group we return the render only for group
			if ($s_group == null || $s_group == '' || $st_param_value['group'] == $s_group) {
				$s_current_value=$this->getFieldCurrentValue($s_param_name);
				$s_current_value_renderized_for_html=$this->sanitizeString($s_current_value);
				$s_default_value=$this->getFieldDefaultValue($s_param_name);
				$b_html_checkbox_checked=$this->getFieldCheckboxChecked($s_param_name);
				$b_html_select_multiselect=$this->getFieldSelectMultiselect($s_param_name);
				$st_values_accepted = $this->getFieldValuesAccepted($s_param_name);
				// It will give the name of the class depending if the field had errors
				$s_current_class = $this->getFieldClass($s_param_name);

				if ($st_param_value['html_type'] == self::HTML_TYPE_HIDDEN) {
					$s_html_control .= '<input type="hidden" name="'.$s_param_name.'" id="'.$s_param_name.'"';
					$s_html_control .= ' value="'.$s_current_value_renderized_for_html.'"';
					$s_html_control .= ' />';

				}

				if ($st_param_value['html_type'] == self::HTML_TYPE_TEXT) {
					$s_html_control .= '<input type="text" name="'.$s_param_name.'" id="'.$s_param_name.'"';
					if ($st_param_value['length_max'] > 0) {
						$s_html_control .= ' maxlength="'.$st_param_value['length_max'].'"';

					}
					if ($st_param_value['html_size_cols'] > 0) {
						$s_html_control .= ' size="'.$st_param_value['html_size_cols'].'"';

					}
					if ($s_current_value_renderized_for_html != '') {
						$s_html_control .= ' value="'.$s_current_value_renderized_for_html.'"';
					}
					if ($s_current_class != '') {
						$s_html_control .= ' class="'.$s_current_class.'"';
					}

					$s_html_control .= ' />';

				}

				 if ($st_param_value['html_type'] == self::HTML_TYPE_PASSWORD) {
					$s_html_control = '<input type="password" name="'.$s_param_name.'" id="'.$s_param_name.'"';
					if ($st_param_value['length_max'] > 0) {
						$s_html_control .= ' maxlength="'.$st_param_value['length_max'].'"';

					}
					if ($st_param_value['html_size_cols'] > 0) {
						$s_html_control .= ' size="'.$st_param_value['html_size_cols'].'"';

					}
					if ($s_current_value_renderized_for_html != '') {
						$s_html_control .= ' value="'.$s_current_value_renderized_for_html.'"';

					}
					if ($s_current_class != '') {
						$s_html_control .= ' class="'.$s_current_class.'"';
					}

					$s_html_control .= ' />';

				}

				if ($st_param_value['html_type'] == self::HTML_TYPE_TEXTAREA) {
					$s_html_control = '<textarea name="'.$s_param_name.'" id="'.$s_param_name.'"';
					//if ($st_param_value['length_max'] > 0) {
					//    $s_html_control .= ' maxlength="'.$st_param_value['length_max'].'"';
					//}
					if ($st_param_value['html_size_cols'] > 0) {
						$s_html_control .= ' cols="'.$st_param_value['html_size_cols'].'"';
					}
					if ($st_param_value['html_size_rows'] > 0) {
						$s_html_control .= ' rows="'.$st_param_value['html_size_rows'].'"';
					}
					if ($s_current_class != '') {
						$s_html_control .= ' class="'.$s_current_class.'"';
					}
					$s_html_control .= '>';

					if ($s_current_value_renderized_for_html != '') {
						$s_html_control .= $s_current_value_renderized_for_html;

					}

					$s_html_control .= '</textarea>';

				}

				if ($st_param_value['html_type'] == self::HTML_TYPE_CHECK) {
					$s_html_control .= '<input type="checkbox" name="'.$s_param_name.'" id="'.$s_param_name.'" value="'.$s_default_value.'"';
					//if ($s_current_value == 'on' || $s_current_value == '1') {
					if ($b_html_checkbox_checked == true) {
						$s_html_control .= ' checked="checked"';
					}
					if ($s_current_class != '') {
						$s_html_control .= ' class="'.$s_current_class.'"';
					}
					$s_html_control .= ' />';
				}

				if ($st_param_value['html_type'] == self::HTML_TYPE_SELECT) {
					$s_html_control .= '<select name="'.$s_param_name.'" id="'.$s_param_name.'"';
					//if ($s_current_value == 'on' || $s_current_value == '1') {
					if ($b_html_select_multiselect == true) {
						$s_html_control .= ' multiple="multiple" ';
					}
					if ($s_current_class != '') {
						$s_html_control .= ' class="'.$s_current_class.'"';
					}
					$s_html_control .= '>';
					// We paint it even if it has no values, may be they want to fullfill them later with JSon
					foreach($st_values_accepted as $s_label=>$s_value_accepted) {
						$s_html_control .= '<option value="'.$s_value_accepted.'"';
						if ($s_value_accepted == $s_current_value) {
							$s_html_control .= ' selected="selected" ';
						}

						$s_html_control .= '>'.$s_label.'</option>';
					}
					$s_html_control .= '</select>';
				}

				if ($s_html_control != '') {
					$st_return_html_controls_array[] = array(   'id'        			=> $s_param_name,
																'label'     			=> $st_param_value['label'],
																'help_field'			=> $st_param_value['help_field'],
																'required'  			=> $st_param_value['required'],
																'html_code' 			=> $s_html_control,
																'html_type' 			=> $st_param_value['html_type'],
																'html_checkbox_checked' => $b_html_checkbox_checked);
				}

			}

        }

        return $st_return_html_controls_array;
    }

	public function setFormHtmlId($s_form_html_id)
	{
		$this->s_form_html_id = $s_form_html_id;
	}

	public function getFormHtmlId()
	{
		return $this->s_form_html_id;
	}

	public function getLabel($s_field_id)
	{
		if (isset($this->st_parameters_mask[$s_field_id]['label'])) {
			return $this->st_parameters_mask[$s_field_id]['label'];
		} else {
			return '';
		}
	}

	public function getHelpField($s_field_id)
	{
		if (isset($this->st_parameters_mask[$s_field_id]['help_field'])) {
			return $this->st_parameters_mask[$s_field_id]['help_field'];
		} else {
			return '';
		}
	}

	protected function parseParams($st_parameters, $st_parameters_mask) {
	// We get an array for parameters and a mask, and we check it

        $s_mask_key = '';
//echo (serialize($st_parameters));
//echo (serialize($_POST));exit();
        $st_parser_result = array( 'failed' => false,
                                   'num_errors' => 0,
                                   'parameters' => $st_parameters,
                                   'mask' => $st_parameters_mask,
                                   'data' => $st_parameters_mask);

        // We do some arrangements to add new control fields for parser_result
        foreach($st_parser_result['mask'] as $s_mask_key=>$st_mask_key_properties){
            $st_parser_result['data'][$s_mask_key]['value_sent'] = array();
			$st_parser_result['data'][$s_mask_key]['default_value'] = '';
			$st_parser_result['data'][$s_mask_key]['current_value'] = '';
            $st_parser_result['data'][$s_mask_key]['num_times_found'] = 0;
            $st_parser_result['data'][$s_mask_key]['num_errors'] = 0;
			$st_parser_result['data'][$s_mask_key]['html_checkbox_checked'] = false;
            $st_parser_result['data'][$s_mask_key]['error_codes'] = array();
        }

        // First loop for the params, assign all te parameters received to their corresponding match in the mask
        foreach($st_parameters as $s_parameter_key => $s_parameter_value)
        {
			$b_error_found_in_loop = false;
            if (in_array($s_parameter_key, array_keys($st_parameters_mask), self::ARRAY_MODE_SEARCH_STRICT)) {
                // Found mask in the request
                $s_mask_key=$s_parameter_key;
				if (!isset($st_parser_result['data'][$s_mask_key]['num_times_found'])) {
					$st_parser_result['data'][$s_mask_key]['num_times_found'] = 0;
				}
                $st_parser_result['data'][$s_mask_key]['num_times_found']++;
                $st_parser_result['data'][$s_mask_key]['value_sent'][] = array($s_mask_key => $s_parameter_value);

				if (isset($st_parameters_mask[$s_mask_key]['values_accepted']) &&
					is_array($st_parameters_mask[$s_mask_key]['values_accepted']) &&
					count($st_parameters_mask[$s_mask_key]['values_accepted']) >0) {
					// There are values_accepted
					if (in_array($s_parameter_value, $st_parameters_mask[$s_mask_key]['values_accepted'], self::ARRAY_MODE_SEARCH_STRICT)) {
						// OK
					} else {
						// TODO: error
						//$this->addParseParamError($st_parser_result, $s_mask_key, self::ERROR_VALUE_NOT_IN_ACCEPTED_VALUES);

					}
				}
            }
            else
            {
                $s_mask_key='*';
				if (!isset($st_parser_result['data'][$s_mask_key]['num_times_found'])) {
					$st_parser_result['data'][$s_mask_key]['num_times_found'] = 0;
				}
                $st_parser_result['data'][$s_mask_key]['num_times_found']++;
                $st_parser_result['data'][$s_mask_key]['value_sent'][] = array($s_parameter_key => $s_parameter_value);
            }
            // Extra Security. We have regular expressions, but it never hurts
//            if ($this->getHasInjectionPattern($s_parameter_key) || $this->getHasInjectionPattern($s_parameter_value)) {
//                // We found chars used for injection
//                $this->addParseParamError($st_parser_result, $s_mask_key, self::ERROR_INJECTION_ATTACK);
//				$b_error_found_in_loop = true;
//            }

            // Check if occurrences exceed the max allowed
            // Please note that as we are working with parameters from $_GET or $_POST
            // we will not detect repeated for variables identified like f.e.: 'user_id'
            // This is to detect number of occurrences under '*'
            if (isset($st_parser_result['data'][$s_mask_key]['max_occurrences']) && $st_parser_result['data'][$s_mask_key]['num_times_found'] > $st_parser_result['data'][$s_mask_key]['max_occurrences']) {
                $this->addParseParamError($st_parser_result, $s_mask_key, self::ERROR_MAX_OCCURRENCES);
				$b_error_found_in_loop = true;
            }

			// If the field is not defined, we do not check for rules
			if (isset($st_parameters_mask[$s_mask_key])) {

				// Copy the value passed (fe: by $_POST) to current_value
				// We convert Html entities to chars
				if ($st_parameters_mask[$s_mask_key]['input_decode_html_entities']) {
					// For example if we get &#39; we turn into '
                    if (is_string($s_parameter_value)) {
                        // Prevent Array[] injection
                        $st_parser_result['data'][$s_mask_key]['current_value'] = htmlspecialchars_decode($s_parameter_value, ENT_QUOTES);
                        $s_parameter_value = $st_parser_result['data'][$s_mask_key]['current_value'];
                    }   // TODO: The case is array
				} else {
					$st_parser_result['data'][$s_mask_key]['current_value'] = $s_parameter_value;
                    // TODO: The case is array
				}

				if (is_string($s_parameter_value)) {
                    $i_parameter_value_length = strlen($s_parameter_value);
                } else {
                    $i_parameter_value_length = 0;
                }
				// TODO: threat if is Array

				if ($st_parameters_mask[$s_mask_key]['type'] == self::DATATYPE_STRING) {
					// Check for integrity of a param STRING
//echo $s_parameter_value.' '.$i_parameter_value_length; exit();
					// If it is not required, we do not check the length_min
					if ( isset($st_parser_result['data'][$s_mask_key]['required'])  &&
						 $st_parser_result['data'][$s_mask_key]['required'] == false &&
						 ($i_parameter_value_length == 0 || (
						  $i_parameter_value_length>=$st_parameters_mask[$s_mask_key]['length_min'] &&
						  $i_parameter_value_length<=$st_parameters_mask[$s_mask_key]['length_max']))
						)
					{
						// OK
						if ($st_parameters_mask[$s_mask_key]['html_type'] == self::HTML_TYPE_CHECK &&
						    $i_parameter_value_length>0) {
							// We've got data, so checkbox is on
							$st_parser_result['data'][$s_mask_key]['html_checkbox_checked'] = self::HTML_CHECKBOX_CHECKED;
						}

					} else {
						if ($i_parameter_value_length<$st_parameters_mask[$s_mask_key]['length_min'] ||
							$i_parameter_value_length>$st_parameters_mask[$s_mask_key]['length_max'])
						{
							$this->addParseParamError($st_parser_result, $s_mask_key, self::ERROR_INVALID_LENGTH);
							$b_error_found_in_loop = true;
						} else {
							// OK
							if ($st_parameters_mask[$s_mask_key]['html_type'] == self::HTML_TYPE_CHECK &&
								$i_parameter_value_length>0) {
								// We've got data, so checkbox is on
								$st_parser_result['data'][$s_mask_key]['html_checkbox_checked'] = self::HTML_CHECKBOX_CHECKED;
							}
						}

					}

					if (!empty($st_parameters_mask[$s_mask_key]['preg_match_mask']) &&
						!preg_match($st_parameters_mask[$s_mask_key]['preg_match_mask'], $s_parameter_value)) {
						$this->addParseParamError($st_parser_result, $s_mask_key, self::ERROR_NOT_MATCHING_EXPRESSION);
						$b_error_found_in_loop = true;
					}

					// TODO: Rethinking this. May be I delete it.
					if (isset($st_parameters_mask[$s_mask_key]['is_email']) && $st_parameters_mask[$s_mask_key]['is_email'] == true) {
						$b_is_email = filter_var($s_parameter_value, FILTER_VALIDATE_EMAIL);
						if ($b_is_email == false) {
							// Is not a valid email otherwise we will have had an string
							$this->addParseParamError($st_parser_result, $s_mask_key, self::ERROR_NOT_VALID_EMAIL);
							$b_error_found_in_loop = true;
						}
					}

					// Filter Values in Array
					if (isset($st_parameters_mask[$s_mask_key]['values_accepted']) &&
						is_array($st_parameters_mask[$s_mask_key]['values_accepted']) &&
						count($st_parameters_mask[$s_mask_key]['values_accepted']) > 0) {
						// Array of Accepted Values. CASE SENSITIVE
						echo 'Debug '.$s_mask_key.' - '; // TODO: Treure
						if (!in_array($s_parameter_value, $st_parameters_mask[$s_mask_key]['values_accepted'], self::ARRAY_MODE_SEARCH_STRICT)) {
							echo 'Debug: Not accepted<br />';
							$this->addParseParamError($st_parser_result, $s_mask_key, self::ERROR_VALUE_NOT_IN_ACCEPTED_VALUES);
							$b_error_found_in_loop = true;
						}

					}

				}

/*				if ($st_parameters_mask[$s_mask_key]['type'] == self::DATATYPE_CHECKBOX) {
					// Check for integrity of a param STRING
					$i_parameter_value_length=strlen($s_parameter_value);
//echo $s_parameter_value.' '.$i_parameter_value_length; exit();
					// If it is not required, we do not check the length_min
					if ( isset($st_parser_result['data'][$s_mask_key]['required'])  &&
						 $st_parser_result['data'][$s_mask_key]['required'] == false &&
						 ($i_parameter_value_length == 0 ||
						  $s_parameter_value == '1' || $s_parameter_value = 'on')
						)
					{
						// OK
						if ($i_parameter_value_length>0) {
							// We set 'on' as '1'. Overwrite.
							//$this->updateParamCurrentValue($s_parameter_key, '1');
							$st_parser_result['data'][$s_mask_key]['current_value'] = 1;
						}

					} else {
							$this->addParseParamError($st_parser_result, $s_mask_key, self::ERROR_VALUE_NOT_IN_ACCEPTED_VALUES);
							$b_error_found_in_loop = true;
					}
				} */


				if ($st_parameters_mask[$s_mask_key]['type'] == self::DATATYPE_DATE) {
					// Check for integrity of a param STRING
					//$i_parameter_value_length=strlen($s_parameter_value);

					if ($i_parameter_value_length>0) {
						// Date is not empty
						if ($i_parameter_value_length<$st_parameters_mask[$s_mask_key]['length_min'] ||
							$i_parameter_value_length>$st_parameters_mask[$s_mask_key]['length_max'])
						{
							$this->addParseParamError($st_parser_result, $s_mask_key, self::ERROR_INVALID_LENGTH);
							$b_error_found_in_loop = true;
						}
						else
						{
							// Date length Ok, let's check if the date is valid
							$i_year=substr($s_parameter_value,0,4);
							$i_month=substr($s_parameter_value,5,2);
							$i_day=substr($s_parameter_value, 8,2);

							if (!checkdate($i_month, $i_day, $i_year)) {
								$this->addParseParamError($st_parser_result, $s_mask_key, self::ERROR_DATE_NOT_VALID);
								$b_error_found_in_loop = true;
							}
						}

						if (!empty($st_parameters_mask[$s_mask_key]['preg_match_mask']) &&
							!preg_match($st_parameters_mask[$s_mask_key]['preg_match_mask'], $s_parameter_value)) {
							$this->addParseParamError($st_parser_result, $s_mask_key, self::ERROR_NOT_MATCHING_EXPRESSION);
							$b_error_found_in_loop = true;
						}

						// Filter Values in Array
						if (isset($st_parameters_mask[$s_mask_key]['values_accepted']) &&
							is_array($st_parameters_mask[$s_mask_key]['values_accepted']) &&
							count($st_parameters_mask[$s_mask_key]['values_accepted']) > 0) {
							// Array of Accepted Values. CASE SENSITIVE
							if (!in_array($s_parameter_value, $st_parameters_mask[$s_mask_key]['values_accepted'], self::ARRAY_MODE_SEARCH_STRICT)) {
								$this->addParseParamError($st_parser_result, $s_mask_key, self::ERROR_VALUE_NOT_IN_ACCEPTED_VALUES);
								$b_error_found_in_loop = true;
							}

						}


					}

				}

				if ($st_parameters_mask[$s_mask_key]['type'] == self::DATATYPE_EMAIL) {
					$b_is_email = filter_var($s_parameter_value, FILTER_VALIDATE_EMAIL);
					if ($b_is_email == false) {
						// Is not a valid email otherwise we will have had an string
						$this->addParseParamError($st_parser_result, $s_mask_key, self::ERROR_NOT_VALID_EMAIL);
						$b_error_found_in_loop = true;
					}

					// Filter Values in Array
					if (isset($st_parameters_mask[$s_mask_key]['values_accepted']) &&
						is_array($st_parameters_mask[$s_mask_key]['values_accepted']) &&
						count($st_parameters_mask[$s_mask_key]['values_accepted']) > 0) {
						// Array of Accepted Values. CASE SENSITIVE
						if (!in_array($s_parameter_value, $st_parameters_mask[$s_mask_key]['values_accepted'], self::ARRAY_MODE_SEARCH_STRICT)) {
							$this->addParseParamError($st_parser_result, $s_mask_key, self::ERROR_VALUE_NOT_IN_ACCEPTED_VALUES);
							$b_error_found_in_loop = true;
						}

					}

				}

				if ($st_parameters_mask[$s_mask_key]['type'] == self::DATATYPE_INTEGER) {
					// Check for integrity of a param INTEGER
					if (!is_numeric($s_parameter_value)) {
						$this->addParseParamError($st_parser_result, $s_mask_key, self::ERROR_INVALID_NUMBER);
						$b_error_found_in_loop = true;
					}
					$i_parameter_value=intval($s_parameter_value);
					if ( $i_parameter_value != $s_parameter_value) {
						// We have a numeric value like 1e10 or 0xFF or 1.15 but is not integer decimal
						$this->addParseParamError($st_parser_result, $s_mask_key, self::ERROR_NUMERIC_NOT_INTEGER);
						$b_error_found_in_loop = true;
					}
					if ($i_parameter_value<$st_parameters_mask[$s_mask_key]['value_min'] ||
						$i_parameter_value>$st_parameters_mask[$s_mask_key]['value_max'])
					{
						$this->addParseParamError($st_parser_result, $s_mask_key, self::ERROR_INVALID_LENGTH);
						$b_error_found_in_loop = true;
					}

					if ($b_error_found_in_loop == false) {
						// We set the value, so if a checkbox passed '' and is defined as integer is gives 0
						$st_parser_result['data'][$s_mask_key]['current_value'] = $i_parameter_value;

						if ($st_parameters_mask[$s_mask_key]['html_type'] == self::HTML_TYPE_CHECK &&
						    $i_parameter_value_length>0) {
							// We've got data, so checkbox is on
							$st_parser_result['data'][$s_mask_key]['html_checkbox_checked'] = self::HTML_CHECKBOX_CHECKED;
						}

					}

					// Filter Values in Array
					if (isset($st_parameters_mask[$s_mask_key]['values_accepted']) &&
						is_array($st_parameters_mask[$s_mask_key]['values_accepted']) &&
						count($st_parameters_mask[$s_mask_key]['values_accepted']) > 0) {
						// Array of Accepted Values. CASE SENSITIVE
						if (!in_array($s_parameter_value, $st_parameters_mask[$s_mask_key]['values_accepted'], self::ARRAY_MODE_SEARCH_STRICT)) {
							$this->addParseParamError($st_parser_result, $s_mask_key, self::ERROR_VALUE_NOT_IN_ACCEPTED_VALUES);
							$b_error_found_in_loop = true;
						}
					}

				}
			}
        }

        // Second loop, to check if required conditions have been satisfied
        foreach($st_parser_result['data'] as $s_mask_key => $st_parameters_fields)
        {
			if ($s_mask_key != '*' && $st_parser_result['data'][$s_mask_key]['type'] == self::DATATYPE_INTEGER &&
				$st_parser_result['data'][$s_mask_key]['current_value'] == '') {
				// INTEGER type without value, probably a CheckBox. Set to 0
				$st_parser_result['data'][$s_mask_key]['current_value'] = 0;
			}

            if (isset($st_parser_result['data'][$s_mask_key]['required']) &&
				$st_parser_result['data'][$s_mask_key]['required'] == true &&
                $st_parser_result['data'][$s_mask_key]['num_times_found']<1)
            {
                $this->addParseParamError($st_parser_result, $s_mask_key, self::ERROR_REQUIRED_FIELD_NOT_PROVIDED);
            }
        }

        if ($st_parser_result['num_errors']>0) {
            $st_parser_result['failed'] = true;
        }

        return $st_parser_result;

    }

    protected function addParseParamError(&$st_parser_result, $s_mask_key, $i_error_code) {
        // Increment global error counter
        $st_parser_result['num_errors']++;
        // Increment specific field error counter
        $st_parser_result['data'][$s_mask_key]['num_errors']++;
        $st_parser_result['data'][$s_mask_key]['error_codes'][] = $i_error_code;
    }

	// Check for injection in the string
    public function getHasInjectionPattern($s_data) {
        $b_injection_found = false;

        $st_injectable_char_to_search[] = "'";    // For SQL Injection
        $st_injectable_char_to_search[] = "<";    // For XSS Attacks
        $st_injectable_char_to_search[] = ">";
        // We check the "traditional way"
        foreach($st_injectable_char_to_search as $i_key => $s_injectable_char) {
            if (strpos($s_data, $s_injectable_char)) {
                $b_injection_found = true;
                break;  // We don't waste CPU with hackers
            }
        }

        return $b_injection_found;
    }

    public function removeSQLInjection($s_data) {
        $s_data = str_replace("'", '', $s_data);
        $s_data = str_replace("`", '', $s_data);

        return $s_data;
    }

	public function getFieldErrors($s_field) {

		if ($this->b_request_parsed_done==true &&
			isset($st_parser_result['data'][$s_field]['error_codes'])) {
			return $st_parser_result['data'][$s_field]['error_codes'];
		}

		return array();
	}

	/**
	 * @author carles.mateo@gmail.com
	 * @description Get JavaScript vars defining the errors
	 * @since 2012-08-24
	 */
	public function getJavascriptWithFormValidationErrorCodes() {
		$st_errors_in_form = $this->getErrors();
		$b_focus_set = false;

		$s_embed_javascript = '<script type ="text/javascript">//<![CDATA['."\n";
		$s_embed_javascript .= 'i_form_num_errors = '.$st_errors_in_form['num_errors'].';'."\n";
		$s_embed_javascript .= 'st_form_errors = new Array();'."\n";

		foreach ( $st_errors_in_form['errors'] as $i_error_key=>$st_error_data)
		{
		  if ($st_error_data['error_field'] != '' && $st_error_data['error_field'] != '*') {
			  $s_embed_javascript .= "st_form_errors['${st_error_data['error_field']}']=${st_error_data['error_code']};"."\n";
		  }
		}

		$s_embed_javascript .= '//]]></script>'."\n";

		return $s_embed_javascript;
	}

	public function getJavascriptToHighlightFormValidationErrors() {
		$st_errors_in_form = $this->getErrors();
		$b_focus_set = false;

		$s_embed_javascript = '<script type ="text/javascript">//<![CDATA['."\n";

		foreach ( $st_errors_in_form['errors'] as $i_error_key=>$st_error_data)
		{
		  if ($st_error_data['error_field'] != '' && $st_error_data['error_field'] != '*') {
			  if ($b_focus_set==false && ($st_error_data['error_html_type'] == self::HTML_TYPE_TEXT ||
				  $st_error_data['error_html_type'] == self::HTML_TYPE_TEXTAREA))
			  {
				  $b_focus_set = true;
				  $s_embed_javascript .= 'document.getElementById(\''.$st_error_data['error_field'].'\').focus();'."\n";
			  }
			  $s_embed_javascript .= 'document.getElementById(\''.$st_error_data['error_field'].'\').style.borderColor="red";'."\n";
			  $s_embed_javascript .= 'document.getElementById(\''.$st_error_data['error_field'].'\').style.borderStyle="solid";'."\n";
		  }
		}

		$s_embed_javascript .= '//]]></script>'."\n";

		if ($b_focus_set == false) {
			// There was no changes. There is no error or was in input type hidden, and there is no need from javascript to
			// highlight
			return '';
		} else {
			return $s_embed_javascript;
		}

	}

	public function getRequestParsedDone () {
		return $this->b_request_parsed_done;
	}

	public function updateParamCurrentValue($s_param_name, $m_value) {

		if ($this->getRequestParsedDone()==true) {
			// We update the current value, not the value_sent nor the mask current_value
			if (isset($this->st_request_parsed['data'][$s_param_name]['current_value'])) {
				$this->st_request_parsed['data'][$s_param_name]['current_value'] = $m_value;
			}
		} else {
			// We update the mask
			if (isset($this->st_parameters_mask[$s_param_name]['current_value'])) {
				$this->st_parameters_mask[$s_param_name]['current_value'] = $m_value;
			}
		}

	}

	public function sanitizeString($s_string_unfiltered, $i_type = FILTER_SANITIZE_FULL_SPECIAL_CHARS, $i_flags = FILTER_FLAG_NO_ENCODE_QUOTES)
	{
		// valid types:
		// http://www.php.net/manual/en/filter.filters.sanitize.php
		// p.e.: FILTER_VALIDATE_IP FILTER_SANITIZE_STRING FILTER_SANITIZE_EMAIL

		// Quote string with slashes
		$s_string_filtered=filter_var($s_string_unfiltered);
		return(filter_var($s_string_filtered,$i_type,$i_flags));
	}

}