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
 * Class Name: Arabic Text ArStemmer Class
 *  
 * Filename: ArStemmer.class.php
 *  
 * Original  Author(s): Khaled Al-Sham'aa <khaled.alshamaa@gmail.com>
 *  
 * Purpose:  Get stem of an Arabic word
 *  
 * ----------------------------------------------------------------------
 *  
 * Source: http://arabtechies.net/node/83
 * By: Taha Zerrouki <taha.zerrouki@gmail.com>
 *  
 * ----------------------------------------------------------------------
 *  
 * Arabic Word Stemmer Class
 *
 * PHP class to get stem of an Arabic word
 *
 * Example:
 * <code>
 *     include('./Arabic.php');
 *     $Arabic = new Arabic('ArStemmer');
 * 
 *     echo $Arabic->stem($word);
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
// namespace Arabic/ArStemmer;

/**
 * This PHP class get stem of an Arabic word
 *  
 * @category  Text 
 * @package   Arabic
 * @author    Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
 * @copyright 2009 Khaled Al-Shamaa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */ 
class ArStemmer
{
    private $_verb_pre = 'æÃÓÝáí';
    private $_verb_post = 'æãßÇäíå';
    private $_verb_may;

    private $_verb_max_pre = 4;
    private $_verb_max_post = 6;
    private $_verb_min_stem = 2;

    private $_noun_pre = 'ÇÈÝßáæÃ';
    private $_noun_post = 'ÇÊÉßãäåæí';
    private $_noun_may;

    private $_noun_max_pre = 4;
    private $_noun_max_post = 6;
    private $_noun_min_stem = 2;
    
    /**
     * Loads initialize values
     */         
    public function __construct()
    {
        $this->_verb_may = $this->_verb_pre . $this->_verb_post;
        $this->_noun_may = $this->_noun_pre . $this->_noun_post;
    }

    /**
     * Get rough stem of the given Arabic word 
     *      
     * @param string $word          Arabic word you would like to get its stem
     * @param string $inputCharset  (optional) Input charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set input charset)       
     * @param string $outputCharset (optional) Output charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set output charset)       
     * @param object $main          Main Ar-PHP object to access charset converter options
     *                    
     * @return string Arabic stem of the word
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function stem($word, $inputCharset = null, $outputCharset = null, $main = null)
    {
        if ($main) {
            if ($inputCharset == null) $inputCharset = $main->getInputCharset();
            $word = $main->coreConvert($word, $inputCharset, 'windows-1256');
        }

        $nounStem = $this->_stem($word, $this->_noun_may, $this->_noun_pre, $this->_noun_post,  
                              $this->_noun_max_pre, $this->_noun_max_post, $this->_noun_min_stem);

        $verbStem = $this->_stem($word, $this->_verb_may, $this->_verb_pre, $this->_verb_post,  
                              $this->_verb_max_pre, $this->_verb_max_post, $this->_verb_min_stem);
        
        if (strlen($nounStem) < strlen($verbStem)) {
            $stem = $nounStem;
        } else {
            $stem = $verbStem;
        }
        
        if ($main) {
            if ($outputCharset == null) $outputCharset = $main->getOutputCharset();
            $stem = $main->coreConvert($stem, 'windows-1256', $outputCharset);
        }

        return $stem;
    }
    
    /**
     * Get rough stem of the given Arabic word (under specific rules)
     *      
     * @param string  $word      Arabic word you would like to get its stem
     * @param string  $notChars  Arabic chars those can't be in postfix or prefix
     * @param string  $preChars  Arabic chars those may exists in the prefix
     * @param string  $postChars Arabic chars those may exists in the postfix
     * @param integer $maxPre    Max prefix length
     * @param integer $maxPost   Max postfix length
     * @param integer $minStem   Min stem length
     *
     * @return string Arabic stem of the word under giving rules
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _stem ($word, $notChars, $preChars, $postChars, $maxPre, $maxPost, $minStem)
    {
        $right = -1;
        $left  = -1;
        $max   = strlen($word);
        
        for ($i=0; $i < $max; $i++) {
            if (strpos($notChars, $word[$i]) === false) {
                if ($right == -1) $right = $i;
                $left  = $i;
            }
        }
        
        if ($right > $maxPre) $right = $maxPre;
        if ($max - $left - 1 > $maxPost) $left = $max - $maxPost -1;
        
        for ($i=0; $i < $right; $i++) {
            if (strpos($preChars, $word[$i]) === false) {
                $right = $i;
                break;
            }
        }
        
        for ($i=$max-1; $i>$left; $i--) {
            if (strpos($postChars, $word[$i]) === false) {
                $left = $i;
                break;
            }
        }

        if ($left - $right >= $minStem) {
            $stem = substr($word, $right, $left-$right+1);
        } else {
            $stem = null;
        }

        return $stem;
    }
}
?>
