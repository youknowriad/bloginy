<?php
/**
 * ----------------------------------------------------------------------
 *  
 * Copyright (C) 2009 by Khaled Al-Shamaa.
 *  
 * http://www.ar-php.org
 *  
 * ----------------------------------------------------------------------
 *  
 * LICENSE
 *
 * This program is open source product; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License (LGPL)
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 *  
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *  
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/lgpl.txt>.
 *  
 * ----------------------------------------------------------------------
 *  
 * Class Name: Arabic Soundex
 *  
 * Filename:   ArSoundex.class.php
 *  
 * Original    Author(s): Khaled Al-Sham'aa <khaled.alshamaa@gmail.com>
 *  
 * Purpose:    Arabic soundex algorithm takes Arabic word as an input
 *             and produces a character string which identifies a set words
 *             that are (roughly) phonetically alike.
 *              
 * ----------------------------------------------------------------------
 *  
 * Arabic Soundex
 *
 * PHP class for Arabic soundex algorithm takes Arabic word as an input and
 * produces a character string which identifies a set words of those are
 * (roughly) phonetically alike.
 * 
 * Terms that are often misspelled can be a problem for database designers. Names, 
 * for example, are variable length, can have strange spellings, and they are not 
 * unique. Words can be misspelled or have multiple spellings, especially across 
 * different cultures or national sources.
 * 
 * To solve this problem, we need phonetic algorithms which can find similar 
 * sounding terms and names. Just such a family of algorithms exists and is called 
 * SoundExes, after the first patented version.
 * 
 * A Soundex search algorithm takes a word, such as a person's name, as input and 
 * produces a character string which identifies a set of words that are (roughly) 
 * phonetically alike. It is very handy for searching large databases when the user 
 * has incomplete data.
 * 
 * The original Soundex algorithm was patented by Margaret O'Dell and Robert 
 * C. Russell in 1918. The method is based on the six phonetic classifications of 
 * human speech sounds (bilabial, labiodental, dental, alveolar, velar, and glottal), 
 * which in turn are based on where you put your lips and tongue to make the sounds.
 * 
 * Soundex function that is available in PHP, but it has been limited to English and 
 * other Latin-based languages. This function described in PHP manual as the 
 * following: Soundex keys have the property that words pronounced similarly produce 
 * the same soundex key, and can thus be used to simplify searches in databases where 
 * you know the pronunciation but not the spelling. This soundex function returns 
 * string of 4 characters long, starting with a letter.
 * 
 * We develop this class as an Arabic counterpart to English Soundex, it handle an 
 * Arabic input string formatted in windows-1256 character set to return Soundex key 
 * equivalent to normal soundex function in PHP even for English and other 
 * Latin-based languages because the original algorithm focus on phonetically 
 * characters alike not the meaning of the word itself.
 * 
 * Example:
 * <code>
 * include('./Arabic.php');
 * $Arabic = new Arabic('ArSoundex');
 *     
 * $soundex = $Arabic->soundex($name);
 * </code>   
 *    
 * @category  Text 
 * @package   Arabic
 * @author    Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
 * @copyright 2009 Khaled Al-Shamaa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */

// New in PHP V5.3: Namespaces
// namespace Arabic/ArSoundex;

/**
 * This PHP class implement Arabic soundex algorithm
 *  
 * @category  Text 
 * @package   Arabic
 * @author    Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
 * @copyright 2009 Khaled Al-Shamaa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */ 
class ArSoundex
{
    private $_asoundexCode = array();
    private $_aphonixCode = array();
    private $_transliteration = array();
    private $_map = array();
    
    private $_len = 4;
    private $_lang = 'en';
    private $_code = 'soundex';
    
    /**
     * Loads initialize values
     */         
    public function __construct()
    {
        $this->_asoundexCode = array('/Ç|æ|í|Ú|Í|å/', '/È|Ý/', '/Î|Ì|Ò|Ó|Õ|Ù|Þ|ß|Û|Ô/', '/Ê|Ë|Ï|Ð|Ö|Ø|É/', '/á/', '/ã|ä/', '/Ñ/');
        
        $this->_aphonixCode = array('/Ç|æ|í|Ú|Í|å/', '/È/', '/Î|Ì|Õ|Ù|Þ|ß|Û|Ô/', '/Ê|Ë|Ï|Ð|Ö|Ø|É/', '/á/', '/ã|ä/', '/Ñ/', '/Ý/', '/Ò|Ó/');
        
        $this->_transliteration = array('Ç' => 'A', 'È' => 'B', 'Ê' => 'T', 'Ë' => 'T', 'Ì' => 'J', 'Í' => 'H', 'Î' => 'K', 'Ï' => 'D', 'Ð' => 'Z', 'Ñ' => 'R', 'Ò' => 'Z', 'Ó' => 'S', 'Ô' => 'S', 'Õ' => 'S', 'Ö' => 'D', 'Ø' => 'T', 'Ù' => 'Z', 'Ú' => 'A', 'Û' => 'G', 'Ý' => 'F', 'Þ' => 'Q', 'ß' => 'K', 'á' => 'L', 'ã' => 'M', 'ä' => 'N', 'å' => 'H', 'æ' => 'W', 'í' => 'Y');
        
        $this->_map = $this->_asoundexCode;
    }
    
    /**
     * Set the length of soundex key (default value is 4)
     *      
     * @param integer $integer Soundex key length
     *      
     * @return boolean TRUE if success, or FALSE if fail
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function setLen($integer)
    {
        $flag = true;
        
        $this->_len = (int)$integer or $flag = false;
        
        return $flag;
    }
    
    /**
     * Set the language of the soundex key (default value is "en")
     *      
     * @param string $str Soundex key language [ar|en]
     *      
     * @return boolean TRUE if success, or FALSE if fail
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function setLang($str)
    {
        $flag = true;
        
        $str = strtolower($str);
        
        if ($str == 'ar' || $str == 'en') {
            $this->_lang = $str;
        } else {
            $flag = false;
        }
        
        return $flag;
    }
    
    /**
     * Set the mapping code of the soundex key (default value is "soundex")
     *      
     * @param string $str Soundex key mapping code [soundex|phonix]
     *      
     * @return boolean TRUE if success, or FALSE if fail
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function setCode($str)
    {
        $flag = true;
        
        $str = strtolower($str);
        
        if ($str == 'soundex' || $str == 'phonix') {
            $this->_code = $str;
            if ($str == 'phonix') {
                $this->_map = $this->_aphonixCode;
            } else {
                $this->_map = $this->_asoundexCode;
            }
        } else {
            $flag = false;
        }
        
        return $flag;
    }
    
    /**
     * Get the soundex key length used now
     *      
     * @return integer return current setting for soundex key length
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function getLen()
    {
        return $this->_len;
    }
    
    /**
     * Get the soundex key language used now
     *      
     * @return string return current setting for soundex key language
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function getLang()
    {
        return $this->_lang;
    }
    
    /**
     * Get the soundex key calculation method used now
     *      
     * @return string return current setting for soundex key calculation method
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function getCode()
    {
        return $this->_code;
    }
    
    /**
     * Methode to get soundex/phonix numric code for given word
     *      
     * @param string $word The word that we want to encode it
     *      
     * @return string The calculated soundex/phonix numeric code
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _mapCode($word)
    {
        $encodedWord = $word;
        
        foreach ($this->_map as $code => $condition) {
            $encodedWord = preg_replace($condition, $code, $encodedWord);
        }
        $encodedWord = preg_replace('/\D/', '0', $encodedWord);
        
        return $encodedWord;
    }
    
    /**
     * Remove any characters replicates
     *      
     * @param string $word Arabic word you want to check if it is feminine
     *      
     * @return string Same word without any duplicate chracters
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _trimRep($word)
    {
        $lastChar = null;
        $cleanWord = null;
        $max = strlen($word);
        
        $i = 0;
        while ($i < $max) {
            if ($word[$i] != $lastChar) {
                $cleanWord .= $word[$i];
            }
            $lastChar = $word[$i];
            $i++;
        }
        
        return $cleanWord;
    }
    
    /**
     * Arabic soundex algorithm takes Arabic word as an input and produces a 
     * character string which identifies a set words that are (roughly) 
     * phonetically alike.
     *      
     * @param string $word          Arabic word you want to calculate its soundex
     * @param string $inputCharset  (optional) Input charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set input charset)       
     * @param string $outputCharset (optional) Output charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set output charset)       
     * @param object $main          Main Ar-PHP object to access charset converter options
     *                    
     * @return string Soundex value for a given Arabic word
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function soundex($word, $inputCharset = null, $outputCharset = null, $main = null)
    {
        if ($main) {
            if ($inputCharset == null) $inputCharset = $main->getInputCharset();
            $word = $main->coreConvert($word, $inputCharset, 'windows-1256');
        }

        $soundex = $word[0];
        $rest = substr($word, 1);
        
        if ($this->_lang == 'en') {
            $soundex = $this->_transliteration[$soundex];
        }
        
        $encodedRest = $this->_mapCode($rest);
        $cleanEncodedRest = $this->_trimRep($encodedRest);
        
        $soundex .= $cleanEncodedRest;
        
        $soundex = str_replace('0', '', $soundex);
        
        $totalLen = strlen($soundex);
        if ($totalLen > $this->_len) {
            $soundex = substr($soundex, 0, $this->_len);
        } else {
            $soundex .= str_repeat('0', $this->_len - $totalLen);
        }
        
        if ($main) {
            if ($outputCharset == null) $outputCharset = $main->getOutputCharset();
            $soundex = $main->coreConvert($soundex, 'windows-1256', $outputCharset);
        }
        
        return $soundex;
    }
}
?>
