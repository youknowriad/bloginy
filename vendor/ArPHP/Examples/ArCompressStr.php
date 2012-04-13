<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Arabic Text Compressor</title>
<meta http-equiv="Content-Type" content="text/html;charset=windows-1256" />
<link rel="stylesheet" type="text/css" href="style.css" media="all" />
</head>

<body>

<div class="Paragraph">
<h2 dir="ltr">Example Output:</h2>

<?php
error_reporting(E_STRICT);
$time_start = microtime(true);

include('../Arabic.php');
$obj = new Arabic('ArCompressStr');

$obj->setInputCharset('windows-1256');
$obj->setOutputCharset('windows-1256');

$file = 'Compress/ar_example.txt';
$fh = fopen($file, 'r');
$str = fread($fh, filesize($file));
fclose($fh);

$zip = $obj->compress($str);

$before = strlen($str);
$after = strlen($zip);
$rate = round($after * 100 / $before);

echo "String size before was: $before Byte<br>";
echo "Compressed string size after is: $after Byte<br>";
echo "Rate $rate %<hr>";

$str = $obj->decompress($zip);

$word = 'الدول';
if ($obj->search($zip, $word)) {
    echo "Search for $word in zipped string and find it<hr>";
} else {
    echo "Search for $word in zipped string and do not find it<hr>";
}

$len = ArCompressStr::length($zip);
echo "Original length of zipped string is $len Byte<hr>";

echo '<div dir="rtl" align="justify">'.nl2br($str).'</div>';
?>
</div><br />
<div class="Paragraph">
<h2>Example Code:</h2>
<?php

    $code = <<<END
<?php
include('../Arabic.php');
\$obj = new Arabic('ArCompressStr');

\$obj->setInputCharset('windows-1256');
\$obj->setOutputCharset('windows-1256');

\$file = 'Compress/ar_example.txt';
\$fh = fopen(\$file, 'r');
\$str = fread(\$fh, filesize(\$file));
fclose(\$fh);

\$zip = \$obj->compress(\$str);

\$before = strlen(\$str);
\$after = strlen(\$zip);
\$rate = round(\$after * 100 / \$before);

echo "String size before was: \$before Byte<br>";
echo "Compressed string size after is: \$after Byte<br>";
echo "Rate \$rate %<hr>";

\$str = \$obj->decompress(\$zip);

\$word = 'الدول';
if (\$obj->search(\$zip, \$word)) {
    echo "Search for \$word in zipped string and find it<hr>";
} else {
    echo "Search for \$word in zipped string and do not find it<hr>";
}

\$len = \ArCompressStr::length(\$zip);
echo "Original length of zipped string is \$len Byte<hr>";

echo '<div dir="rtl" align="justify">'.nl2br(\$str).'</div>';
?>
END;

    highlight_string($code);
    
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
