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
 * Class Name: English-Arabic Transliteration
 *  
 * Filename:   ArTransliteration.class.php
 *  
 * Original    Author(s): Khaled Al-Sham'aa <khaled.alshamaa@gmail.com>
 *  
 * Purpose:    Transliterate English words into Arabic by render them
 *             in the orthography of the Arabic language
 *              
 * ----------------------------------------------------------------------
 *
 * English-Arabic Transliteration
 *    
 * PHP class transliterate English words into Arabic by render them in the 
 * orthography of the Arabic language.
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
 *   $Ar = new Arabic('ArTransliteration');
 *     
 *   $ar_term = $Ar->en2ar($en_term);
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
// namespace Arabic/ArTransliteration;

/**
 * This PHP class transliterate English words into Arabic
 *  
 * @category  Text 
 * @package   Arabic
 * @author    Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
 * @copyright 2009 Khaled Al-Shamaa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */ 
class ArTransliteration
{
    private static $_enRegPatterns = array('/^au/', '/^a/', '/^e/', '/^i/', '/^mc/', '/^o/', '/^u/', '/^wr/', '/ough$/', '/ue$/', '/a$/', '/s$/');
    private static $_enRegReplacements = array('ا', 'ا', 'ا', 'ا', 'ماك', 'او', 'ا', 'ر', 'ه', '', 'ه', 'س');
            
    private static $_enPatterns = array('ough', 'alk', 'ois', 'sch', 'tio', 'ai', 'au', 'bb', 'cc', 'ce', 
                                      'ci', 'cy', 'ch', 'ck', 'dd', 'ea', 'ee', 'ey', 'ff', 'ge', 
                                      'gi', 'gg', 'gh', 'gn', 'ie', 'kk', 'kh', 'll', 'mm', 'nn', 
                                      'oo', 'ou', 'ph', 'pp', 'qu', 'rr', 'sh', 'ss', 'th', 'tt', 
                                      'wr', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 
                                      'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 
                                      't', 'u', 'v', 'w', 'x', 'y', 'z');
            
    private static $_enReplacements = array('او', 'وك', 'وا', 'ش', 'ش', 'اي', 'او', 'ب', 'ك', 'س', 
                                          'سي', 'سي', 'تش', 'ك', 'د', 'ي', 'ي', 'اي', 'ف', 'ج', 
                                          'جي', 'غ', 'ف', 'جن', 'ي', 'ك', 'خ', 'ل', 'م', 'ن', 
                                          'و', 'و', 'ف', 'ب', 'كو', 'ر', 'ش', 'س', 'ذ', 'ت', 
                                          'ر', 'ا', 'ب', 'ك', 'د', '', 'ف', 'غ', 'ه', 'ي', 
                                          'ج', 'ك', 'ل', 'م', 'ن', 'و', 'ب', 'ك', 'ر', 'س', 
                                          'ت', 'و', 'ف', 'و', 'كس', 'ي', 'ز');

    
    /**
     * Transliterate English string into Arabic by render them in the 
     * orthography of the Arabic language
     *         
     * @param string $string        English string you want to transliterate
     * @param string $outputCharset (optional) Output charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set output charset)       
     * @param object $main          Main Ar-PHP object to access charset converter options
     *                    
     * @return String Out of vocabulary English string in Arabic characters
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public static function en2ar($string, $outputCharset = null, $main = null)
    {
        $string = strtolower($string);
        $words = split(' ', $string);
        $string = '';
        
        foreach ($words as $word) {
            $word = preg_replace(self::$_enRegPatterns, self::$_enRegReplacements, $word);
                                      
            $word = str_replace(self::$_enPatterns, self::$_enReplacements, $word);

            $string .= ' ' . $word;
        }
        
        if ($main) {
            if ($outputCharset == null) $outputCharset = $main->getOutputCharset();
            $string = $main->coreConvert($string, 'utf-8', $outputCharset);
        }

        return $string;
    }
}
?>
