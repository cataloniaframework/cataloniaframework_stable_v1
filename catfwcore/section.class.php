<?php
/**
 * User:        Carles Mateo
 * Date:        2013-02-08
 * Time:        21:38
 * Filename:    section.class.php
 * Description: To define Sections and Menus and call later by the name
 */

namespace CataloniaFramework;

abstract class Section
{

    public static $st_sections = Array();

    public static function registerSection($s_section_name, $s_path, $s_controller = null, $s_action = 'Index') {
    //public static function registerSection($s_section_name, $s_path) {

        if ($s_controller === null) {
            $s_controller = $s_section_name;
        }

        // Register a section so later could be
        self::$st_sections[$s_section_name] = Array('section_name'      => $s_section_name,
                                                    'path'              => $s_path,
                                                    'controller'        => $s_controller,
                                                    'action'            => $s_action);

    }

    public static function getSectionURL($s_section_name, $b_force_bar_right = true) {
        // Return URL for the section, or error

        if (isset(self::$st_sections[$s_section_name]) &&
            isset(self::$st_sections[$s_section_name]['path']))
        {
            return self::$st_sections[$s_section_name]['path'].(($b_force_bar_right == true && substr(self::$st_sections[$s_section_name]['path'], -1, 1) != '/')? '/' : '');
        }

        return null;

    }

    public static function getSectionInfoByName($s_section_name) {
        if (isset(self::$st_sections[$s_section_name])) {
            return self::$st_sections[$s_section_name];
        }

        return null;

    }

    public static function getSectionInfoByPath($s_path) {
        foreach(self::$st_sections as $s_key=>$st_section_info) {
            if ($st_section_info['path'] == $s_path) {
                return $st_section_info;
            }
        }
    }

    public static function getAllSections() {
        return self::$st_sections;
    }
}
