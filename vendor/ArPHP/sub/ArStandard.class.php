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
 * Class Name: Arabic Text ArStandard Class
 *  
 * Filename: ArStandard.class.php
 *  
 * Original  Author(s): Khaled Al-Sham'aa <khaled.alshamaa@gmail.com>
 *  
 * Purpose:  Standardize Arabic text
 *  
 * ----------------------------------------------------------------------
 *  
 * Arabic Text Standardize Class
 *
 * @desc PHP class to standarize Arabic text
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
// namespace Arabic/ArStandard;

/**
 * This PHP class standardize Arabic text
 *  
 * @category  Text 
 * @package   Arabic
 * @author    Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
 * @copyright 2009 Khaled Al-Shamaa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */ 
class ArStandard
{
    /**
     * This method will standardize Arabic text to follow writing standards 
     * (just like magazine rules)
     *          
     * @param string $text          Arabic text you would like to standardize
     * @param string $inputCharset  (optional) Input charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set input charset)       
     * @param string $outputCharset (optional) Output charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set output charset)       
     * @param object $main          Main Ar-PHP object to access charset converter options
     *                    
     * @return String Standardized version of input Arabic text
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function standard($text, $inputCharset = null, $outputCharset = null, $main = null)
    {
        if ($main) {
            if ($inputCharset == null) $inputCharset = $main->getInputCharset();
            $text = $main->coreConvert($text, $inputCharset, 'utf-8');
        }

        $patterns = array();
        $replacements = array();
        
        /*
          النقطة، الفاصلة، الفاصلة المنقوطة، النقطتان، 
          علامتي الاستفهام والتعجب، النقاط الثلاث المتتالية 
          يترك فراغ واحد بعدها جميعا دون أي فراغ قبلها
        */
        array_push($patterns, '/\s*([\.\،\؛\:\!\؟])\s*/u');
        array_push($replacements, '\\1 ');

        /*
          النقاط المتتالية عددها 3 فقط 
          (ليست نقطتان وليست أربع أو أكثر)
        */
        array_push($patterns, '/(\. ){2,}/u');
        array_push($replacements, '...');

        /*
          الأقواس ( ) [ ] { } يترك قبلها وبعدها فراغ 
          وحيد، فيما لا يوجد بينها وبين ما بداخلها أي فراغ
        */
        array_push($patterns, '/\s*([\(\{\[])\s*/u');
        array_push($replacements, ' \\1');

        array_push($patterns, '/\s*([\)\}\]])\s*/u');
        array_push($replacements, '\\1 ');

        /*
          علامات الاقتباس "..." يترك قبلها وبعدها فراغ 
          وحيد، فيما لا يوجد بينها وبين ما بداخلها أي فراغ
        */
        array_push($patterns, '/\s*\"\s*(.+)((?<!\s)\"|\s+\")\s*/u');
        array_push($replacements, ' "\\1" ');

        /*
          علامات الإعتراض -...- يترك قبلها وبعدها فراغ 
          وحيد، فيما لا يوجد بينها وبين ما بداخلها أي فراغ
        */
        array_push($patterns, '/\s*\-\s*(.+)((?<!\s)\-|\s+\-)\s*/u');
        array_push($replacements, ' -\\1- ');

        /*
          لا يترك فراغ بين حرف العطف الواو وبين 
          الكلمة التي تليه إلا إن كانت تبدأ بحرف الواو
        */
        array_push($patterns, '/\sو\s+([^و])/u');
        array_push($replacements, ' و\\1');

        /*
          الواحدات الإنجليزية توضع على يمين الرقم مع ترك فراغ
        */
        array_push($patterns, '/\s+(\w+)\s*(\d+)\s+/');
        array_push($replacements, ' <span dir="ltr">\\2 \\1</span> ');

        array_push($patterns, '/\s+(\d+)\s*(\w+)\s+/');
        array_push($replacements, ' <span dir="ltr">\\1 \\2</span> ');

        /*
          النسبة المؤية دائما إلى يسار الرقم وبدون أي فراغ يفصل بينهما 40% مثلا
        */
        array_push($patterns, '/\s+(\d+)\s*\%\s+/u');
        array_push($replacements, ' %\\1 ');

        $text = preg_replace($patterns, $replacements, $text);

        if ($main) {
            if ($outputCharset == null) $outputCharset = $main->getOutputCharset();
            $text = $main->coreConvert($text, 'utf-8', $outputCharset);
        }
        
        return $text;
    }
}
?>