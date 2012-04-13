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
 * Class Name: Arabic Date
 *  
 * Filename:   ArDate.class.php
 *  
 * Original    Author(s): Khaled Al-Sham'aa <khaled.alshamaa@gmail.com>
 *  
 * Purpose:    Arabic customization for PHP date function
 *  
 * ----------------------------------------------------------------------
 *  
 * Arabic Date
 *
 * PHP class for Arabic and Islamic customization of PHP date function. It
 * can convert UNIX timestamp into string in Arabic as well as convert it into
 * Hijri calendar
 * 
 * The Islamic Calendar:
 * 
 * The Islamic calendar is purely lunar and consists of twelve alternating months 
 * of 30 and 29 days, with the final 29 day month extended to 30 days during leap 
 * years. Leap years follow a 30 year cycle and occur in years 1, 5, 7, 10, 13, 16, 
 * 18, 21, 24, 26, and 29. The calendar begins on Friday, July 16th, 622 C.E. in 
 * the Julian calendar, Julian day 1948439.5, the day of Muhammad's separate from 
 * Mecca to Medina, the first day of the first month of year 1 A.H.--"Anno Hegira".
 * 
 * Each cycle of 30 years thus contains 19 normal years of 354 days and 11 leap 
 * years of 355, so the average length of a year is therefore 
 * ((19 x 354) + (11 x 355)) / 30 = 354.365... days, with a mean length of month of 
 * 1/12 this figure, or 29.53055... days, which closely approximates the mean 
 * synodic month (time from new Moon to next new Moon) of 29.530588 days, with the 
 * calendar only slipping one day with respect to the Moon every 2525 years. Since 
 * the calendar is fixed to the Moon, not the solar year, the months shift with 
 * respect to the seasons, with each month beginning about 11 days earlier in each 
 * successive solar year.
 * 
 * The convert presented here is the most commonly used civil calendar in the Islamic 
 * world; for religious purposes months are defined to start with the first 
 * observation of the crescent of the new Moon.
 * 
 * The Julian Calendar:
 * 
 * The Julian calendar was proclaimed by Julius Casar in 46 B.C. and underwent 
 * several modifications before reaching its final form in 8 C.E. The Julian 
 * calendar differs from the Gregorian only in the determination of leap years, 
 * lacking the correction for years divisible by 100 and 400 in the Gregorian 
 * calendar. In the Julian calendar, any positive year is a leap year if divisible 
 * by 4. (Negative years are leap years if when divided by 4 a remainder of 3 
 * results.) Days are considered to begin at midnight.
 * 
 * In the Julian calendar the average year has a length of 365.25 days. compared to 
 * the actual solar tropical year of 365.24219878 days. The calendar thus 
 * accumulates one day of error with respect to the solar year every 128 years. 
 * Being a purely solar calendar, no attempt is made to synchronise the start of 
 * months to the phases of the Moon.
 * 
 * The Gregorian Calendar:
 * 
 * The Gregorian calendar was proclaimed by Pope Gregory XIII and took effect in 
 * most Catholic states in 1582, in which October 4, 1582 of the Julian calendar 
 * was followed by October 15 in the new calendar, correcting for the accumulated 
 * discrepancy between the Julian calendar and the equinox as of that date. When 
 * comparing historical dates, it's important to note that the Gregorian calendar, 
 * used universally today in Western countries and in international commerce, was 
 * adopted at different times by different countries. Britain and her colonies 
 * (including what is now the United States), did not switch to the Gregorian 
 * calendar until 1752, when Wednesday 2nd September in the Julian calendar dawned 
 * as Thursday the 14th in the Gregorian.
 * 
 * The Gregorian calendar is a minor correction to the Julian. In the Julian 
 * calendar every fourth year is a leap year in which February has 29, not 28 days, 
 * but in the Gregorian, years divisible by 100 are not leap years unless they are 
 * also divisible by 400. How prescient was Pope Gregory! Whatever the problems of 
 * Y2K, they won't include sloppy programming which assumes every year divisible by 
 * 4 is a leap year since 2000, unlike the previous and subsequent years divisible 
 * by 100, is a leap year. As in the Julian calendar, days are considered to begin 
 * at midnight.
 * 
 * The average length of a year in the Gregorian calendar is 365.2425 days compared 
 * to the actual solar tropical year (time from equinox to equinox) of 365.24219878 
 * days, so the calendar accumulates one day of error with respect to the solar year 
 * about every 3300 years. As a purely solar calendar, no attempt is made to 
 * synchronise the start of months to the phases of the Moon.
 * 
 * date -- Format a local time/date
 * string date ( string format, int timestamp);
 * 
 * Returns a string formatted according to the given format string using the given 
 * integer timestamp or the current local time if no timestamp is given. In 
 * otherwords, timestamp is optional and defaults to the value of time().
 * 
 * Example:
 * <code>
 *   date_default_timezone_set('UTC');
 *   $time = time();
 *   
 *   echo date('l dS F Y h:i:s A', $time);
 *   echo '<br /><br />';
 *   
 *   include('./Arabic.php');
 *   $Ar = new Arabic('ArDate');
 *   
 *   echo $Ar->date('l dS F Y h:i:s A', $time);
 *   echo '<br /><br />';
 *   
 *   $Ar->ArDate->setMode(2);
 *   echo $Ar->date('l dS F Y h:i:s A', $time);
 *   echo '<br /><br />';
 *   
 *   $Ar->ArDate->setMode(3);
 *   echo $Ar->date('l dS F Y h:i:s A', $time);
 *   echo '<br /><br />';
 *   
 *   $Ar->ArDate->setMode(4);
 *   echo $Ar->date('l dS F Y h:i:s A', $time);    
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
// namespace Arabic/ArDate;

/**
 * This PHP class is an Arabic customization for PHP date function
 *  
 * @category  Text 
 * @package   Arabic
 * @author    Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
 * @copyright 2009 Khaled Al-Shamaa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */ 
class ArDate
{
    private $_mode = 1;
    private static $_ISLAMIC_EPOCH = 1948439.5;
    
    /**
     * Setting value for $mode scalar
     *      
     * @param integer $mode Output mode of date function where:
     *                       1) Hegri format (Islamic calendar)
     *                       2) Arabic month names used in Middle East countries
     *                       3) Arabic Transliteration of Gregorian month names
     *                       4) Both of 2 and 3 formats together
     *                       5) Libyan way
     *                                   
     * @return boolean TRUE if success, or FALSE if fail
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function setMode($mode = 1)
    {
        $flag = true;
        
        $mode = (int) $mode;
        
        if ($mode > 0 && $mode < 6) {
            $this->_mode = $mode;
        } else {
            
            $flag = false;
        }
        
        return $flag;
    }
    
    /**
     * Getting $mode value that refer to output mode format
     *               1) Hegri format (Islamic calendar)
     *               2) Arabic month names used in Middle East countries
     *               3) Arabic Transliteration of Gregorian month names
     *               4) Both of 2 and 3 formats together
     *               5) Libyan way
     *                           
     * @return Integer Value of $mode properity
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function getMode()
    {
        return $this->_mode;
    }
    
    /**
     * Format a local time/date in Arabic string
     *      
     * @param string  $format        Format string (same as PHP date function)
     * @param integer $timestamp     Unix timestamp
     * @param string  $outputCharset (optional) Output charset [utf-8|windows-1256|iso-8859-6]
     *                               default value is NULL (use set output charset)       
     * @param integer $correction    To apply correction factor (+/- 1-2) to standard hijri calendar
     * @param object  $main          Main Ar-PHP object to access charset converter options
     *                    
     * @return string Format Arabic date string according to given format string
     *                using the given integer timestamp or the current local
     *                time if no timestamp is given.
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function date($format, $timestamp, $outputCharset = null, $correction = 0, $main = null)
    {
        $timestamp = $timestamp  + 3600*24*$correction;
        
        if ($this->_mode == 1) {
            $hj_txt_month[1] = 'محرم';
            $hj_txt_month[2] = 'صفر';
            $hj_txt_month[3] = 'ربيع الأول';
            $hj_txt_month[4] = 'ربيع الثاني';
            $hj_txt_month[5] = 'جمادى الأولى';
            $hj_txt_month[6] = 'جمادى الثانية';
            $hj_txt_month[7] = 'رجب';
            $hj_txt_month[8] = 'شعبان';
            $hj_txt_month[9] = 'رمضان';
            $hj_txt_month[10] = 'شوال';
            $hj_txt_month[11] = 'ذو القعدة';
            $hj_txt_month[12] = 'ذو الحجة';
            
            $patterns = array();
            $replacements = array();
            
            array_push($patterns, 'Y');
            array_push($replacements, 'x1');
            array_push($patterns, 'y');
            array_push($replacements, 'x2');
            array_push($patterns, 'M');
            array_push($replacements, 'x3');
            array_push($patterns, 'F');
            array_push($replacements, 'x3');
            array_push($patterns, 'n');
            array_push($replacements, 'x4');
            array_push($patterns, 'm');
            array_push($replacements, 'x5');
            array_push($patterns, 'j');
            array_push($replacements, 'x6');
            array_push($patterns, 'd');
            array_push($replacements, 'x7');
            
            $format = str_replace($patterns, $replacements, $format);
            
            $str = date($format, $timestamp);
            $str = $this->_en2ar($str);
            
            list($Y, $M, $D) = split(' ', date('Y m d', $timestamp));
            
            list($hj_y, $hj_m, $hj_d) = $this->_hjConvert($Y, $M, $D);
            
            $patterns = array();
            $replacements = array();
            
            array_push($patterns, 'x1');
            array_push($replacements, $hj_y);
            array_push($patterns, 'x2');
            array_push($replacements, substr($hj_y, -2));
            array_push($patterns, 'x3');
            array_push($replacements, $hj_txt_month[$hj_m]);
            array_push($patterns, 'x4');
            array_push($replacements, $hj_m);
            array_push($patterns, 'x5');
            array_push($replacements, sprintf('%02d', $hj_m));
            array_push($patterns, 'x6');
            array_push($replacements, $hj_d);
            array_push($patterns, 'x7');
            array_push($replacements, sprintf('%02d', $hj_d));
            
            $str = str_replace($patterns, $replacements, $str);
        } elseif ($this->_mode == 5) {
            $year = date('Y', $timestamp);
            $year -= 632;
            $yr = substr("$year", -2);
            
            $format = str_replace('Y', $year, $format);
            $format = str_replace('y', $yr, $format);
            
            $str = date($format, $timestamp);
            $str = $this->_en2ar($str);

        } else {
            $str = date($format, $timestamp);
            $str = $this->_en2ar($str);
        }
        
        if ($main) {
            if ($outputCharset == null) $outputCharset = $main->getOutputCharset();
            $str = $main->coreConvert($str, 'utf-8', $outputCharset);
        }

        return $str;
    }
    
    /**
     * Translate English date/time terms into Arabic langauge
     *      
     * @param string $str Date/time string using English terms
     *      
     * @return string Date/time string using Arabic terms
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _en2ar($str)
    {
        $patterns = array();
        $replacements = array();
        
        $str = strtolower($str);
        
        array_push($patterns, 'saturday');
        array_push($replacements, 'السبت');
        array_push($patterns, 'sunday');
        array_push($replacements, 'الأحد');
        array_push($patterns, 'monday');
        array_push($replacements, 'الاثنين');
        array_push($patterns, 'tuesday');
        array_push($replacements, 'الثلاثاء');
        array_push($patterns, 'wednesday');
        array_push($replacements, 'الأربعاء');
        array_push($patterns, 'thursday');
        array_push($replacements, 'الخميس');
        array_push($patterns, 'friday');
        array_push($replacements, 'الجمعة');
        
        if ($this->_mode == 2) {
            array_push($patterns, 'january');
            array_push($replacements, 'كانون ثاني');
            array_push($patterns, 'february');
            array_push($replacements, 'شباط');
            array_push($patterns, 'march');
            array_push($replacements, 'آذار');
            array_push($patterns, 'april');
            array_push($replacements, 'نيسان');
            array_push($patterns, 'may');
            array_push($replacements, 'أيار');
            array_push($patterns, 'june');
            array_push($replacements, 'حزيران');
            array_push($patterns, 'july');
            array_push($replacements, 'تموز');
            array_push($patterns, 'august');
            array_push($replacements, 'آب');
            array_push($patterns, 'september');
            array_push($replacements, 'أيلول');
            array_push($patterns, 'october');
            array_push($replacements, 'تشرين أول');
            array_push($patterns, 'november');
            array_push($replacements, 'تشرين ثاني');
            array_push($patterns, 'december');
            array_push($replacements, 'كانون أول');
        } elseif ($this->_mode == 3) {
            array_push($patterns, 'january');
            array_push($replacements, 'يناير');
            array_push($patterns, 'february');
            array_push($replacements, 'فبراير');
            array_push($patterns, 'march');
            array_push($replacements, 'مارس');
            array_push($patterns, 'april');
            array_push($replacements, 'أبريل');
            array_push($patterns, 'may');
            array_push($replacements, 'مايو');
            array_push($patterns, 'june');
            array_push($replacements, 'يونيو');
            array_push($patterns, 'july');
            array_push($replacements, 'يوليو');
            array_push($patterns, 'august');
            array_push($replacements, 'أغسطس');
            array_push($patterns, 'september');
            array_push($replacements, 'سبتمبر');
            array_push($patterns, 'october');
            array_push($replacements, 'أكتوبر');
            array_push($patterns, 'november');
            array_push($replacements, 'نوفمبر');
            array_push($patterns, 'december');
            array_push($replacements, 'ديسمبر');
        } elseif ($this->_mode == 4) {
            array_push($patterns, 'january');
            array_push($replacements, 'كانون ثاني/يناير');
            array_push($patterns, 'february');
            array_push($replacements, 'شباط/فبراير');
            array_push($patterns, 'march');
            array_push($replacements, 'آذار/مارس');
            array_push($patterns, 'april');
            array_push($replacements, 'نيسان/أبريل');
            array_push($patterns, 'may');
            array_push($replacements, 'أيار/مايو');
            array_push($patterns, 'june');
            array_push($replacements, 'حزيران/يونيو');
            array_push($patterns, 'july');
            array_push($replacements, 'تموز/يوليو');
            array_push($patterns, 'august');
            array_push($replacements, 'آب/أغسطس');
            array_push($patterns, 'september');
            array_push($replacements, 'أيلول/سبتمبر');
            array_push($patterns, 'october');
            array_push($replacements, 'تشرين أول/أكتوبر');
            array_push($patterns, 'november');
            array_push($replacements, 'تشرين ثاني/نوفمبر');
            array_push($patterns, 'december');
            array_push($replacements, 'كانون أول/ديسمبر');
        } elseif ($this->_mode == 5) {
            array_push($patterns, 'january');
            array_push($replacements, 'أي النار');
            array_push($patterns, 'february');
            array_push($replacements, 'النوار');
            array_push($patterns, 'march');
            array_push($replacements, 'الربيع');
            array_push($patterns, 'april');
            array_push($replacements, 'الطير');
            array_push($patterns, 'may');
            array_push($replacements, 'الماء');
            array_push($patterns, 'june');
            array_push($replacements, 'الصيف');
            array_push($patterns, 'july');
            array_push($replacements, 'ناصر');
            array_push($patterns, 'august');
            array_push($replacements, 'هانيبال');
            array_push($patterns, 'september');
            array_push($replacements, 'الفاتح');
            array_push($patterns, 'october');
            array_push($replacements, 'التمور');
            array_push($patterns, 'november');
            array_push($replacements, 'الحرث');
            array_push($patterns, 'december');
            array_push($replacements, 'الكانون');
        }
        
        array_push($patterns, 'sat');
        array_push($replacements, 'السبت');
        array_push($patterns, 'sun');
        array_push($replacements, 'الأحد');
        array_push($patterns, 'mon');
        array_push($replacements, 'الاثنين');
        array_push($patterns, 'tue');
        array_push($replacements, 'الثلاثاء');
        array_push($patterns, 'wed');
        array_push($replacements, 'الأربعاء');
        array_push($patterns, 'thu');
        array_push($replacements, 'الخميس');
        array_push($patterns, 'fri');
        array_push($replacements, 'الجمعة');
        
        if ($this->_mode == 2) {
            array_push($patterns, 'jan');
            array_push($replacements, 'كانون ثاني');
            array_push($patterns, 'feb');
            array_push($replacements, 'شباط');
            array_push($patterns, 'mar');
            array_push($replacements, 'آذار');
            array_push($patterns, 'apr');
            array_push($replacements, 'نيسان');
            array_push($patterns, 'may');
            array_push($replacements, 'أيار');
            array_push($patterns, 'jun');
            array_push($replacements, 'حزيران');
            array_push($patterns, 'jul');
            array_push($replacements, 'تموز');
            array_push($patterns, 'aug');
            array_push($replacements, 'آب');
            array_push($patterns, 'sep');
            array_push($replacements, 'أيلول');
            array_push($patterns, 'oct');
            array_push($replacements, 'تشرين أول');
            array_push($patterns, 'nov');
            array_push($replacements, 'تشرين ثاني');
            array_push($patterns, 'dec');
            array_push($replacements, 'كانون أول');
        } elseif ($this->_mode == 3) {
            array_push($patterns, 'jan');
            array_push($replacements, 'يناير');
            array_push($patterns, 'feb');
            array_push($replacements, 'فبراير');
            array_push($patterns, 'mar');
            array_push($replacements, 'مارس');
            array_push($patterns, 'apr');
            array_push($replacements, 'أبريل');
            array_push($patterns, 'may');
            array_push($replacements, 'مايو');
            array_push($patterns, 'jun');
            array_push($replacements, 'يونيو');
            array_push($patterns, 'jul');
            array_push($replacements, 'يوليو');
            array_push($patterns, 'aug');
            array_push($replacements, 'أغسطس');
            array_push($patterns, 'sep');
            array_push($replacements, 'سبتمبر');
            array_push($patterns, 'oct');
            array_push($replacements, 'أكتوبر');
            array_push($patterns, 'nov');
            array_push($replacements, 'نوفمبر');
            array_push($patterns, 'dec');
            array_push($replacements, 'ديسمبر');
        } elseif ($this->_mode == 4) {
            array_push($patterns, 'jan');
            array_push($replacements, 'كانون ثاني/يناير');
            array_push($patterns, 'feb');
            array_push($replacements, 'شباط/فبراير');
            array_push($patterns, 'mar');
            array_push($replacements, 'آذار/مارس');
            array_push($patterns, 'apr');
            array_push($replacements, 'نيسان/أبريل');
            array_push($patterns, 'may');
            array_push($replacements, 'أيار/مايو');
            array_push($patterns, 'jun');
            array_push($replacements, 'حزيران/يونيو');
            array_push($patterns, 'jul');
            array_push($replacements, 'تموز/يوليو');
            array_push($patterns, 'aug');
            array_push($replacements, 'آب/أغسطس');
            array_push($patterns, 'sep');
            array_push($replacements, 'أيلول/سبتمبر');
            array_push($patterns, 'oct');
            array_push($replacements, 'تشرين أول/أكتوبر');
            array_push($patterns, 'nov');
            array_push($replacements, 'تشرين ثاني/نوفمبر');
            array_push($patterns, 'dec');
            array_push($replacements, 'كانون أول/ديسمبر');
        } elseif ($this->_mode == 5) {
            array_push($patterns, 'jan');
            array_push($replacements, 'أي النار');
            array_push($patterns, 'feb');
            array_push($replacements, 'النوار');
            array_push($patterns, 'mar');
            array_push($replacements, 'الربيع');
            array_push($patterns, 'apr');
            array_push($replacements, 'الطير');
            array_push($patterns, 'may');
            array_push($replacements, 'الماء');
            array_push($patterns, 'jun');
            array_push($replacements, 'الصيف');
            array_push($patterns, 'jul');
            array_push($replacements, 'ناصر');
            array_push($patterns, 'aug');
            array_push($replacements, 'هانيبال');
            array_push($patterns, 'sep');
            array_push($replacements, 'الفاتح');
            array_push($patterns, 'oct');
            array_push($replacements, 'التمور');
            array_push($patterns, 'nov');
            array_push($replacements, 'الحرث');
            array_push($patterns, 'dec');
            array_push($replacements, 'الكانون');
        }
        
        array_push($patterns, 'am');
        array_push($replacements, 'صباحاً');
        array_push($patterns, 'pm');
        array_push($replacements, 'مساءً');
        
        array_push($patterns, 'st');
        array_push($replacements, '');
        array_push($patterns, 'nd');
        array_push($replacements, '');
        array_push($patterns, 'rd');
        array_push($replacements, '');
        array_push($patterns, 'th');
        array_push($replacements, '');
        
        $str = str_replace($patterns, $replacements, $str);
        
        return $str;
    }
    
    /**
     * Convert given Gregorian date into Higri date
     *      
     * @param integer $Y Year Gregorian year
     * @param integer $M Month Gregorian month
     * @param integer $D Day Gregorian day
     *      
     * @return array Higri date [int Year, int Month, int Day](Islamic calendar)
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _hjConvert($Y, $M, $D)
    {
        // To get these functions to work, you have to compile PHP with --enable-calendar 
        // http://www.php.net/manual/en/calendar.installation.php
        // $jd = GregorianToJD($M, $D, $Y);
        
        $jd = $this->_gregToJd($M, $D, $Y);
        
        list($year, $month, $day) = $this->_jdToIslamic($jd);
        
        return array($year, $month, $day);
    }
    
    /**
     * Convert given Julian day into Higri date
     *      
     * @param integer $jd Julian day
     *      
     * @return array Higri date [int Year, int Month, int Day](Islamic calendar)
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _jdToIslamic($jd)
    {
        $jd = (int)$jd + 0.5;
        $year = ((30 * ($jd - self::$_ISLAMIC_EPOCH)) + 10646) / 10631;
        $year = (int)$year;
        $month = min(12, ceil(($jd - (29 + $this->_islamicToJd($year, 1, 1))) / 29.5) + 1);
        $day = ($jd - $this->_islamicToJd($year, $month, 1)) + 1;
        
        return array($year, $month, $day);
    }
    
    /**
     * Convert given Higri date into Julian day
     *      
     * @param integer $year  Year Higri year
     * @param integer $month Month Higri month
     * @param integer $day   Day Higri day
     *      
     * @return integer Julian day
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _islamicToJd($year, $month, $day)
    {
        return($day + ceil(29.5 * ($month - 1)) + ($year - 1) * 354 + (int)((3 + (11 * $year)) / 30) + self::$_ISLAMIC_EPOCH) - 1;
    }
    
    /**
     * Converts a Gregorian date to Julian Day Count
     *      
     * @param integer $m The month as a number from 1 (for January) to 12 (for December) 
     * @param integer $d The day as a number from 1 to 31
     * @param integer $y The year as a number between -4714 and 9999
     *       
     * @return integer The julian day for the given gregorian date as an integer
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _gregToJd ($m, $d, $y)
    {
        if ($m > 2) {
            $m = $m - 3;
        } else {
            $m = $m + 9; 
            $y = $y - 1;
        }
        
        $c = $y / 100; 
        $ya = $y - 100 * $c;
        $jd = (146097 * $c) / 4 + (1461 * $ya) / 4 + (153 * $m + 2) / 5 + $d + 1721119;
        
        return round($jd);
    }
}
?>
