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
 * Class Name: Arabic-English Transliteration
 *  
 * Filename:   EnTransliteration.class.php
 *  
 * Original    Author(s): Khaled Al-Sham'aa <khaled.alshamaa@gmail.com>
 *  
 * Purpose:    Transliterate Arabic words into English by render them
 *             in the orthography of the English language
 *              
 * ----------------------------------------------------------------------
 *  
 * Arabic-English Transliteration
 * 
 * PHP class transliterate Arabic words into English by render them in the 
 * orthography of the English language.
 *       
 * Out of vocabulary (OOV) words are a common source of errors in cross language 
 * information retrieval. Bilingual dictionaries are often limited in their coverage 
 * of named- entities, numbers, technical terms and acronyms. There is a need to 
 * generate translations for these "on-the-fly" or at query time.
 * 
 * A significant proportion of OOV words are named entities and technical terms. 
 * Typical analyses find around 50% of OOV words to be named entities. Yet these 
 * can be the most important words in the queries. Cross language retrieval 
 * performance (average precision) reduced more than 50% when named entities in the 
 * queries were not translated.
 * 
 * When the query language and the document language share the same alphabet it may 
 * be sufficient to use the OOV word as its own translation. However, when the two 
 * languages have different alphabets, the query term must somehow be rendered in 
 * the orthography of the other language. The process of converting a word from one 
 * orthography into another is called transliteration.
 * 
 * Foreign words often occur in Arabic text as transliteration. This is the case for 
 * many categories of foreign words, not just proper names but also technical terms 
 * such as caviar, telephone and internet.
 * 
 * Example:
 * <code>
 *   include('./Arabic.php');
 *   $Ar = new Arabic('EnTransliteration');
 *     
 *   $en_term = $Ar->ar2en($ar_term);
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
// namespace Arabic/EnTransliteration;

/**
 * This PHP class transliterate Arabic words into English
 *  
 * @category  Text 
 * @package   Arabic
 * @author    Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
 * @copyright 2009 Khaled Al-Shamaa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */ 
class EnTransliteration
{
    private static $_arRegPatterns = array('/^ال/','/^إِي/','/^عِي/','/^عُو/','/ْع$/', 
                                       '/ِي$/','/َو$/','/َي$/','/^ع/','/^أ/','/^آ/'         );
    private static $_arRegReplacements = array('Al-', 'ei', 'ei', 'ou', 'a',
                                           'i', 'aw', 'ay', 'a', 'a', 'aa');
        
    private static $_arPatterns = array('ّ', 'تة', 'ته', 'كة', 'كه', 'ده', 'دة', 'ضه', 'ضة', 'سه', 'سة', 'صه', 'صة',
                                    'َا', 'َي', 'ُو', 'ِي', 'ً', 'ٌ', 'ٍ', 'َ', 'ِ', 'ُ', 
                                    'ا', 'ب', 'ت', 'ث', 'ج', 'ح', 'خ', 'د', 'ذ', 'ر', 
                                    'ز', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 
                                    'ق', 'ك', 'ل', 'م', 'ن', 'ه', 'ة', 'و', 'ي', 'ى', 
                                    'أ', 'إ', 'ء', 'ؤ', 'ئ', 'آ', 'ْ');
  
    private static $_arReplacements = array('#', "t'h", "t'h", "k'h", "k'h", "d'h", "d'h", "d'h", "d'h", "s'h", "s'h", "s'h", "s'h",
                                    'a', 'a', 'ou', 'ei', 'an', 'an', 'an', 'a', 'i', 'u', 
                                    'a', 'b', 't', 'th', 'j', 'h', 'kh', 'd', 'dh', 'r', 
                                    'z', 's', 'sh', 's', 'd', 't', 'z', "'", 'gh', 'f', 
                                    'q', 'k', 'l', 'm', 'n', 'h', 'h', 'w', 'y', 'a', 
                                    "'a", 'i', "'a", "u'", "'i", "'aa", '');
        
    private static $_arFinePatterns = array("/'+/", "/([\- ])'/", '/(.)#/');
    private static $_arFineReplacements = array("'", '\\1', "\\1'\\1");
    
    /**
     * Transliterate Arabic string into English by render them in the 
     * orthography of the English language
     *           
     * @param string $string       Arabic string you want to transliterate
     * @param string $inputCharset (optional) Input charset [utf-8|windows-1256|iso-8859-6]
     *                             default value is NULL (use set input charset)       
     * @param object $main         Main Ar-PHP object to access charset converter options
     *                    
     * @return String Out of vocabulary Arabic string in English characters
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public static function ar2en($string, $inputCharset = null, $main = null)
    {
        if ($main) {
            if ($inputCharset == null) $inputCharset = $main->getInputCharset();
            $string = $main->coreConvert($string, $inputCharset, 'utf-8');
        }

        $string = str_replace('/ة ال/', 'tul', $string);

        $words = split(' ', $string);
        $string = '';
        
        foreach ($words as $word) {
            $temp = preg_replace(self::$_arRegPatterns, self::$_arRegReplacements, $word);
            $temp = str_replace(self::$_arPatterns, self::$_arReplacements, $temp);
            $temp = preg_replace(self::$_arFinePatterns, self::$_arFineReplacements, $temp);
            
            $temp = ucwords($temp);

            $pos  = strpos($temp, '-');
            if ($pos > 0) {
                $temp2  = substr($temp, 0, $pos);
                $temp2 .= '-'.strtoupper($temp[$pos+1]);
                $temp2 .= substr($temp, $pos+2);
            } else {
                $temp2  = $temp;
            }

            $string .= ' ' . $temp2;
        }
        
        return $string;
    }
}
?>
