<?php

    /**
     * Creator:      Carles Mateo
     * Date Created: 2013-02-20 11:22
     * Last Updater: Carles Mateo
     * Last Updated: 2014-01-07 13:31
     * Filename:     commonrequests.class.php
     * Description:  Space for the custom developments
     */

namespace CataloniaFramework;

abstract class CommonRequests
{

    // Define here the user vars that are to be defined commonly for all the requests
    // for example the footer
    // At this point you have available USER_LANGUAGE constant
    public static function registerUserVars($o_db = null) {
        Views::addUserVar('APP_TITLE', 'DemoApp with Catalonia Framework', Views::VAR_ACTION_REPLACE);
        Views::addUserVar('APP_TITLE_WITH_LINK', 'DemoApp with <a href="http://www.cataloniaframework.com" target="_blank">Catalonia Framework</a>', Views::VAR_ACTION_REPLACE);
        Views::addUserVar('HEAD_TITLE_BLOCK', '<div class="header-body">
                                                    <div class="in">
                                                        <h1 id="site-name">'.
                                                            Views::getUserVar('APP_TITLE_WITH_LINK')
                                                       .'</h1>
                                                    </div>
                                               </div>', Views::VAR_ACTION_REPLACE);
        Views::addUserVar('HEAD_NAVIGATION_BLOCK', '<div id="navigation">
                                                        <div class="in">
                                                            <nav id="site-nav" class="clearfix">
                                                                ||*||[NAV_SECTIONS]||*||
                                                                <div id="flags_lang" class="search" name="flags_lang">
                                                                    Login or Register
                                                                    <a href="/ca/"><img border="0" alt="Català" src="/img/flag_cat.png" /></a>
                                                                    <a href="/en/"><img border="0" alt="English" src="/img/flag_usa.png" /></a>
                                                                </div>
                                                            </nav>
                                                        </div>
                                                    </div>');

        $o_menu_navigation = new Menu();
        $o_menu_navigation->addMenuItemFromSection('index', 'Home', t('Home'));
        $o_menu_navigation->addMenuItemFromSection('about', 'About', t('About us'));
        //$o_menu_navigation->addMenuItemFromSection('contact', 'Contact');
        $st_menu_links = $o_menu_navigation->getMenuItemsAsLinks();

        $s_menus = implode(' ', $st_menu_links);

        Views::addUserVar('NAV_SECTIONS', $s_menus, Views::VAR_ACTION_REPLACE);

        Views::addUserVar('FOOTER', 'Powered by Catalonia Framework', Views::VAR_ACTION_REPLACE);
    }

    public static function registerURLS($o_db = null) {
        // By default the framework shows a file if exists
        // But you can define custom urls that will be processed at your will

        // Add here your custom URLs not processed by MVC pattern
        Navigation::addURL('this-is-a-sample/humans.txt', WEB_ROOT.'humans.txt', Navigation::ACTION_REQUIRE_FILE,
                            ControllerBase::RESPONSE_TEXT, ControllerBase::CACHE_NO_CACHE);
        Navigation::addURL('this-is-a-sample/robots.txt', WEB_ROOT.'robots.txt', Navigation::ACTION_REQUIRE_FILE,
                            ControllerBase::RESPONSE_TEXT, ControllerBase::CACHE_NO_CACHE);

    }

    public static function registerSections($o_db = null) {
        // Here can register your own sections

        if (MULTILANG==true) {
            $s_prefix = '/'.USER_LANGUAGE.'/';
        } else {
            $s_prefix = '/';
        }

        Translations::loadTranslations('common_menu');

        Section::registerSection('about', $s_prefix.t('seo_url_about_us'), 'About', 'Index');
        //Section::registerSection('contact', $s_prefix.'contact');
        //Section::registerSection('donate', $s_prefix.'donate');
        Section::registerSection('manual', $s_prefix.'manual', 'Manual', 'Index');
    }

    // Use this method to log to the database the request
    public static function logRequest($o_db = null) {


    }

    // Executed from ending with Core::End
    public static function endRequest($o_db = null) {

    }

}
