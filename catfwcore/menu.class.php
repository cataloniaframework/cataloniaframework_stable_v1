<?php

/**
 * Creator:      Carles Mateo
 * Date Created: 2013-02-23 20:00
 * Last Updater: Carles Mateo
 * Last Updated: 2013-09-15 18:05
 * Filename:     menu.class.php
 * Description:
 */

namespace CataloniaFramework;

use CataloniaFramework\Section as Section;

class Menu
{

    // This saves the different menus
    // TODO: complete
    public static $st_MENUS = Array();

    public $st_MENU = Array();

    public function __construct() {
        Translations::loadTranslations('common_menu');
    }

    public function addMenuItemFromSection($s_section_name, $s_menu_item_name = null, $s_menu_visible_text = '') {
        if ($s_section_name == '') return;



        $st_section_info = Section::getSectionInfoByName($s_section_name);

        if ($st_section_info === null) {
            return;
        }

        if ($s_menu_item_name === null) {
            $s_menu_item_name = $st_section_info['section_name'];
        }

        $this->st_MENU[$s_menu_item_name] = Array(  'menu_name'         => $s_menu_item_name,
                                                    'path'              => $st_section_info['path'],
                                                    'visible_text'      => $s_menu_visible_text /*,
                                                    'controller'        => $st_section_info['controller'],
                                                    'action'            => $st_section_info['action']*/);

    }

    public function getMenuItems() {

        return $this->st_MENU;

    }

    public function getMenuItemsAsLinks($s_class_name = '') {

        $st_menu_result = Array();

        foreach($this->st_MENU as $s_menu_item_name=>$st_menu_item_info) {
            $s_link = '<a href="'.$st_menu_item_info['path'].'"';
            if ($s_class_name != '') {
                $s_link .= ' class="'.$s_class_name.'"';
            }
            $s_link .= '>'.$st_menu_item_info['visible_text'].'</a>';

            $st_menu_result[$s_menu_item_name] = $s_link;
        }

        return $st_menu_result;

    }

}
