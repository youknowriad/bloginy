<p dir="rtl">
<?php
error_reporting(E_STRICT);
include('../Arabic.php');

$normalise = new Arabic('ArNormalise');
$file = fopen('Normalization/sample.txt', 'r');

while($read = fgets($file)) {
    print "$read<br />";
    $read = $normalise->unshape($read);
    print "$read<br />";
#    $read = $normalise->ArNormalise->stripTashkeel($read);
#    print $read . "\n";
#    $read = $normalise->ArNormalise->stripTatweel($read);
#    print $read . "\n";
#    $read = $normalise->ArNormalise->normaliseHamza($read);
#    print $read . "\n";
#    $read = $normalise->ArNormalise->normaliseLamaleph($read);
#    print $read . "\n";
    $read = $normalise->normalise($read);
     print "$read<br />";
}
fclose($file);
?>
</p>