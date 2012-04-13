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
 * Class Name: Arabic Charset Converter
 *  
 * Filename:   ArCharsetC.class.php
 *  
 * Original    Author(s): Khaled Al-Sham'aa <khaled.alshamaa@gmail.com>
 *  
 * Purpose:    Convert a given Arabic string from one Arabic character set to another,
 *             those available character sets includes the most popular three:
 *             Windows-1256, ISO 8859-6, and UTF-8
 *              
 * ----------------------------------------------------------------------
 *  
 * Arabic Charset Converter
 *
 * PHP class to convert a given Arabic string from one Arabic character set
 * to another, those available character sets includes the most popular three:
 * Windows-1256, ISO 8859-6, and UTF-8.
 *
 * Example:
 * <code>
 *   include('./Arabic.php');
 *   $Arabic = new Arabic('ArCharsetC');
 *
 *   $Arabic->setInputCharset('windows-1256');
 *   $Arabic->setOutputCharset('utf-8');
 *   
 *   $charset = $Arabic->getOutputCharset();
 *      
 *   $text = $Arabic->convert($text);
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
// namespace Arabic/ArCharsetC;

/**
 * This PHP class converts a given Arabic string from one Arabic character set to another
 *  
 * @category  Text 
 * @package   Arabic
 * @author    Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
 * @copyright 2009 Khaled Al-Shamaa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */ 
class ArCharsetC
{
    private $_utf;
    private $_win;
    private $_iso;

    // Hold an instance of the class
    private static $instance;
    
    /**
     * Loads initialize values
     * 
     * @param array $sets Charsets you would like to support
     */         
    public function __construct($sets = array('windows-1256', 'utf-8'))
    {
        $path = str_replace('\\', '/', __FILE__);
        $path = substr($path, 0, strrpos($path, '/'));

        $handle = fopen($path.'/charset.src', 'r');
        if ($handle) {
            $this->_utf = fgets($handle, 4096);
            $this->_win = fgets($handle, 4096);
            $this->_iso = fgets($handle, 4096);
            $this->_html = fgets($handle, 4096);
            fclose($handle);
        }

        if (in_array('windows-1256', $sets)) {
            include_once $path.'/charset/_windows1256.php';
        }
        
        if (in_array('iso-8859-6', $sets)) {
            include_once $path.'/charset/_iso88596.php';
        }
        
        if (in_array('utf-8', $sets)) {
            include_once $path.'/charset/_utf8.php';
        }
        
        if (in_array('bug', $sets)) {
            include_once $path.'/charset/_bug.php';
        }
        
        if (in_array('html', $sets)) {
            include_once $path.'/charset/_html.php';
        }
    }

    // The singleton method
    public static function singleton() 
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }
    
    /**
     * Get HTML entity from given position
     *      
     * @param integer $index Extract position
     *      
     * @return string HTML entity
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _getHTML($index)
    {
        return trim(substr($this->_html, $index*4, 4));
    }
    
    /**
     * Get UTF character from given position
     *      
     * @param integer $index Extract position
     *      
     * @return string UTF character
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _getUTF($index)
    {
        return trim(substr($this->_utf, $index*2, 2));
    }
    
    /**
     * Get extract position of a given UTF character
     *      
     * @param string $char UTF character
     *      
     * @return integer Extract position
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _findUTF($char)
    {
        if (!$char) return false;
        return strpos($this->_utf, $char)/2;
    }
    
    /**
     * Get Windows-1256 character from given position
     *      
     * @param integer $index Extract position
     *      
     * @return string Windows-1256 character
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _getWIN($index)
    {
        return substr($this->_win, $index, 1);
    }
    
    /**
     * Get extract position of a given Windows-1256 character
     *      
     * @param string $char Windows-1256 character
     *      
     * @return integer Extract position
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _findWIN($char)
    {
        if (!$char) return false;
        return strpos($this->_win, $char);
    }
    
    /**
     * Get ISO-8859-6 character from given position
     *      
     * @param integer $index Extract position
     *      
     * @return string ISO-8859-6 character
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _getISO($index)
    {
        return substr($this->_iso, $index, 1);
    }
    
    /**
     * Get extract position of a given ISO-8859-6 character
     *      
     * @param string $char ISO-8859-6 character
     *      
     * @return integer Extract position
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _findISO($char)
    {
        if (!$char) return false;
        return strpos($this->_iso, $char);
    }
    
    /**
     * Convert Arabic string from Windows-1256 to ISO-8859-6 format
     *      
     * @param string $string Original Arabic string in Windows-1256 format
     *      
     * @return string Converted Arabic string in ISO-8859-6 format
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function win2iso($string)
    {
        $chars = preg_split('//', $string);
        $converted = null;
        
        foreach ($chars as $char) {
            $key = $this->_findWIN($char);
            if (is_int($key)) {
                $converted .= $this->_getISO($key);
            } else {
                $converted .= $char;
            }
        }
        return $converted;
    }
    
    /**
     * Convert Arabic string from Windows-1256 to UTF-8 format
     *      
     * @param string $string Original Arabic string in Windows-1256 format
     *      
     * @return string Converted Arabic string in Windows-1256 format
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function win2utf($string)
    {
        $chars = preg_split('//', $string);
        $converted = null;
        
        foreach ($chars as $char) {
            $key = $this->_findWIN($char);

            if (is_int($key)) {
                $converted .= $this->_getUTF($key);
            } else {
                $converted .= $char;
            }
        }
        return $converted;
    }

    /**
     * Convert Arabic string from Windows-1256 to HTML entities format
     *      
     * @param string $string Original Arabic string in Windows-1256 format
     *      
     * @return string Converted Arabic string in HTML entities format
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function win2html($string)
    {
        $chars = preg_split('//', $string);
        $converted = null;
        
        foreach ($chars as $char) {
            $key = $this->_findWIN($char);

            if (is_int($key) && $key < 58) {
                $converted .= '&#' . $this->_getHTML($key) . ';';
            } else {
                $converted .= $char;
            }
        }
        return $converted;
    }
    
    /**
     * Convert Arabic string from ISO-8859-6 to Windows-1256 format
     *      
     * @param string $string Original Arabic string in ISO-8859-6 format
     *      
     * @return string Converted Arabic string in Windows-1256 format
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function iso2win($string)
    {
        $chars = preg_split('//', $string);
        $converted = null;
        
        foreach ($chars as $char) {
            $key = $this->_findISO($char);
            if (is_int($key)) {
                $converted .= $this->_getWIN($key);
            } else {
                $converted .= $char;
            }
        }
        return $converted;
    }
    
    /**
     * Convert Arabic string from ISO-8859-6 to UTF-8 format
     *      
     * @param string $string Original Arabic string in ISO-8859-6 format
     *      
     * @return string Converted Arabic string in UTF-8 format
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function iso2utf($string)
    {
        $chars = preg_split('//', $string);
        $converted = null;
        
        foreach ($chars as $char) {
            $key = $this->_findISO($char);
            if (is_int($key)) {
                $converted .= $this->_getUTF($key);
            } else {
                $converted .= $char;
            }
        }
        return $converted;
    }
    
    /**
     * Convert Arabic string from UTF-8 to Windows-1256 format
     *      
     * @param string $string Original Arabic string in UTF-8 format
     *      
     * @return string Converted Arabic string in Windows-1256 format
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function utf2win($string)
    {
        $chars = preg_split('//', $string);
        $converted = null;
        
        $cmp = false;
        foreach ($chars as $char) {
            $ascii = ord($char);
            if (($ascii == 216 || $ascii == 217) && !$cmp) {
                $code = $char;
                $cmp = true;
                continue;
            }
            if ($cmp) {
                $code .= $char;
                $cmp = false;
                $key = $this->_findUTF($code);
                if (is_int($key)) {
                    $converted .= $this->_getWIN($key);
                }
            } else {
                $converted .= $char;
            }
        }
        return $converted;
    }
    
    /**
     * Convert Arabic string from UTF-8 to ISO-8859-6 format
     *      
     * @param string $string Original Arabic string in UTF-8 format
     *      
     * @return string Converted Arabic string in ISO-8859-6 format
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function utf2iso($string)
    {
        $chars = preg_split('//', $string);
        $converted = null;
        
        $cmp = false;
        foreach ($chars as $char) {
            $ascii = ord($char);
            if (($ascii == 216 || $ascii == 217) && !$cmp) {
                $code = $char;
                $cmp = true;
                continue;
            }
            if ($cmp) {
                $code .= $char;
                $cmp = false;
                $key = $this->_findUTF($code);
                if (is_int($key)) {
                    $converted .= $this->_getISO($key);
                }
            } else {
                $converted .= $char;
            }
        }
        return $converted;
    }
    
    /**
     * Convert buggy Arabic imported database string to Windows-1256 format
     *      
     * @param string $string Original corrupted Arabic string, usually when export
     *                       database from MySQL < 4.1 into MySQL >= 4.1 using phpMyAdmin
     *                       tool where each Arabic UTF-8 character translate as two
     *                       ISO-8859-1 characters in export, then translate them into
     *                       UTF-8 format in import.
     *                    
     * @return string Converted Arabic string in Windows-1256 format
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function bug2win($string)
    {
        $chars = preg_split('//', $string);
        $converted = null;
        
        $cmp = false;
        foreach ($chars as $char) {
            $ascii = ord($char);
            if (($ascii == 195 || $ascii == 194) && !$cmp) {
                $code = $char;
                $cmp = true;
                continue;
            }
            if ($cmp) {
                $code .= $char;
                $cmp = false;
                $key = array_search($code, $this->bug);
                if (is_int($key)) {
                    $converted .= $this->_getWIN($key);
                }
            } else {
                $converted .= $char;
            }
        }
        return $converted;
    }
    
    /**
     * Convert buggy Arabic imported database string to UTF-8 format
     *      
     * @param string $string Original corrupted Arabic string, usually when export
     *                       database from MySQL < 4.1 into MySQL >= 4.1 using phpMyAdmin
     *                       tool where each Arabic UTF-8 character translate as two
     *                       ISO-8859-1 characters in export, then translate them into
     *                       UTF-8 format in import.
     *                    
     * @return string Converted Arabic string in UTF-8 format
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function bug2utf($string)
    {
        $chars = preg_split('//', $string);
        $converted = null;
        
        $cmp = false;
        foreach ($chars as $char) {
            $ascii = ord($char);
            if (($ascii == 195 || $ascii == 194) && !$cmp) {
                $code = $char;
                $cmp = true;
                continue;
            }
            if ($cmp) {
                $code .= $char;
                $cmp = false;
                $key = array_search($code, $this->bug);
                if (is_int($key)) {
                    $converted .= $this->_getUTF($key);
                }
            } else {
                $converted .= $char;
            }
        }
        return $converted;
    }
    
    /**
     * Convert buggy Arabic imported database string to ISO-8859-6 format
     *      
     * @param string $string Original corrupted Arabic string, usually when export
     *                       database from MySQL < 4.1 into MySQL >= 4.1 using phpMyAdmin
     *                       tool where each Arabic UTF-8 character translate as two
     *                       ISO-8859-1 characters in export, then translate them into
     *                       UTF-8 format in import.
     *                    
     * @return string Converted Arabic string in ISO-8859-6 format
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function bug2iso($string)
    {
        $chars = preg_split('//', $string);
        $converted = null;
        
        $cmp = false;
        foreach ($chars as $char) {
            $ascii = ord($char);
            if (($ascii == 195 || $ascii == 194) && !$cmp) {
                $code = $char;
                $cmp = true;
                continue;
            }
            if ($cmp) {
                $code .= $char;
                $cmp = false;
                $key = array_search($code, $this->bug);
                if (is_int($key)) {
                    $converted .= $this->_getISO($key);
                }
            } else {
                $converted .= $char;
            }
        }
        return $converted;
    }
    
    /**
     * Convert buggy Arabic string as HTML entities to UTF-8 format
     *      
     * @param string $string Original corrupted Arabic string, usually when insert 
     *                       Arabic string as HTML entities.
     *                            
     * @return string Converted Arabic string in UTF-8 format
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function html2utf($string)
    {
        $converted = preg_replace($this->html, $this->utf8, $string);
        return $converted;
    }
    
    /**
     * Convert buggy Arabic string as HTML entities to Windows-1256 format
     *                    
     * @param string $string Original corrupted Arabic string, usually when insert 
     *                       Arabic string as HTML entities.
     *                    
     * @return string Converted Arabic string in Windows-1256 format
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function html2win($string)
    {
        $converted = preg_replace($this->html, $this->windows1256, $string);
        return $converted;
    }
    
    /**
     * Convert buggy Arabic string as HTML entities to ISO-8859-6 format
     *      
     * @param string $string Original corrupted Arabic string, usually when insert 
     *                       Arabic string as HTML entities.
     *                    
     * @return string Converted Arabic string in ISO-8859-6 format
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function html2iso($string)
    {
        $converted = preg_replace($this->html, $this->iso88596, $string);
        return $converted;
    }
}
?>
