<?php

/**
 * Creator:      Carles Mateo
 * Date Created: 2013-02-11 00:15
 * Last Updater:
 * Last Updated:
 * Filename:     catfw_index.php
 * Description:
 */

namespace CataloniaFramework;

?><html>
    <head>
    <?php require_once 'index_head.php'; ?>
    <script>
        $(document).ready(function() {
            $.ajax({
                        url:'/en/json/phpversion',
                        Type:'POST',
                        dataType: 'json',
                        success:function(data) {
                            $('#php_version').html(data['php_version'] + ' (loaded by JSon)');
                        }
                    })
        });
    </script>
    </head>
<body>
||*||[HEAD_TITLE_BLOCK]||*||
||*||[HEAD_NAVIGATION_BLOCK]||*||

This is a sample App showing what you can do very easily.<br />
Explore the source code and visit the <a href="<?php echo Section::getSectionURL('manual'); ?>" target="_blank">manual pages</a>.<br />
<br />
<strong>PHP Version:</strong> <i id="php_version">?</i>.
<br />
Total space in the volume of the cache directory : <i id="total_space_cache"></i><br />
Free space in the volume of the cache directory  : <i id="free_space_cache"></i><br />
<br />
Delete the cache files: <a onclick="javascript:$.ajax({
                                                            url:'/en/json/deletecache',
                                                            Type:'GET',
                                                            dataType: 'json',
                                                            success:function(data) {
                                                                $('#delete_result').html(data['operation_result']);
                                                            }
                                                        })" href="#">Delete</a> <i id="delete_result"></i><br />
<br />
The Operating System of the server is: <?php echo PHP_OS; ?><br />
<i id="os_version"></i><br />
<?php
    if (PHP_OS == 'Linux') {
        echo 'We like Linux :)<br />';
        echo '<script type="text/javascript">
                $.ajax({
                        url:\'/en/json/osversion\',
                        Type:\'GET\',
                        dataType: \'json\',
                        success:function(data) {
                            $(\'#os_version\').html(\'<strong>Concretely:</strong> \' + data[\'os_version\'] + \' (loaded via JSon)\');
                        }
                    });

            </script>';
    }
?>
<script type="text/javascript">
    $.ajax({
            url:'/en/json/spacecache',
            Type:'GET',
            dataType: 'json',
            success:function(data) {
                $('#total_space_cache').html(data['total_space_cache'] + ' MB (loaded via JSon)');
                $('#free_space_cache').html(data['free_space_cache'] + ' MB (loaded via JSon)');
            }
        });

</script>

<br />
<?php require 'index_footer.php'; ?>
</body>
</html>