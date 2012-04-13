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
 * Class Name: Tagging Arabic Word Class
 *  
 * Filename: ArWordTag.class.php
 *  
 * Original  Author(s): Khaled Al-Sham'aa <khaled.alshamaa@gmail.com>
 *  
 * Purpose:  Arabic grammarians describe Arabic as being derived from
 *           three main categories: noun, verb and particle. This class
 *           built to recognize the class of a given Arabic word.
 *            
 * ----------------------------------------------------------------------
 *  
 * Tagging Arabic Word
 *
 * This PHP Class can identifying names, places, dates, and other noun
 * words and phrases in Arabic language that establish the meaning of a body
 * of text.
 * 
 * This process of identifying names, places, dates, and other noun words and 
 * phrases that establish the meaning of a body of text-is critical to software 
 * systems that process large amounts of unstructured data coming from sources such 
 * as email, document files, and the Web.
 * 
 * Arabic words are classifies into three main classes, namely, verb, noun and 
 * particle. Verbs are sub classified into three subclasses (Past verbs, Present 
 * Verbs, etc.); nouns into forty six subclasses (e.g. Active participle, Passive 
 * participle, Exaggeration pattern, Adjectival noun, Adverbial noun, Infinitive 
 * noun, Common noun, Pronoun, Quantifier, etc.) and particles into twenty three 
 * subclasses (e.g. additional, resumption, Indefinite, Conditional, Conformational, 
 * Prohibition, Imperative, Optative, Reasonal, Dubious, etc.), and from these three 
 * main classes that the rest of the language is derived.
 * 
 * The most important aspect of this system of describing Arabic is that all the 
 * subclasses of these three main classes inherit properties from the parent 
 * classes.
 * 
 * Arabic is very rich in categorising words, and contains classes for almost every 
 * form of word imaginable. For example, there are classes for nouns of instruments, 
 * nouns of place and time, nouns of activity and so on. If we tried to use all the 
 * subclasses described by Arabic grammarians, the size of the tagset would soon 
 * reach more than two or three hundred tags. For this reason, we have chosen only 
 * the main classes. But because of the way all the classes inherit from others, it 
 * would be quite simple to extend this tagset to include more subclasses.      
 *
 * Example:
 * <code>
 *     include('./Arabic.php');
 *     $Ar = new Arabic('EnTransliteration');
 * 
 *     $hStr=$Ar->highlightText($str,'#80B020');
 * 
 *     echo $str . '<hr />' . $hStr . '<hr />';
 *     
 *     $taggedText = $Ar->tagText($str);
 * 
 *     foreach($taggedText as $wordTag) {
 *         list($word, $tag) = $wordTag;
 *     
 *         if ($tag == 1) {
 *             echo "<font color=#DBEC21>$word is Noun</font>, ";
 *         }
 *     
 *         if ($tag == 0) {
 *             echo "$word is not Noun, ";
 *         }
 *     }    
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
// namespace Arabic/ArWordTag;

/**
 * This PHP class to tagging Arabic Word
 *  
 * @category  Text 
 * @package   Arabic
 * @author    Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
 * @copyright 2009 Khaled Al-Shamaa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */ 
class ArWordTag
{
    private static $_particle_pre_nouns = array('⁄‰', '›Ì', '„–', '„‰–', '„‰', '«·Ï', '⁄·Ï', 'Õ Ï', '«·«', '€Ì—', '”ÊÏ', 'Œ·«', '⁄œ«', 'Õ«‘«', '·Ì”');
    private static $_normalizeAlef = array('√','≈','¬');
    private static $_normalizeDiacritics = array('Û','','ı','Ò','ˆ','Ú','˙','¯');
    
    /**
     * Check if given rabic word is noun or not
     *      
     * @param string $word         Word you want to check if it is noun (windows-1256)
     * @param string $word_befor   The word before word you want to check
     * @param string $inputCharset (optional) Input charset [utf-8|windows-1256|iso-8859-6]
     *                             default value is NULL (use set input charset)       
     * @param object $main         Main Ar-PHP object to access charset converter options
     *                    
     * @return boolean TRUE if given word is Arabic noun
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public static function isNoun($word, $word_befor, $inputCharset = null, $main = null)
    {
        $word = trim($word);
        $word_befor = trim($word_befor);

        if ($main) {
            if ($inputCharset == null) $inputCharset = $main->getInputCharset();
            $word = $main->coreConvert($word, $inputCharset, 'windows-1256');
            $word_befor = $main->coreConvert($word_befor, $inputCharset, 'windows-1256');
        }
        
        $word = str_replace(self::$_normalizeAlef, '«', $word);
        $word_befor = str_replace(self::$_normalizeAlef, '«', $word_befor);
        
        if (in_array($word_befor, self::$_particle_pre_nouns)) {
            return true;
        }
        
        if (is_numeric($word) || is_numeric($word_befor)) {
            return true;
        }
        
        if (preg_match('/(|Ú|Ò)$/', $word)) {
            return true;
        }
        
        $word = str_replace(self::$_normalizeDiacritics, '', $word);
        $wordLen = strlen($word);
        
        if (substr($word, 0, 2) == '«·' && $wordLen >= 5) {
            return true;
        }
        
        if (in_array(substr($word, -1), array('Ï','¡','…')) && $wordLen >= 4) {
            return true;
        }

        if (substr($word, -2) == '« ' && $wordLen >= 5) {
            return true;
        }

        if (preg_match('/^„\S{3}$/', $word) || preg_match('/^„\S{2}«\S$/', $word) || preg_match('/^„\S{3}…$/', $word) || preg_match('/^\S{2}«\S$/', $word) || preg_match('/^\S«\SÊ\S$/', $word) || preg_match('/^\S{2}Ê\S$/', $word) || preg_match('/^\S{2}Ì\S$/', $word) || preg_match('/^„\S{2}Ê\S$/', $word) || preg_match('/^„\S{2}Ì\S$/', $word) || preg_match('/^\S{3}…$/', $word) || preg_match('/^\S{2}«\S…$/', $word) || preg_match('/^\S«\S{2}…$/', $word) || preg_match('/^\S«\SÊ\S…$/', $word) || preg_match('/^«\S{2}Ê\S…$/', $word) || preg_match('/^«\S{2}Ì\S$/', $word) || preg_match('/^«\S{3}$/', $word) || preg_match('/^\S{3}Ï$/', $word) || preg_match('/^\S{3}«¡$/', $word) || preg_match('/^\S{3}«‰$/', $word) || preg_match('/^„\S«\S{2}$/', $word) || preg_match('/^„‰\S{3}$/', $word) || preg_match('/^„ \S{3}$/', $word) || preg_match('/^„” \S{3}$/', $word) || preg_match('/^„\S \S{2}$/', $word) || preg_match('/^„ \S«\S{2}$/', $word) || preg_match('/^\S«\S{2}$/', $word)) {
            return true;
        }

        return false;
    }
    
    /**
     * Tag all words in a given Arabic string if they are nouns or not
     *      
     * @param string $str           Arabic string you want to tag all its words
     * @param string $inputCharset  (optional) Input charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set input charset)       
     * @param string $outputCharset (optional) Output charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set output charset)       
     * @param object $main          Main Ar-PHP object to access charset converter options
     *                    
     * @return array Two dimension array where item[i][0] represent the word i
     *               in the given string, and item[i][1] is 1 if that word is
     *               noun and 0 if it is not
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public static function tagText($str, $inputCharset = null, $outputCharset = null, $main = null)
    {
        if ($main) {
            if ($inputCharset == null) $inputCharset = $main->getInputCharset();
            $str = $main->coreConvert($str, $inputCharset, 'windows-1256');
        }

        $text = array();
        $words = split(' ', $str);
        $prevWord = '';
        
        foreach ($words as $word) {
            if ($word == '') {
                continue;
            }

            if (self::isNoun($word, $prevWord)) {
                $text[] = array($word, 1);
            } else {
                $text[] = array($word, 0);
            }
            
            $prevWord = $word;
        }

        if ($main) {
            if ($outputCharset == null) $outputCharset = $main->getOutputCharset();

            $max = count($text);
            for ($i = 0; $i < $max; $i++) {
                $text[$i][0] = $main->coreConvert($text[$i][0], 'windows-1256', $outputCharset);
            }
        }
        
        return $text;
    }
    
    /**
     * Highlighted all nouns in a given Arabic string
     *      
     * @param string $str           Arabic string you want to highlighted all its nouns
     * @param string $bg            Background color of the highlight Arabic text
     * @param string $inputCharset  (optional) Input charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set input charset)       
     * @param string $outputCharset (optional) Output charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set output charset)       
     * @param object $main          Main Ar-PHP object to access charset converter options
     *                    
     * @return string Arabic string in HTML format where all nouns highlighted
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public static function highlightText($str, $bg = '#FFEEAA', $inputCharset = null, $outputCharset = null, $main = null)
    {
        if ($main) {
            if ($inputCharset == null) $inputCharset = $main->getInputCharset();
            $str = $main->coreConvert($str, $inputCharset, 'windows-1256');
        }

        $html = '';
        $prevTag = 0;
        $prevWord = '';
        
        $taggedText = self::tagText($str);
        
        foreach ($taggedText as $wordTag) {
            list($word, $tag) = $wordTag;
            
            if ($prevTag == 0 && $tag == 1) {
                $html .= " \r\n<span style=\"background-color: $bg\">";
            }
            
            if ($prevTag == 1 && in_array($word, self::$_particle_pre_nouns)) {
                $prevWord = $word;
                continue;
            }
            
            if ($prevTag == 1 && $tag == 0) {
                $html .= "</span> \r\n";
            }
            
            $html .= ' ' . $prevWord . ' ' . $word;
            
            if ($prevWord != '') {
                $prevWord = '';
            }
            $prevTag = $tag;
        }
        
        if ($prevTag == 1) {
            $html .= "</span> \r\n";
        }
        
        if ($main) {
            if ($outputCharset == null) $outputCharset = $main->getOutputCharset();
            $html = $main->coreConvert($html, 'windows-1256', $outputCharset);
        }
        
        return $html;
    }
}
?>
