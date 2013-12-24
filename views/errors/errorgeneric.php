<?php

/**
 * Creator:      Carles Mateo
 * Date Created: 2013-02-11 20:58
 * Last Updater:
 * Last Updated:
 * Filename:     errorgeneric.php
 * Description:
 */

namespace CataloniaFramework;

?><html>
    <body>
        <h1>Error</h1>
        Something bad happened. :-(<br />
        <?php

            if (ENVIRONMENT == DEVELOPMENT) {
                // Show Errors
                if (isset($i_error_code)) {
                    echo 'Error number: '.$i_error_code;
                    echo '<br />';
                }
                if (isset($s_error_msg)) {
                    echo 'Error description: '.$s_error_msg;
                    echo '<br />';
                }

            }

        ?>
    </body>
</html>