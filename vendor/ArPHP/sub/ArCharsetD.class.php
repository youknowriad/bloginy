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
 * Class Name: Detect Arabic String Character Set
 *  
 * Filename:   ArCharsetD.class.php
 *  
 * Original    Author(s): Khaled Al-Sham'aa <khaled.alshamaa@gmail.com>
 *  
 * Purpose:    This class will return Arabic character set that used for
 *             a given Arabic string passing into this class, those available
 *             character sets that can be detected by this class includes
 *             the most popular three: Windows-1256, ISO 8859-6, and UTF-8.
 *              
 * ----------------------------------------------------------------------
 *  
 * Detect Arabic String Character Set
 *
 * The last step of the Information Retrieval process is to display the found 
 * documents to the user. However, some difficulties might occur at that point. 
 * English texts are usually written in the ASCII standard. Unlike the English 
 * language, many languages have different character sets, and do not have one 
 * standard. This plurality of standards causes problems, especially in a web 
 * environment.
 *
 * This PHP class will return Arabic character set that used for a given
 * Arabic string passing into this class, those available character sets that can
 * be detected by this class includes the most popular three: Windows-1256,
 * ISO 8859-6, and UTF-8.
 *
 * Example:
 * <code>
 *   include('./Arabic.php');
 *   $Arabic = new Arabic('ArCharsetD');
 * 
 *   $charset = $Arabic->ArCharsetD->getCharset($text);    
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
// namespace Arabic/ArCharsetD;

/**
 * This PHP class detect Arabic string character set
 *  
 * @category  Text 
 * @package   Arabic
 * @author    Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
 * @copyright 2009 Khaled Al-Shamaa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org
 */ 
class ArCharsetD
{
    /**
     * Count number of hits for the most frequented letters in Arabic language 
     * (Alef, Lam and Yaa), then calculate association ratio with each of 
     * possible character set (UTF-8, Windows-1256 and ISO-8859-6)
     *                           
     * @param String $string Arabic string in unknown format
     *      
     * @return Array Character set as key and string association ratio as value
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function guess($string)
    {
        $charset['windows-1256'] = substr_count($string, 'Ç');
        $charset['windows-1256'] += substr_count($string, 'á');
        $charset['windows-1256'] += substr_count($string, 'í');
        
        $charset['iso-8859-6'] = substr_count($string, 'Ç');
        $charset['iso-8859-6'] += substr_count($string, 'ä');
        $charset['iso-8859-6'] += substr_count($string, 'ê');
        
        $charset['utf-8'] = substr_count($string, 'Ø§');
        $charset['utf-8'] += substr_count($string, 'Ù„');
        $charset['utf-8'] += substr_count($string, 'ÙŠ');
        
        $total = $charset['windows-1256'] + $charset['iso-8859-6'] + $charset['utf-8'];
        
        $charset['windows-1256'] = round($charset['windows-1256'] * 100 / $total);
        $charset['iso-8859-6'] = round($charset['iso-8859-6'] * 100 / $total);
        $charset['utf-8'] = round($charset['utf-8'] * 100 / $total);
        
        return $charset;
    }
    
    /**
     * Find the most possible character set for given Arabic string in unknown 
     * format
     *         
     * @param String $string Arabic string in unknown format
     *      
     * @return String The most possible character set for given Arabic string in
     *                unknown format[utf-8|windows-1256|iso-8859-6]
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function getCharset($string)
    {
        if (preg_match('/<meta .* charset=([^\"]+)".*>/sim', $string, $matches)) {
            $value = $matches[1];
        } else {
            $charset = $this->guess($string);
            arsort($charset);
            $value = key($charset);
        }

        return $value;
    }
}
?>