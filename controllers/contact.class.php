<?php

/**
 * Creator:      Carles Mateo
 * Date Created: 2013-02-11 00:05
 * Last Updater:
 * Last Updated:
 * Filename:     index.php
 * Description:
 */

namespace CataloniaFramework;

class Contact extends ControllerBase {

    // Cache the controller
    public $b_cache_controller = false;
    // Cache for 1 hour
    public $i_cache_TTL_seconds = 3600;

    public function actionIndex($s_url = '', $st_params = Array(), $st_params_url = Array(), $o_db = null) {

        // Load multi-language
        Translations::loadTranslations('contact', USER_LANGUAGE);

        $st_parameters = Array();

        $o_contact_form = new Form();
        $o_contact_form->addTextToForm('first_name', t('Name'), Form::HELP_FIELD_NOT_HELP, 15, Form::DATATYPE_STRING, Form::HTML_NO_CLASS, Form::HTML_NO_CLASS,
                                        Form::IS_REQUIRED, 3, 50, Form::REGEXP_EMPTY, Array(), Form::IS_NOT_READONLY, Form::IS_NOT_DISABLED, '', '', '');
        $o_contact_form->addTextToForm('surname', t('Surname'), Form::HELP_FIELD_NOT_HELP, 15, Form::DATATYPE_STRING, Form::HTML_NO_CLASS, Form::HTML_NO_CLASS,
                                        Form::IS_NOT_REQUIRED, 3, 50, Form::REGEXP_EMPTY, Array(), Form::IS_NOT_READONLY, Form::IS_NOT_DISABLED, '', '', '');

        $s_error_msg = '';

        $o_contact_form->checkParams($_POST);
        if (Requests::isPostRequest()==true) {
            if (!$o_contact_form->mayContinue()) {
                $s_error_msg = 'Error';
            }
        }


        $st_parameters['o_contact_form'] = $o_contact_form;
        $st_parameters['s_error_msg'] = $s_error_msg;

        $s_view = $this->getView('contact_index', $st_parameters, USER_LANGUAGE);

        return $s_view;
    }

}
