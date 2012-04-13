<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Muslim Prayer Times</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style.css" media="all" />
</head>

<body>

<div class="Paragraph">
<h2 dir="ltr">Example Output:</h2>
<?php
    error_reporting(E_STRICT);
    $time_start = microtime(true);

    date_default_timezone_set('UTC');
    
    include('../Arabic.php');
    $Arabic = new Arabic('Salat');

    $Arabic->setLocation(33.513, 36.292, 3);
    $Arabic->setDate(date('j'), date('n'), date('Y'));

    $times = $Arabic->getPrayTime();

    echo "<b>Damascus, Syria</b> ".date('l F j, Y')."<br /><br />";
    echo "<b>Fajr:</b> {$times[0]}<br />";
    echo "<b>Sunrise:</b> {$times[1]}<br />";
    echo "<b>Zuhr:</b> {$times[2]}<br />";
    echo "<b>Asr:</b> {$times[3]}<br />";
    echo "<b>Maghrib:</b> {$times[4]}<br />";
    echo "<b>Isha:</b> {$times[5]}<br />";
?>
</div><br />
<div class="Paragraph">
<h2>Example Code:</h2>
<?php
highlight_string("<?php
    date_default_timezone_set('UTC');
    
    include('../Arabic.php');
    \$Arabic = new Arabic('Salat');

    \$Arabic->setLocation(33.513, 36.292, 3);
    \$Arabic->setDate(date('j'), date('n'), date('Y'));

    \$times = \$Arabic->getPrayTime();

    echo \"<b>Damascus, Syria</b> \".date('l F j, Y').\"<br /><br />\";
    echo \"<b>Fajr:</b> {\$times[0]}<br />\";
    echo \"<b>Sunrise:</b> {\$times[1]}<br />\";
    echo \"<b>Zuhr:</b> {\$times[2]}<br />\";
    echo \"<b>Asr:</b> {\$times[3]}<br />\";
    echo \"<b>Maghrib:</b> {\$times[4]}<br />\";
    echo \"<b>Isha:</b> {\$times[5]}<br />\";
?>");

    $time_end = microtime(true);
    $time = $time_end - $time_start;
    
    echo "<hr />Total execution time is $time seconds<br />\n";
    echo 'Amount of memory allocated to this script is ' . memory_get_usage() . ' bytes';

    $included_files = get_included_files();
    echo '<h4>Names of included or required files:</h4><ul>';
    
    foreach ($included_files as $filename) {
        echo "<li>$filename</li>";
    }

    echo '</ul>';
?>
</div>

</body>
</html>
