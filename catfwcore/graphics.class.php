<?php

/**
 * Creator:      Carles Mateo
 * Date Created: 2014-02-01 20:02
 * Last Updater:
 * Last Updated:
 * Filename:     graphics.class.php
 * Description:
 */

namespace CataloniaFramework;

use CataloniaFramework\Section as Section;

abstract class Graphics
{

    public static function createImageStats($i_width, $i_height, $st_values, $s_title = '') {
        if (function_exists('imagecreate')) {
            // The GD library is installed
            // Create the image
            $o_my_img = imagecreate( $i_width, $i_height );

            $i_background = imagecolorallocate( $o_my_img, 255, 255, 255 );
            $i_text_colour = imagecolorallocate( $o_my_img, 0, 0, 0 );
            $i_line_colour = imagecolorallocate( $o_my_img, 100, 100, 100 );
            $i_line_colour_top = imagecolorallocate( $o_my_img, 100, 100, 255 );

            imagesetthickness ( $o_my_img, 4 );
            imageline( $o_my_img, 0, 25, $i_width, 25, $i_line_colour_top );

            $i_start_x = 10;
            $i_start_y = 35;

            $i_margin_down_y = 5;
            $i_margin_right_x = 5;

            $i_line_width = 1;
            $i_color_points = imagecolorallocate( $o_my_img, 128, 128, 128 );

            $i_width_draw = $i_width - $i_start_x - $i_margin_right_x - $i_line_width;
            $i_height_draw = $i_height - $i_start_y - $i_margin_down_y - $i_line_width;

            imagesetthickness ( $o_my_img, 2 );

            // Vertical line
            imageline($o_my_img, $i_start_x, $i_height - $i_margin_down_y, $i_width - $i_margin_right_x, $i_height - $i_margin_down_y, $i_line_colour);
            // Horizontal line
            imageline($o_my_img, $i_start_x, $i_start_y, $i_start_x, $i_height - $i_margin_down_y, $i_line_colour);

            $i_count_values = count($st_values);

            $i_pixels_per_value = $i_width_draw / $i_count_values;
            $i_pixels_per_value_y = $i_height_draw / 100;   // 100%

            $i_pixel_counter = 0;
            foreach($st_values as $s_index=>$st_value) {
                $i_cpu_used = $st_value['vvalue'];

                imageline($o_my_img, $i_start_x + 1 + ($i_pixel_counter*$i_pixels_per_value), $i_height - $i_margin_down_y - 1 - (floatval($i_cpu_used) * $i_pixels_per_value_y) -1, $i_start_x + 1 + ($i_pixel_counter*$i_pixels_per_value), $i_height - $i_margin_down_y - 1 - (floatval($i_cpu_used) * $i_pixels_per_value_y), $i_color_points);
                $i_pixel_counter++;
            }

            if ($s_title != '') {
                imagestring( $o_my_img, 4, 2, 2, $s_title, $i_text_colour );
            } else {
                imagestring( $o_my_img, 4, 2, 2, "cataloniaframework.com", $i_text_colour );
            }


            ob_start();
            imagepng( $o_my_img );
            $s_html = ob_get_clean();

            imagecolordeallocate( $o_my_img, $i_line_colour );
            imagecolordeallocate( $o_my_img, $i_text_colour );
            imagecolordeallocate( $o_my_img, $i_background );
            imagedestroy( $o_my_img );

        } else {
            // Image with message: Note install GD
            $s_image = "iVBORw0KGgoAAAANSUhEUgAAALQAAABGAgMAAAAY+VazAAAACVBMVEUAAP///wCA/wBN5ClCAAAACXBIWXMAAA7EAAAOxAGVKw4bAAABN0lEQVRIie2UO27DMAyGSUDeOTj3oQB312Dep+fpKcOXHL8SdOnQxL/BWBI/SxQD/ACfI9Joy+i2TeIZTcuI+SWNstAm3mXXE9GHIvCm9QiwvmX4AinAuoYYlK7olxWY0KOy/wpyhYkiKtcS1ERs5wxMFvGdjtjWFhqQjMZON2XaQjenm5KSdFvT+KgkRhy5CiXosqYrMlkkjU6j05PTCGOvW2878wBz1VvbDXVmlcyaE+XEzx6tJ4V2rb309/r5vb7/L33pbYRmwOnG7szd+839BecdzQ965fgE7pUVj3TumXR6fzorb2l1MHf70Rjz+fT+J3Q6a9Dcvf8JXTLvlZiXgnu/00M9p6XTXlfrex9ojL2h05tKzmk9f+yVhPc7jYcO2tlD9AQlZ+r93se57v6dS5cufZTutfnNQtAj5yQAAAAASUVORK5CYII=";
            $s_html = base64_decode($s_image);

        }

        return $s_html;

    }

}
