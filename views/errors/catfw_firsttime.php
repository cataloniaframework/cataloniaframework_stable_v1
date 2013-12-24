<?php

namespace CataloniaFramework;

?><html>
<head><title>Welcome to Catalonia Framework</title></head>
<body>
<h1>It works!</h1>
Now you must go to config/general.php and:
<ul>
    <li>Change the line <strong>define('FIRST_TIME', true);</strong> and set 'FIRST_TIME' to false</li>
    <li>Change the TimeZone</li>
    <li>Configure your paths at least for <strong>config/development.php</strong></li>
    <li>Configure your paths at least for <strong>config/development_db.php</strong></li>
    <li>Ensure cache/ dir has permissions (777) and cache/. has read and write (666)</li>
</ul>
<a href="<?php echo CATALONIAFW_URL; ?>" target="_blank">Catalonia Framework</a>
</body>
</html>