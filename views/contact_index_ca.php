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
    </head>
<body>
||*||[HEAD_TITLE_BLOCK]||*||
||*||[HEAD_NAVIGATION_BLOCK]||*||

<h1>Contactar</h1>

<p>Això és per a demostrar la funcionalitat de la classe Form</p>
<div id="error_msg">
    <?php

        // $st_view_vars is defined in getView and passed down
        $o_form = $st_view_vars['o_contact_form'];
        $s_embed_javascript = $st_view_vars['s_embed_javascript'];
        $s_error_msg = $st_view_vars['s_error_msg'];
        if (isset($st_view_vars['s_ok_msg'])) {
            $s_ok_msg = $st_view_vars['s_ok_msg'];
        } else {
            $s_ok_msg = '';
        }



        if (isset($s_error_msg) && $s_error_msg != '')
        {
            echo '<p class="general_error_msg">'.$s_error_msg.'</p><br />';
            //wd($o_api_params->getErrors());
            $s_embed_javascript = $o_form->getJavascriptToHighlightFormValidationErrors();

            /*
             document.getElementById(controlID).style.borderColor="red";
document.getElementById(controlID).style.borderStyle="solid";
             */
        }
    ?>
</div>
<div id="ok_msg" class="general_ok_msg">
    <?php
    if (isset($s_ok_msg) && $s_ok_msg != '')
    {
        echo '<p class="general_ok_msg">'.$s_ok_msg.'</p><br />';
    }
    ?>
</div>

<?php

	$s_html_rendered = '';
	foreach($o_form->getParametersAsHtmlControls() as $i_key=>$st_html_control)
	{
		if ($st_html_control['html_type'] == Form::HTML_TYPE_HIDDEN) {
			$s_html_rendered .= $st_html_control['html_code'];
			$s_html_rendered .= "\n";
		} else {
			$s_html_rendered .= '<tr>';
			if ($st_html_control['required'] == true) {
				$s_html_rendered .= '<td class="general_mandatory_field">*</td>';
			} else {
				$s_html_rendered .= '<td></td>';
			}
			$s_html_rendered .= '<td>'.$st_html_control['label'].':</td>';
			$s_html_rendered .= '<td>'.$st_html_control['html_code'].'</td>';
			$s_html_rendered .= '<td>'.$st_html_control['help_field'].'</td>';

			$s_html_rendered .= '</tr>';
			$s_html_rendered .= "\n";
		}
	}

?>
<form action="" method="POST">
<input type="hidden" name="action_id" value="REGISTER" />
<table border="0" id="form-<?php echo $o_form->getFormHtmlId(); ?>">
	<?php echo $s_html_rendered; ?>
</table>
<?php
    if (isset($s_embed_javascript) && $s_embed_javascript != '') {
        echo $s_embed_javascript;
    }

    ?>
<br />
<input type="submit" />
</form>

<?php require 'index_footer.php'; ?>
</body>
</html>