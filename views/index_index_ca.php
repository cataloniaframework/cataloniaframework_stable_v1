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
                        url:'/ca/json/phpversion',
                        Type:'POST',
                        dataType: 'json',
                        success:function(data) {
                            $('#php_version').html(data['php_version'] + ' (carregat amb JSon)');
                        }
                    })
        });
    </script>
    </head>
<body>
||*||[HEAD_TITLE_BLOCK]||*||
||*||[HEAD_NAVIGATION_BLOCK]||*||

Això és un exemple que mostra el que pots fer molt fàcilment.<br />
Explora el codi font i visita els <a href="<?php echo Section::getSectionURL('manual'); ?>" target="_blank">manuals</a>.<br />
<br />
<strong>PHP Version:</strong> <i id="php_version">?</i>.<br />
<br />
Espai total al volum del directori de cache : <i id="total_space_cache"></i><br />
Espai lliure al volum del directori de cache: <i id="free_space_cache"></i><br />
<br />
Esborra els arxius de cache: <a onclick="javascript:$.ajax({
                                                            url:'/ca/json/deletecache',
                                                            Type:'POST',
                                                            dataType: 'json',
                                                            success:function(data) {
                                                                $('#delete_result').html(data['operation_result']);
                                                            }
                                                        });" href="#">Esborrar</a> <i id="delete_result"></i><br />
<br />
El sistema operatiu del servidor és: <?php echo PHP_OS; ?><br />
<i id="os_version"></i><br />
<?php
    if (PHP_OS == 'Linux') {
        echo 'Ens agrada Linux :)<br />';
        echo '<script type="text/javascript">
                $.ajax({
                        url:\'/ca/json/osversion\',
                        Type:\'GET\',
                        dataType: \'json\',
                        success:function(data) {
                            $(\'#os_version\').html(\'<strong>Concretament:</strong> \' + data[\'os_version\'] + \' (carregat amb JSon)\');
                        }
                    });

            </script>';
    }
?>
<script type="text/javascript">
    $.ajax({
            url:'/ca/json/spacecache',
            Type:'GET',
            dataType: 'json',
            success:function(data) {
                $('#total_space_cache').html(data['total_space_cache'] + ' MB (carregat amb JSon)');
                $('#free_space_cache').html(data['free_space_cache'] + ' MB (carregat amb JSon)');
            }
        });

</script>

<br />
<?php require 'index_footer.php'; ?>
</body>
</html>