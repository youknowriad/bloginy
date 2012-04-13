<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Arabic Auto Summarize Class</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style.css" media="all" />

<style type="text/css">
.summary { background-color:#eeee80; }
</style>

</head>

<body>

<div class="Paragraph" dir="rtl">
<h2 dir="ltr">Example Output:</h2>
<?php
    error_reporting(E_STRICT);
    $time_start = microtime(true);

    include('../Arabic.php');
    $Arabic = new Arabic('ArAutoSummarize');
    $Arabic->setInputCharset('windows-1256');

    $dir = 'Articles/';
    
    if(!isset($_GET['file'])){
        if(!isset($_GET['rate'])){ $rate = 20; }else{ $rate = $_GET['rate']; }
    
        echo '<form action="ArAutoSummarize.php" method="get" name="setRate">
              <select name="rate" onChange="document.setRate.submit()">';
    
        for($i=10; $i<50; $i+=10){
            if($i == $rate){ $selected = 'selected'; }else{ $selected = ''; }
            echo "<option value=\"$i\" $selected>%$i من حجم الوثيقة الأصلية</option>";
        }
    
        echo '</select></form><hr>';
    
        if ($dhandle = opendir($dir)) {
            while (false !== ($file = readdir($dhandle))) {
                if ($file != "." && $file != "..") {
                    // get contents of a file into a string
                    $fhandle = fopen($dir.$file, "r");
                    $contents = fread($fhandle, filesize($dir.$file));
                    fclose($fhandle);
    
                    $summary = $Arabic->doRateSummarize($contents, $rate, $_GET['q']);
                    echo "<b><a href=\"ArAutoSummarize.php?file=$file&rate=$rate\">";
                    echo "$file</a>:</b><br />$summary<hr>";
                }
            }
            closedir($dhandle);
        }
    }else{
        $file = $_GET['file'];
        $rate = $_GET['rate'];
    
        // get contents of a file into a string
        $fhandle = fopen($dir.$file, "r");
        $contents = fread($fhandle, filesize($dir.$file));
        fclose($fhandle);
    
        $highlighted = $Arabic->highlightRateSummary($contents, $rate, $_GET['q'], 'summary');
        $metaKeywords = $Arabic->getMetaKeywords($contents, $rate);

        echo "<b>$file:</b><br />$metaKeywords<hr />$highlighted<br /><br />";
        echo "<a href=\"ArAutoSummarize.php?rate=$rate\">Back</a>";
    }
?>
</div><br />
<div class="Paragraph">
<h2>Example Code:</h2>
<?php
highlight_string("<?php
    include('../Arabic.php');
    \$Arabic = new Arabic('ArAutoSummarize');
    \$Arabic->setInputCharset('windows-1256');
    
    \$dir = 'Articles/';
    
    if(!isset(\$_GET['file'])){
        if(!isset(\$_GET['rate'])){ \$rate = 20; }else{ \$rate = \$_GET['rate']; }
    
        echo '<form action=\"ArAutoSummarize.php\" method=\"get\" name=\"setRate\">
              <select name=\"rate\" onChange=\"document.setRate.submit()\">';
    
        for(\$i=10; \$i<50; \$i+=10){
            if(\$i == \$rate){ \$selected = 'selected'; }else{ \$selected = ''; }
            echo \"<option value=\"\$i\" \$selected>%\$i من حجم الوثيقة الأصلية</option>\";
        }
    
        echo '</select></form><hr>';
    
        if (\$dhandle = opendir(\$dir)) {
            while (false !== (\$file = readdir(\$dhandle))) {
                if (\$file != \".\" && \$file != \"..\") {
                    // get contents of a file into a string
                    \$fhandle = fopen(\$dir.\$file, \"r\");
                    \$contents = fread(\$fhandle, filesize(\$dir.\$file));
                    fclose(\$fhandle);
    
                    \$summary = \$Arabic->doRateSummarize(\$contents, \$rate, \$_GET['q']);
                    echo \"<b><a href=\"ArAutoSummarize.php?file=\$file&rate=\$rate\">\";
                    echo \"\$file</a>:</b><br />\$summary<hr>\";
                }
            }
            closedir(\$dhandle);
        }
    }else{
        \$file = \$_GET['file'];
        \$rate = \$_GET['rate'];
    
        // get contents of a file into a string
        \$fhandle = fopen(\$dir.\$file, \"r\");
        \$contents = fread(\$fhandle, filesize(\$dir.\$file));
        fclose(\$fhandle);
    
        \$highlighted = \$Arabic->highlightRateSummary(\$contents, \$rate, \$_GET['q'], 'summary');
        \$metaKeywords = \$Arabic->getMetaKeywords(\$contents, \$rate);
        
        echo \"<b>\$file:</b><br />\$metaKeywords<hr />\$highlighted<br /><br />\";
        echo \"<a href=\"ArAutoSummarize.php?rate=\$rate\">Back</a>\";
    }
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
