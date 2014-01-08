<?php

namespace CataloniaFramework;

$s_new_install_url = 'http://www.cataloniaframework.com/en/newinstallation/statsnewinstallations/os/'.php_uname().'/php/'.phpversion().'/cfw_ver/'.FRAMEWORK_VERSION;

?><html>
<head><title>Welcome to Catalonia Framework</title></head>
<body>
<h1>It works!</h1>
This is your first time with the Framework, so let's setup it.<br />
<br />
Now you must go to config/general.php and:
<ul>
    <li>Change the line <strong>define('FIRST_TIME', true);</strong> and set 'FIRST_TIME' to false</li>
    <li>Change the TimeZone, if you want</li>
    <li>Configure your paths at least for <strong>config/development.php</strong></li>
    <li>Configure your connection info at least for <strong>config/development_db.php</strong> if you want to use the Database</li>
    <li>Ensure cache/ dir has permissions (777) and cache/. has read and write (666)</li>
</ul>
<img src="<?php echo $s_new_install_url ?>" /><br />
<a href="<?php echo CATALONIAFW_URL; ?>" target="_blank">Catalonia Framework</a>
</body>
</html>