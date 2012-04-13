<?php
    error_reporting(E_STRICT);
    header ("Content-type: model/vrml");
?>
#VRML V2.0 utf8
# The VRML 2.0 Sourcebook
# Copyright [1997] By
# Andrea L. Ames, David R. Nadeau, and John L. Moreland
Group {
	children [
		Viewpoint {
			description "Forward view"
			position 0.0 1.6 5.0
		},
		NavigationInfo {
			type "WALK"
			speed 1.0
			headlight FALSE
			avatarSize [ 0.5, 1.6, 0.5 ]
		},
		Inline { url "VRML/dungeon.wrl" }
	]
}

<?php
    include('../Arabic.php');
    $Arabic = new Arabic('ArGlyphs');
    $text = "خَالِد الشَّمْعَة";
    $text = $Arabic->utf8Glyphs($text);
?>

Shape
        {
        geometry Text
                {string "<?= $text; ?>"}
        }
