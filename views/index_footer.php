<?php

/**
 * Creator:      Carles Mateo
 * Date Created: 2013-02-23 19:50
 * Last Updater:
 * Last Updated:
 * Filename:     index_footer.php
 * Description:
 */

namespace CataloniaFramework;

// This is a common section and so translations are not loaded in the controller normal load
Translations::loadTranslations('common_footer', USER_LANGUAGE);

?><div id="footer"><small>||*||[FOOTER]||*|| V. ||*||[CATFW_FRAMEWORK_VERSION]||*|| <?php echo t('Time'); ?>: ||*||[CATFW_DATETIME]||*|| <?php echo t('Vars used in these templates'); ?>: ||*||[CATFW_STATS_VARS_NUMBER_USED]||*|| <?php echo t('Total Var replacements'); ?>: ||*||[CATFW_STATS_VARS_REPLACEMENT]||*|| <? echo t('Execution time'); ?>: ||*||[CATFW_EXECUTION_TIME]||*|| <?php echo t('seconds'); ?> <?php echo t('Cached controller'); ?>: ||*||[CATFW_STATS_CACHED_CONTROLLER]||*||</small></div>