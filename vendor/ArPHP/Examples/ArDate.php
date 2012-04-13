<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Arabic/Islamic Date and Calendar</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style.css" media="all" />
</head>

<body>

<div class="Paragraph" dir="rtl">
<h2 dir="ltr">Example Output:</h2>
<?php
    error_reporting(E_STRICT);
    $time_start = microtime(true);

    date_default_timezone_set('UTC');
    $time = time();

    echo date('l dS F Y h:i:s A', $time);
    echo '<br /><br />';

    include('../Arabic.php');
    $Arabic = new Arabic('ArDate');

    echo $Arabic->date('l dS F Y h:i:s A', $time);
    echo '<br /><br />';

    $Arabic->setMode(2);
    echo $Arabic->date('l dS F Y h:i:s A', $time);
    echo '<br /><br />';
    
    $Arabic->setMode(3);
    echo $Arabic->date('l dS F Y h:i:s A', $time);
    echo '<br /><br />';

    $Arabic->setMode(4);
    echo $Arabic->date('l dS F Y h:i:s A', $time);
    echo '<br /><br />';

    $Arabic->setMode(5);
    echo $Arabic->date('l dS F Y h:i:s A', $time);
?>
</div><br />
<div class="Paragraph">
<h2>Example Code:</h2>
<?php
highlight_string("<?php
    date_default_timezone_set('UTC');
    \$time = time();
    
    echo date('l dS F Y h:i:s A', \$time);
    echo '<br /><br />';

    include('../Arabic.php');
    \$Arabic = new Arabic('ArDate');

    echo \$Arabic->date('l dS F Y h:i:s A', \$time);
    echo '<br /><br />';
    
    \$Arabic->setMode(2);
    echo \$Arabic->date('l dS F Y h:i:s A', \$time);
    echo '<br /><br />';
    
    \$Arabic->setMode(3);
    echo \$Arabic->date('l dS F Y h:i:s A', \$time);
    echo '<br /><br />';
    
    \$Arabic->setMode(4);
    echo \$Arabic->date('l dS F Y h:i:s A', \$time);
    echo '<br /><br />';
    
    \$Arabic->setMode(5);
    echo \$Arabic->date('l dS F Y h:i:s A', \$time);
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