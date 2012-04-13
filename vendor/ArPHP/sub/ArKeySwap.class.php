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
 * Class Name: Arabic Keyboard Swapping Language
 *  
 * Filename:   ArKeySwap.class.php
 *  
 * Original    Author(s): Khaled Al-Sham'aa <khaled.alshamaa@gmail.com>
 *  
 * Purpose:    Convert keyboard language programmatically (English - Arabic)
 *  
 * ----------------------------------------------------------------------
 *  
 * Arabic Keyboard Swapping Language
 *
 * PHP class to convert keyboard language between English and Arabic
 * programmatically. This function can be helpful in dual language forms when
 * users miss change keyboard language while they are entering data.
 * 
 * If you wrote an Arabic sentence while your keyboard stays in English mode by 
 * mistake, you will get a non-sense English text on your PC screen. In that case 
 * you can use this class to make a kind of magic conversion to swap that odd text 
 * by original Arabic sentence you meant when you type on your keyboard.
 * 
 * Please note that magic conversion in the opposite direction (if you type English 
 * sentences while your keyboard stays in Arabic mode) is also available in this 
 * class, but it is not reliable as much as previous case because in Arabic keyboard 
 * we have some keys provide a shortcut to type two chars in one click (those keys 
 * include: b, B, G and T).
 * 
 * Well, we try in this class to come over this issue by suppose that user used 
 * optimum way by using shortcut keys when available instead of assemble chars using 
 * stand alone keys, but if (s)he does not then you may have some typo chars in 
 * converted text.
 * 
 * Example:
 * <code>
 *     include('./Arabic.php');
 *     $Ar = new Arabic('ArKeySwap');
 * 
 *     $str = "Hpf lk hgkhs hglj'vtdkK Hpf hg`dk dldg,k f;gdjil Ygn ,p]hkdm ...";
 * 
 *     echo "<p><u><i>Before:</i></u><br />$str<br /><br />";
 *     
 *     $text = $Ar->swap_ea($str);\
 *        
 *     echo "<u><i>After:</i></u><br />$text<br /><br />";    
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
// namespace Arabic/ArKeySwap;

/**
 * This PHP class convert keyboard language programmatically (English - Arabic)
 *  
 * @category  Text 
 * @package   Arabic
 * @author    Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
 * @copyright 2009 Khaled Al-Shamaa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */ 
class ArKeySwap
{
    // First 12 chars replaced by 1 Byte in Arabic keyboard while rest replaced by 2 Bytes UTF8
    private static $_swap_en = '{}DFL:"ZCV<>`qwertyuiop[]asdfghjkl;\'zxcvnm,./~QWERYIOPASHJKXNM?';
    private static $_swap_ar = '<>][/:"~}{,.ذضصثقفغعهخحجدشسيبلاتنمكطئءؤرىةوزظًٌَُّإ÷×؛ٍِأـ،ْآ’؟';
    
    private static $_swap_fr = '²azertyuiop^$qsdfghjklmù*<wxcvn,;:!²1234567890°+AZERYIOP¨£QSDFHJKLM%µ<WXCVN?./§';
    private static $_swap_ar_azerty = '>ضصثقفغعهخحجدشسيبلاتنمكطذ\\ئءؤرىةوزظ>&é"\'(-è_çà)=ضصثقغهخحجدشسيباتنمكطذ\\ئءؤرىةوزظ';  
    
    /**
     * Make conversion to swap that odd Arabic text by original English sentence 
     * you meant when you type on your keyboard (if keyboard language was  
     * incorrect)
     *          
     * @param string $text         Odd Arabic string
     * @param string $inputCharset (optional) Input charset [utf-8|windows-1256|iso-8859-6]
     *                             default value is NULL (use set input charset)       
     * @param object $main         Main Ar-PHP object to access charset converter options
     *                    
     * @return string Normal English string
     * @author Khaled Al-Shamaa
     */
    public static function swap_ae($text, $inputCharset = null, $main = null)
    {
        if ($main) {
            if ($inputCharset == null) $inputCharset = $main->getInputCharset();
            $text = $main->coreConvert($text, $inputCharset, 'windows-1256');
        }

        $output = '';
        
        $text = stripslashes($text);

        $text = str_replace('لا', 'b', $text);
        $text = str_replace('لآ', 'B', $text);
        $text = str_replace('لأ', 'G', $text);
        $text = str_replace('لإ', 'T', $text);
        $text = str_replace('‘' , 'U', $text);
        
        $max = strlen($text);
        
        for ($i=0; $i<$max; $i++) {

            $pos = strpos(self::$_swap_ar, $text[$i]);

            if ($pos === false) {
                $output .= $text[$i];
            } else {
                $pos2 = strpos(self::$_swap_ar, $text[$i].$text[$i+1]);
                if ($pos2 !== false) {
                    $pos = $pos2;
                    $i++;
                }

                if ($pos < 12) {
                    $adj_pos = $pos;
                } else {
                    $adj_pos = ($pos - 12)/2 + 12;
                }

                $output .= substr(self::$_swap_en, $adj_pos, 1);
            }

        }
        
        return $output;
    }
    
    /**
     * Make conversion to swap that odd English text by original Arabic sentence 
     * you meant when you type on your keyboard (if keyboard language was  
     * incorrect)
     *           
     * @param string $text          Odd English string
     * @param string $outputCharset (optional) Output charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set output charset)       
     * @param object $main          Main Ar-PHP object to access charset converter options
     *                    
     * @return string Normal Arabic string
     * @author Khaled Al-Shamaa
     */
    public static function swap_ea($text, $outputCharset = null, $main = null)
    {
        $output = '';
        
        $text = stripslashes($text);
        
        $text = str_replace('b', 'لا', $text);
        $text = str_replace('B', 'لآ', $text);
        $text = str_replace('G', 'لأ', $text);
        $text = str_replace('T', 'لإ', $text);
        $text = str_replace('U', '‘' , $text);
        
        $max = strlen($text);
        
        for ($i=0; $i<$max; $i++) {
            $pos = strpos(self::$_swap_en, $text[$i]);
            if ($pos === false) {
                $output .= $text[$i];
            } else {
                if ($pos < 12) {
                    $adj_pos = $pos;
                    $len = 1;
                } else {
                    $adj_pos = ($pos - 12)*2 + 12;
                    $len = 2; 
                }

                $output .= substr(self::$_swap_ar, $adj_pos, $len);
            }
        }
        
        if ($main) {
            if ($outputCharset == null) $outputCharset = $main->getOutputCharset();
            $output = $main->coreConvert($output, 'utf-8', $outputCharset);
        }
        
        return $output;
    }
}
?>
