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
 * Class Name: Spell numbers in the Arabic idiom
 *  
 * Filename:   ArNumbers.class.php
 *  
 * Original    Author(s): Khaled Al-Sham'aa <khaled.alshamaa@gmail.com>
 *  
 * Purpose:    Spell numbers in the Arabic idiom
 *  
 * ----------------------------------------------------------------------
 *  
 * Spell numbers in the Arabic idiom
 *
 * PHP class to spell numbers in the Arabic idiom. This function is very
 * useful for financial applications in Arabic for example.
 *    
 * If you ever have to create an Arabic PHP application built around invoicing or 
 * accounting, you might find this class useful. Its sole reason for existence is 
 * to help you translate integers into their spoken-word equivalents in Arabic 
 * language.
 * 
 * How is this useful? Well, consider the typical invoice: In addition to a 
 * description of the work done, the date, and the hourly or project cost, it always 
 * includes a total cost at the end, the amount that the customer is expected to pay. 
 * To avoid any misinterpretation of the total amount, many organizations (mine 
 * included) put the amount in both words and figures; for example, $1,200 becomes 
 * "one thousand and two hundred dollars." You probably do the same thing every time 
 * you write a check.
 * 
 * Now take this scenario to a Web-based invoicing system. The actual data used to 
 * generate the invoice will be stored in a database as integers, both to save space 
 * and to simplify calculations. So when a printable invoice is generated, your Web 
 * application will need to convert those integers into words, this is more clarity 
 * and more personality.
 * 
 * This class will accept almost any numeric value and convert it into an equivalent 
 * string of words in written Arabic language (using Windows-1256 character set). 
 * The value can be any positive number up to 999,999,999 (users should not use 
 * commas). It will take care of feminine and Arabic grammar rules.
 *
 * Example:
 * <code>
 *     include('./Arabic.php');
 *     $Arabic = new Arabic('ArNumbers');
 *     
 *     $Arabic->ArNumbers->setFeminine(1);
 *     $Arabic->ArNumbers->setFormat(1);
 *     
 *     $integer = 2147483647;
 *     
 *     $text = $Arabic->int2str($integer);
 *     
 *     echo "<p align=\"right\"><b class=hilight>$integer</b><br />$text</p>";
 * 
 *     $Arabic->ArNumbers->setFeminine(2);
 *     $Arabic->ArNumbers->setFormat(2);
 *     
 *     $integer = 2147483647;
 *     
 *     $text = $Arabic->int2str($integer);
 *     
 *     echo "<p align=\"right\"><b class=hilight>$integer</b><br />$text</p>";   
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
// namespace Arabic/ArNumbers;

/**
 * This PHP class spell numbers in the Arabic idiom
 *  
 * @category  Text 
 * @package   Arabic
 * @author    Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
 * @copyright 2009 Khaled Al-Shamaa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */ 
class ArNumbers
{
    private $_individual = array();
    private $_feminine = 1;
    private $_format = 1;
    
    /**
     * Loads initialize values
     */         
    public function __construct()
    {
        $this->_individual[1][1] = 'واحد';
        $this->_individual[1][2] = 'واحدة';
        
        $this->_individual[2][1][1] = 'إثنان';
        $this->_individual[2][1][2] = 'إثنين';
        $this->_individual[2][2][1] = 'إثنتان';
        $this->_individual[2][2][2] = 'إثنتين';
        
        $this->_individual[3][1] = 'ثلاث';
        $this->_individual[4][1] = 'أربع';
        $this->_individual[5][1] = 'خمس';
        $this->_individual[6][1] = 'ست';
        $this->_individual[7][1] = 'سبع';
        $this->_individual[8][1] = 'ثماني';
        $this->_individual[9][1] = 'تسع';
        $this->_individual[10][1] = 'عشر';
        $this->_individual[3][2] = 'ثلاثة';
        $this->_individual[4][2] = 'أربعة';
        $this->_individual[5][2] = 'خمسة';
        $this->_individual[6][2] = 'ستة';
        $this->_individual[7][2] = 'سبعة';
        $this->_individual[8][2] = 'ثمانية';
        $this->_individual[9][2] = 'تسعة';
        $this->_individual[10][2] = 'عشرة';
        
        $this->_individual[11][1] = 'أحد عشر';
        $this->_individual[11][2] = 'إحدى عشرة';
        
        $this->_individual[12][1][1] = 'إثنا عشر';
        $this->_individual[12][1][2] = 'إثني عشر';
        $this->_individual[12][2][1] = 'إثنتا عشرة';
        $this->_individual[12][2][2] = 'إثنتي عشرة';
        
        $this->_individual[13][1] = 'ثلاث عشرة';
        $this->_individual[14][1] = 'أربع عشرة';
        $this->_individual[15][1] = 'خمس عشرة';
        $this->_individual[16][1] = 'ست عشرة';
        $this->_individual[17][1] = 'سبع عشرة';
        $this->_individual[18][1] = 'ثماني عشرة';
        $this->_individual[19][1] = 'تسع عشرة';
        $this->_individual[13][2] = 'ثلاثة عشر';
        $this->_individual[14][2] = 'أربعة عشر';
        $this->_individual[15][2] = 'خمسة عشر';
        $this->_individual[16][2] = 'ستة عشر';
        $this->_individual[17][2] = 'سبعة عشر';
        $this->_individual[18][2] = 'ثمانية عشر';
        $this->_individual[19][2] = 'تسعة عشر';
        
        $this->_individual[20][1] = 'عشرون';
        $this->_individual[30][1] = 'ثلاثون';
        $this->_individual[40][1] = 'أربعون';
        $this->_individual[50][1] = 'خمسون';
        $this->_individual[60][1] = 'ستون';
        $this->_individual[70][1] = 'سبعون';
        $this->_individual[80][1] = 'ثمانون';
        $this->_individual[90][1] = 'تسعون';
        $this->_individual[20][2] = 'عشرين';
        $this->_individual[30][2] = 'ثلاثين';
        $this->_individual[40][2] = 'أربعين';
        $this->_individual[50][2] = 'خمسين';
        $this->_individual[60][2] = 'ستين';
        $this->_individual[70][2] = 'سبعين';
        $this->_individual[80][2] = 'ثمانين';
        $this->_individual[90][2] = 'تسعين';
        
        $this->_individual[200][1] = 'مئتان';
        $this->_individual[200][2] = 'مئتين';
        
        $this->_individual[100] = 'مئة';
        $this->_individual[300] = 'ثلاثمئة';
        $this->_individual[400] = 'أربعمئة';
        $this->_individual[500] = 'خمسمئة';
        $this->_individual[600] = 'ستمئة';
        $this->_individual[700] = 'سبعمئة';
        $this->_individual[800] = 'ثمانمئة';
        $this->_individual[900] = 'تسعمئة';
        
        $this->complications[1][1] = 'ألفان';
        $this->complications[1][2] = 'ألفين';
        $this->complications[1][3] = 'آلاف';
        $this->complications[1][4] = 'ألف';
        
        $this->complications[2][1] = 'مليونان';
        $this->complications[2][2] = 'مليونين';
        $this->complications[2][3] = 'ملايين';
        $this->complications[2][4] = 'مليون';
        
        $this->complications[3][1] = 'ملياران';
        $this->complications[3][2] = 'مليارين';
        $this->complications[3][3] = 'مليارات';
        $this->complications[3][4] = 'مليار';
    }
    
    /**
     * Set feminine flag of the counted object
     *      
     * @param integer $value Counted object feminine (1 for masculine & 2 for feminine)
     *      
     * @return boolean TRUE if success, or FALSE if fail
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function setFeminine($value)
    {
        $flag = true;
        
        if ($value == 1 || $value == 2) {
            $this->_feminine = $value;
        } else {
            
            $flag = false;
        }
        
        return $flag;
    }
    
    /**
     * Set the grammar position flag of the counted object
     *      
     * @param integer $value Grammar position of counted object
     *                       (1 if Marfoua & 2 if Mansoub or Majrour)
     *                            
     * @return boolean TRUE if success, or FALSE if fail
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function setFormat($value)
    {
        $flag = true;
        
        if ($value == 1 || $value == 2) {
            $this->_format = $value;
        } else {
            
            $flag = false;
        }
        
        return $flag;
    }
    
    /**
     * Get the feminine flag of counted object
     *      
     * @return integer return current setting of counted object feminine flag
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function getFeminine()
    {
        return $this->_feminine;
    }
    
    /**
     * Get the grammer position flag of counted object
     *      
     * @return integer return current setting of counted object grammer
     *                 position flag
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function getFormat()
    {
        return $this->_format;
    }
    
    /**
     * Spell integer number in Arabic idiom
     *      
     * @param integer $number        The number you want to spell in Arabic idiom
     * @param string  $outputCharset (optional) Output charset [utf-8|windows-1256|iso-8859-6]
     *                               default value is NULL (use set output charset)       
     * @param object  $main          Main Ar-PHP object to access charset converter options
     *                    
     * @return string The Arabic idiom that spells inserted number
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function int2str($number, $outputCharset = null, $main = null)
    {
        $temp = explode('.', $number);

        $string = $this->_int2str($temp[0]);

        if (!empty($temp[1])) {
            $dec = $this->_int2str($temp[1]);
            $string .= ' فاصلة ' . $dec; 
        }
        
        if ($main) {
            if ($outputCharset == null) $outputCharset = $main->getOutputCharset();
            $string = $main->coreConvert($string, 'utf-8', $outputCharset);
        }

        return $string;
    }
    
    /**
     * Spell integer number in Arabic idiom
     *      
     * @param integer $number The number you want to spell in Arabic idiom
     *      
     * @return string The Arabic idiom that spells inserted number
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _int2str($number)
    {
        $blocks = array();
        $items = array();
        $string = '';
        $number = trim((int)$number);

        if ($number > 0) {
            while (strlen($number) > 3) {
                array_push($blocks, substr($number, -3));
                $number = substr($number, 0, strlen($number) - 3);
            }
            array_push($blocks, $number);
            
            $blocks_num = count($blocks) - 1;
  
            for ($i = $blocks_num; $i >= 0; $i--) {
                $number = floor($blocks[$i]);
  
                $text = $this->_writtenBlock($number);
                if ($text) {
                    if ($number == 1 && $i != 0) {
                        $text = $this->complications[$i][4];
                    } elseif ($number == 2 && $i != 0) {
                        $text = $this->complications[$i][$this->_format];
                    } elseif ($number > 2 && $number < 11 && $i != 0) {
                        $text .= ' ' . $this->complications[$i][3];
                    } elseif ($i != 0) {
                        $text .= ' ' . $this->complications[$i][4];
                    }
                    array_push($items, $text);
                }
            }
            
            $string = implode(' و ', $items);
        } else {
            $string = 'صفر';
        }
        return $string;
    }
    
    /**
     * Spell sub block number of three digits max in Arabic idiom
     *      
     * @param integer $number Sub block number of three digits max you want to 
     *                        spell in Arabic idiom
     *                      
     * @return string The Arabic idiom that spells inserted sub block
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _writtenBlock($number)
    {
        $items = array();
        $string = '';
        
        if ($number > 99) {
            $hundred = floor($number / 100) * 100;
            $number = $number % 100;
            
            if ($hundred == 200) {
                array_push($items, $this->_individual[$hundred][$this->_format]);
            } else {
                array_push($items, $this->_individual[$hundred]);
            }
        }
        
        if ($number == 2 || $number == 12) {
            array_push($items, $this->_individual[$number][$this->_feminine][$this->_format]);
        } elseif ($number < 20) {
            array_push($items, $this->_individual[$number][$this->_feminine]);
        } else {
            $ones = $number % 10;
            $tens = floor($number / 10) * 10;
            
            if ($ones == 2) {
                array_push($items, $this->_individual[$ones][$this->_feminine][$this->_format]);
            } elseif ($ones > 0) {
                array_push($items, $this->_individual[$ones][$this->_feminine]);
            }
            
            array_push($items, $this->_individual[$tens][$this->_format]);
        }
        
        $items = array_diff($items, array(''));
        
        $string = implode(' و ', $items);
        
        return $string;
    }
}
?>