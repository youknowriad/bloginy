<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Spell numbers in the Arabic idiom</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style.css" media="all" />
</head>

<body>

<div class="Paragraph" dir="rtl">
<h2 dir="ltr">Example Output 1: المعدود مذكر مرفوع</h2>
<?php
    error_reporting(E_STRICT);
    $time_start = microtime(true);

    include('../Arabic.php');
    $Arabic = new Arabic('ArNumbers');

    $Arabic->setFeminine(1);
    $Arabic->setFormat(1);
    
    $integer = 2147483647;
    
    $text = $Arabic->int2str($integer);
    
    echo "<center>$integer<br />$text</center>";
?>

</div><br />
<div class="Paragraph">
<h2>Example Code 1:</h2>
<?php
highlight_string("<?php
    include('../Arabic.php');
    \$Arabic = new Arabic('ArNumbers');
    
    \$Arabic->setFeminine(1);
    \$Arabic->setFormat(1);
    
    \$integer = 2147483647;
    
    \$text = \$Arabic->int2str(\$integer);
    
    echo \"<center>\$integer<br />\$text</center>\";
?>");
?>
</div>
<br />
<div class="Paragraph" dir="rtl">
<h2 dir="ltr">Example Output 2: المعدود مؤنث منصوب أو مجرور</h2>
<?php
    $Arabic->setFeminine(2);
    $Arabic->setFormat(2);
    
    $integer = 2147483647;
    
    $text = $Arabic->int2str($integer);
    
    echo "<center>$integer<br />$text</center>";
?>

</div><br />
<div class="Paragraph">
<h2>Example Code 2:</h2>
<?php
highlight_string("<?php
    include('../Arabic.class.php');
    \$Arabic = new Arabic('ArNumbers');
    
    \$Arabic->setFeminine(2);
    \$Arabic->setFormat(2);
    
    \$integer = 2147483647;
    
    \$text = \$Arabic->int2str(\$integer);
    
    echo \"<center>\$integer<br />\$text</center>\";
?>");
?>
</div><br />

<div class="Paragraph" dir="rtl">
<h2 dir="ltr">Example Output 3: المعدود مؤنث منصوب أو مجرور بفاصلة</h2>
<?php
    $Arabic->setFeminine(2);
    $Arabic->setFormat(2);
    
    $integer = '2749.317';
    
    $text = $Arabic->int2str($integer);
    
    echo "<center>$integer<br />$text</center>";
?>

</div><br />
<div class="Paragraph">
<h2>Example Code 3:</h2>
<?php
highlight_string("<?php
    include('../Arabic.class.php');
    \$Arabic = new Arabic('ArNumbers');
    
    \$Arabic->setFeminine(2);
    \$Arabic->setFormat(2);
    
    \$integer = '2749.317';
    
    \$text = \$Arabic->int2str(\$integer);
    
    echo \"<center>\$integer<br />\$text</center>\";
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
