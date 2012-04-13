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
 * Class Name: Arabic StrToTime Class
 *  
 * Filename: ArStrToTime.class.php
 *  
 * Original  Author(s): Khaled Al-Sham'aa <khaled.alshamaa@gmail.com>
 *  
 * Purpose:  Parse about any Arabic textual datetime description into a Unix timestamp
 *  
 * ----------------------------------------------------------------------
 *  
 * Arabic StrToTime Class
 *
 * PHP class to parse about any Arabic textual datetime description into a Unix timestamp.
 * 
 * The function expects to be given a string containing an Arabic date format and will 
 * try to parse that format into a Unix timestamp (the number of seconds since January 
 * 1 1970 00:00:00 GMT), relative to the timestamp given in now, or the current 
 * time if none is supplied.
 *          
 * Example:
 * <code>
 *     date_default_timezone_set('UTC');
 *     $time = time();
 * 
 *     echo date('l dS F Y', $time);
 *     echo '<br /><br />';
 * 
 *     include('./Arabic.php');
 *     $Arabic = new Arabic('ArStrToTime');
 * 
 *     $int  = $Arabic->strtotime($str, $time);
 *     $date = date('l dS F Y', $int);
 *     echo "<b><font color=#FFFF00>Arabic String:</font></b> $str<br />";
 *     echo "<b><font color=#FFFF00>Unix Timestamp:</font></b> $int<br />";
 *     echo "<b><font color=#FFFF00>Formated Date:</font></b> $date<br />";    
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
// namespace Arabic/ArStrToTime;

/**
 * This PHP class parse about any Arabic textual datetime description into a 
 * Unix timestamp
 *  
 * @category  Text 
 * @package   Arabic
 * @author    Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
 * @copyright 2009 Khaled Al-Shamaa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */ 
class ArStrToTime
{
    private static $_hj = array('محرم', 'صفر', 'ربيع الأول', 'ربيع الثاني', 'جمادى الأولى', 'جمادى الثانية',
                               'رجب', 'شعبان', 'رمضان', 'شوال', 'ذو القعدة', 'ذو الحجة');
    
    private static $_patterns = array('أ', 'إ', 'آ', 'ة', 'بعد', 'تالي', 'لاحق', 'قادم', 'سابق', 'فائت',
                                     'ماضي', 'منذ', 'قبل', 'يوم', 'ايام', 'ساعه', 'ساعتان', 'ساعتين', 'ساعات', 'دقيقه',
                                     'دقيقتان', 'دقيقتين', 'دقائق', 'ثانيه', 'ثانيتين', 'ثانيتان', 'ثواني', 'اسبوعين', 'اسبوعان', 'اسابيع',
                                     'اسبوع', 'شهرين', 'شهران', 'اشهر', 'شهور', 'شهر', 'سنه', 'سنتين', 'سنتان', 'سنوات',
                                     'سنين', 'صباحا', 'فجرا', 'قبل الظهر', 'مساء', 'عصرا', 'بعد الظهر', 'ليلا', 'غد', 'بارحة',
                                     'أمس', 'مضت', 'مضى', 'هذا', 'هذه', 'الآن', 'لحظه', 'اول', 'ثالث', 'رابع', 'خامس',
                                     'سادس', 'سابع', 'ثامن', 'تاسع', 'عاشر', 'حادي عشر', 'حاديه عشر', 'ثاني عشر', 'ثانيه عشر', 'سبت',
                                     'احد', 'اثنين', 'ثلاثاء', 'اربعاء', 'خميس', 'جمعه', 'ثلاث', 'اربع', 'خمس', 'ست',
                                     'سبع', 'ثمان', 'تسع', 'عشر', 'كانون ثاني', 'شباط', 'اذار', 'نيسان', 'ايار', 'حزيران',
                                     'تموز', 'اب', 'ايلول', 'تشرين اول', 'تشرين ثاني', 'كانون اول', 'يناير', 'فبراير', 'مارس', 'ابريل',
                                     'مايو', 'يونيو', 'يوليو', 'اغسطس', 'سبتمبر', 'اكتوبر', 'نوفمبر', 'ديسمبر');

    private static $_replacements = array('ا', 'ا', 'ا', 'ه', 'next', 'next', 'next', 'next', 'last', 'last',
                                         'last', '-', '-', 'day', 'days', 'hour', '2 hours', '2 hours', 'hours', 'minute',
                                         '2 minutes', '2 minutes', 'minutes', 'second', '2 seconds', '2 seconds', 'seconds', '2 weeks', '2 weeks', 'weeks',
                                         'week', '2 months', '2 months', 'months', 'months', 'month', 'year', '2 years', '2 years', 'years',
                                         'years', 'am', 'am', 'am', 'pm', 'pm', 'pm', 'pm', 'tomorrow', 'yesterday',
                                         'yesterday', 'ago', 'ago', 'this', 'this', 'now', 'now', 'first', 'third', 'fourth', 'fifth',
                                         'sixth', 'seventh', 'eighth', 'ninth', 'tenth', 'eleventh', 'eleventh', 'twelfth', 'twelfth', 'saturday',
                                         'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', '3', '4', '5', '6',
                                         '7', '8', '9', '10', 'january', 'february', 'march', 'april', 'may', 'june',
                                         'july', 'august', 'september', 'october', 'november', 'december', 'january', 'february', 'march', 'april',
                                         'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december');

    /**
     * This method will parse about any Arabic textual datetime description into 
     * a Unix timestamp
     *          
     * @param string  $text         The string to parse, according to the GNU » Date Input Formats syntax (in Arabic).
     * @param integer $now          The timestamp used to calculate the returned value.       
     * @param string  $inputCharset (optional) Input charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set input charset)       
     * @param object  $main         Main Ar-PHP object to access charset converter options
     *                    
     * @return Integer Returns a timestamp on success, FALSE otherwise
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public static function strtotime($text, $now, $inputCharset = null, $main = null)
    {
        if ($main) {
            if ($inputCharset == null) $inputCharset = $main->getInputCharset();
            $text = $main->coreConvert($text, $inputCharset, 'utf-8');
        }
        
        $int = 0;

        for ($i=0; $i<12; $i++) {
            if (strpos($text, self::$_hj[$i]) > 0) {
                preg_match('/.*(\d{1,2}).*(\d{4}).*/', $text, $matches);

                include_once 'ArMktime.class.php';
                $temp = new ArMktime();
                $int = $temp->mktime(0, 0, 0, $i+1, $matches[1], $matches[2]);
                $temp = null;

                break;
            }
        }

        if ($int == 0) {
            $patterns = array();
            $replacements = array();
  
            array_push($patterns, '/َ|ً|ُ|ٌ|ِ|ٍ|ْ|ّ/');
            array_push($replacements, '');
  
            array_push($patterns, '/\s*ال(\S{3,})\s+ال(\S{3,})/');
            array_push($replacements, ' \\2 \\1');
  
            array_push($patterns, '/\s*ال(\S{3,})/');
            array_push($replacements, ' \\1');
  
            $text = preg_replace($patterns, $replacements, $text);
  
            $text = str_replace(self::$_patterns, self::$_replacements, $text);
  
            $text = preg_replace('/[ابتثجحخدذرزسشصضطظعغفقكلمنهوي]/', '', $text);
  
            $int = strtotime($text, $now);
        }
        
        return $int;
    }
}
?>
