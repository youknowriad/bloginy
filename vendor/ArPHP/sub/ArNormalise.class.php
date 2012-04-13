<?php


class ArNormalise {
   
    function __construct() {
        $this->path = str_replace('\\', '/',__FILE__);
        $this->path = substr($this->path, 0, strrpos($this->path, '/'));
        include($this->path . "/ArUnicode.constants.php");
        $this->unshape_map = $ligature_map;
        $this->unshape_keys = array_keys($this->unshape_map);
        $this->unshape_values = array_values($this->unshape_map);
    	$this->chars = $char_names;
    }    

    public function stripTatweel($text) {
        if ($main) {
              if ($inputCharset == null) $inputCharset = $main->getInputCharset();
              $arg = $main->coreConvert($text, $inputCharset, 'utf8');
        }


        return str_replace($this->chars['TATWEEL'], '', $text); 
    }

    function stripTashkeel($text) {
   if ($main) {
              if ($inputCharset == null) $inputCharset = $main->getInputCharset();
              $arg = $main->coreConvert($text, $inputCharset, 'utf8');
        }


        $tashkeel = array(
         $this->chars['FATHATAN'], 
	 $this->chars['DAMMATAN'], 
	 $this->chars['KASRATAN'], 
         $this->chars['FATHA'], 
	 $this->chars['DAMMA'], 
	 $this->chars['KASRA'],
         $this->chars['SUKUN'],
	 $this->chars['SHADDA']
        );
	return str_replace($tashkeel, "", $text);
    }

    public function normaliseHamza($text) {
   if ($main) {
              if ($inputCharset == null) $inputCharset = $main->getInputCharset();
              $arg = $main->coreConvert($text, $inputCharset, 'utf8');
        }


	    $replace = array(
		    $this->chars['WAW_HAMZA'] = $this->chars['WAW'],
		    $this->chars['YEH_HAMZA'] = $this->chars['YEH'],
		    );
	    $alephs = array(
                    $this->chars['ALEF_MADDA'],
		    $this->chars['ALEF_HAMZA_ABOVE'],
		    $this->chars['ALEF_HAMZA_BELOW'],
		    $this->chars['HAMZA_ABOVE,HAMZA_BELOW']
		    );
	    $text = str_replace(array_keys($replace),
		     array_values($replace),
		     $text);
	    $text = str_replace($alephs, $this->chars['ALEF'], $text);
	    return $text;
    }

    public function normaliseLamaleph ($text) {
   if ($main) {
              if ($inputCharset == null) $inputCharset = $main->getInputCharset();
              $arg = $main->coreConvert($text, $inputCharset, 'utf8');
        }


        $text = str_replace($this->chars['LAM_ALEPH'], $simple_LAM_ALEPH, $text);
        $text = str_replace($this->chars['LAM_ALEPH_HAMZA_ABOVE'], $simple_LAM_ALEPH_HAMZA_ABOVE, $text);
        $text = str_replace($this->chars['LAM_ALEPH_HAMZA_BELOW'], $simple_LAM_ALEPH_HAMZA_BELOW, $text);
        $text = str_replace($this->chars['LAM_ALEPH_MADDA_ABOVE'], $simple_LAM_ALEPH_MADDA_ABOVE, $text);
        return $text;
    }

    /**
     * Return unicode char by its code
     */
    public function unichr($u) {
        return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
    }

    public function unshapeReverse($text){
        $text = $this->unshape($text);
        $array = split(' \t', $text);
        print_r ($array);
    }

    # Needs fixing because php can't freaking iterate over 
    # unicode characters in a sring.
    # We need ICU support.
    /*function unshape_proper($text) {
        $array = preg_split('//', $text, -1, PREG_SPLIT_NO_EMPTY);
        $returned = "";
        foreach($array as $char)
             #$returned .= (defined ($this->unshape_map[$char]))
        #    ? $this->unshape_map[$char] : 
        #    $char;
             print $char . "\n";
        return $returned;
    }*/
        
    public function unshape($text, $inputCharset = null, $outputCharset = null, $main = null)
    {
   if ($main) {
              if ($inputCharset == null) $inputCharset = $main->getInputCharset();
              $arg = $main->coreConvert($text, $inputCharset, 'utf8');
        }


        return str_replace($this->unshape_keys,
            $this->unshape_values,
            $text);
    }

    /* A wrapper function */
    public function normalise($text, $inputCharset = null, $outputCharset = null, $main = null)
    {
       if ($main) {
              if ($inputCharset == null) $inputCharset = $main->getInputCharset();
              $arg = $main->coreConvert($text, $inputCharset, 'utf8');
        }

 	$text = $this->stripTashkeel($text);
    	$text = $this->stripTatweel($text);
    	$text = $this->normaliseHamza($text);
    	$text = $this->normaliseLamaleph($text);
        if ($main) {
              if ($outputCharset == null) $outputCharset = $main->getOutputCharset();
              $text = $main->coreConvert($text, 'utf8', $outputCharset);
          }
	return $text;
    } 
}

?>
