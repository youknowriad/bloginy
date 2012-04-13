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
 * Class Name: Compress string using Huffman-like coding
 *  
 * Filename:   ArCompressStr.class.php
 *  
 * Original    Author(s): Khaled Al-Sham'aa <khaled.alshamaa@gmail.com>
 *  
 * Purpose:    This class will compress given string in binary format
 *             using variable-length code table (derived in a particular way 
 *             based on the estimated probability of occurrence for each 
 *             possible value of the source symbol) for encoding a source symbol
 *              
 * ----------------------------------------------------------------------
 *  
 * Arabic Compress String Class
 *
 * Compress string using Huffman-like coding
 *
 * This class compresses text strings into roughly 70% of their original size 
 * by benefit from using compact coding for most frequented letters in a given 
 * language. This algorithm associated with text language, so you will find 6 
 * different classes for the following languages: Arabic, English, French, 
 * German, Italian and Spanish language.
 * 
 * Benefits of this compress algorithm include:
 * 
 * - It is written in pure PHP code, so there is no need to any PHP extensions to use it.
 * - You can search in compressed string directly without any need uncompress text before search in.
 * - You can get original string length directly without need to uncompress compressed text.
 * 
 * Note:
 * Unfortunately text compressed using this algorithm lose the structure that 
 * normal zip algorithm used, so benefits from using ZLib functions on this 
 * text will be reduced.
 * 
 * There is another drawback, this algorithm working only on text from a given 
 * language, it does not working fine on binary files like images or PDF.
 * 
 * Example:
 * <code>
 * include('./Arabic.php');
 * $obj = new Arabic('ArCompressStr');
 * 
 * $obj->setInputCharset('windows-1256');
 * $obj->setOutputCharset('windows-1256');
 * 
 * $file = 'Compress/ar_example.txt';
 * $fh = fopen($file, 'r');
 * $str = fread($fh, filesize($file));
 * fclose($fh);
 * 
 * $zip = $obj->compress($str);
 * 
 * $before = strlen($str);
 * $after = strlen($zip);
 * $rate = round($after * 100 / $before);
 * 
 * echo "String size before was: $before Byte<br>";
 * echo "Compressed string size after is: $after Byte<br>";
 * echo "Rate $rate %<hr>";
 * 
 * $str = $obj->decompress($zip);
 * 
 * if ($obj->search($zip, $word)) {
 *     echo "Search for $word in zipped string and find it<hr>";
 * } else {
 *     echo "Search for $word in zipped string and do not find it<hr>";
 * }
 * 
 * $len = ArCompressStr::length($zip);
 * echo "Original length of zipped string is $len Byte<hr>";
 * 
 * echo '<div dir="rtl" align="justify">'.nl2br($str).'</div>';   
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
// namespace Arabic/ArCompressStr;

/**
 * This PHP class compress Arabic string using Huffman-like coding
 *  
 * @category  Text 
 * @package   Arabic
 * @author    Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
 * @copyright 2009 Khaled Al-Shamaa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */ 
class ArCompressStr
{
    private static $_encode = ' «·„ÌÊ …';
    private static $_binary = '0000|0001|0010|0011|0100|0101|0110|0111|';
    
    private static $_hex = '0123456789abcdef';
    private static $_bin = '0000|0001|0010|0011|0100|0101|0110|0111|1000|1001|1010|1011|1100|1101|1110|1111|';
    
    /**
     * Set required _encode and _decode hash of most probably character in 
     * selected language
     *      
     * @param string $lang [en, fr, gr, it, sp, ar] Language profile selected
     *      
     * @return boolean TRUE 
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public static function setLang($lang) 
    {
        switch ($lang) {
        case 'en':
            self::$_encode = ' etaoins';
            break;
        case 'fr':
            self::$_encode = ' enasriu';
            break;
        case 'gr':
            self::$_encode = ' enristu';
            break;
        case 'it':
            self::$_encode = ' eiaorln';
            break;
        case 'sp':
            self::$_encode = ' eaosrin';
            break;
        default:
            self::$_encode = ' «·„ÌÊ …';
        }

        self::$_binary = '0000|0001|0010|0011|0100|0101|0110|0111|';
        
        return true;
    }
    
    /**
     * Compress the given string using the Huffman-like coding
     *      
     * @param string $str          The text to compress
     * @param string $inputCharset (optional) Input charset [utf-8|windows-1256|iso-8859-6]
     *                             default value is NULL (use set input charset)       
     * @param object $main         Main Ar-PHP object to access charset converter options
     *                    
     * @return binary The compressed string in binary format
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public static function compress($str, $inputCharset = null, $main = null) 
    {
        if ($main) {
            if ($inputCharset == null) $inputCharset = $main->getInputCharset();
            $str = $main->coreConvert($str, $inputCharset, 'windows-1256');
        }

        $bits = self::_str2bits($str);
        $hex = self::_bits2hex($bits);
        $bin = pack('h*', $hex);

        return $bin;
    }

    /**
     * Uncompress a compressed string
     *       
     * @param binary $bin           The text compressed by compress(). 
     * @param string $outputCharset (optional) Output charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set output charset)       
     * @param object $main          Main Ar-PHP object to access charset converter options
     *                    
     * @return string The original uncompressed string
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public static function decompress($bin, $outputCharset = null, $main = null) 
    {
        $temp = unpack('h*', $bin);
        $bytes = $temp[1];

        $bits = self::_hex2bits($bytes);
        $str = self::_bits2str($bits);
        
        if ($main) {
            if ($outputCharset == null) $outputCharset = $main->getOutputCharset();
            $str = $main->coreConvert($str, 'windows-1256', $outputCharset);
        }

        return $str;
    }

    /**
     * Search a compressed string for a given word
     *      
     * @param binary $bin          Compressed binary string
     * @param string $word         The string you looking for
     * @param string $inputCharset (optional) Input charset [utf-8|windows-1256|iso-8859-6]
     *                             default value is NULL (use set input charset)       
     * @param object $main         Main Ar-PHP object to access charset converter options
     *                    
     * @return boolean True if found and False if not found
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public static function search($bin, $word, $inputCharset = null, $main = null) 
    {
        if ($main) {
            if ($inputCharset == null) $inputCharset = $main->getInputCharset();
            $word = $main->coreConvert($word, $inputCharset, 'windows-1256');
        }

        $w_bits = self::_str2bits($word);

        $temp = unpack('h*', $bin);
        $bytes = $temp[1];
        $bits = self::_hex2bits($bytes);

        if (strpos($bits, $w_bits)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retrieve the origenal string length
     *      
     * @param binary $bin Compressed binary string
     *      
     * @return integer Origenal string length
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public static function length($bin) 
    {
        $temp = unpack('h*', $bin);
        $bytes = $temp[1];
        $bits = self::_hex2bits($bytes);

        $count = 0;
        $i = 0;

        while (isset($bits[$i])) {
            $count++;
            if ($bits[$i] == 1) {
                $i += 9;
            } else {
                $i += 4;
            }
        }

        return $count;
    }

    /**
     * Convert textual string into binary string
     *      
     * @param string $str The textual string to convert
     *       
     * @return binary The binary representation of textual string
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private static function _str2bits($str) 
    {
        $bits = '';
        $total = strlen($str);

        $i = -1;
        while (++$i < $total) {
            $char = $str[$i];

            $pos = strpos(self::$_encode, $char);
            if ($pos !== false) {
                $bits .= substr(self::$_binary, $pos*5, 4);
            } else {
                $int = ord($char);
                $bits .= '1'.substr(self::$_bin, (int)($int/16)*5, 4);
                $bits .= substr(self::$_bin, ($int%16)*5, 4);
            }
        }

        // Complete nibbel
        $add = strlen($bits) % 4;
        $bits .= str_repeat('0', $add);

        return $bits;
    }

    /**
     * Convert binary string into textual string
     *      
     * @param binary $bits The binary string to convert
     *       
     * @return string The textual representation of binary string
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private static function _bits2str($bits) 
    {
        $str = '';
        while ($bits) {
            $flag = substr($bits, 0, 1);
            $bits = substr($bits, 1);

            if ($flag == 1) {
                $byte = substr($bits, 0, 8);
                $bits = substr($bits, 8);

                if ($bits || strlen($code) == 8) {
                    $int = base_convert($byte, 2, 10);
                    $char = chr($int);
                    $str .= $char;
                }
            } else {
                $code = substr($bits, 0, 3);
                $bits = substr($bits, 3);

                if ($bits || strlen($code) == 3) {
                    $pos = strpos(self::$_binary, "0$code|");
                    $str .= substr(self::$_encode, $pos/5, 1);
                }
            }
        }

        return $str;
    }

    /**
     * Convert binary string into hexadecimal string
     *      
     * @param binary $bits The binary string to convert
     *       
     * @return hexadecimal The hexadecimal representation of binary string  
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private static function _bits2hex($bits) 
    {
        $hex = '';
        $total = strlen($bits) / 4;

        for ($i = 0; $i < $total; $i++) {
            $nibbel = substr($bits, $i*4, 4);

            $pos = strpos(self::$_bin, $nibbel);
            $hex .= substr(self::$_hex, $pos/5, 1);
        }

        return $hex;
    }

    /**
     * Convert hexadecimal string into binary string
     *      
     * @param hexadecimal $hex The hexadezimal string to convert
     *       
     * @return binary The binary representation of hexadecimal string
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private static function _hex2bits($hex) 
    {
        $bits = '';
        $total = strlen($hex);

        for ($i = 0; $i < $total; $i++) {
            $pos = strpos(self::$_hex, $hex[$i]);
            $bits .= substr(self::$_bin, $pos*5, 4);
        }

        return $bits;
    }
}
?>
