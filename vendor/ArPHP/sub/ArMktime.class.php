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
 * Class Name: Arabic Maketime
 *  
 * Filename:   ArMktime.class.php
 *  
 * Original    Author(s): Khaled Al-Sham'aa <khaled.alshamaa@gmail.com>
 *  
 * Purpose:    Arabic customization for PHP mktime function
 *  
 * ----------------------------------------------------------------------
 *  
 * Arabic Maketime
 *
 * PHP class for Arabic and Islamic customization of PHP mktime function.
 * It can convert Hijri date into UNIX timestamp format
 *
 * Unix time() value:
 * 
 * Development of the Unix operating system began at Bell Laboratories in 1969 by 
 * Dennis Ritchie and Ken Thompson, with the first PDP-11 version becoming 
 * operational in February 1971. Unix wisely adopted the convention that all 
 * internal dates and times (for example, the time of creation and last modification 
 * of files) were kept in Universal Time, and converted to local time based on a 
 * per-user time zone specification. This far-sighted choice has made it vastly 
 * easier to integrate Unix systems into far-flung networks without a chaos of 
 * conflicting time settings.
 * 
 * The machines on which Unix was developed and initially deployed could not support 
 * arithmetic on integers longer than 32 bits without costly multiple-precision 
 * computation in software. The internal representation of time was therefore chosen 
 * to be the number of seconds elapsed since 00:00 Universal time on January 1, 1970 
 * in the Gregorian calendar (Julian day 2440587.5), with time stored as a 32 bit 
 * signed integer (long in the original C implementation).
 * 
 * The influence of Unix time representation has spread well beyond Unix since most 
 * C and C++ libraries on other systems provide Unix-compatible time and date 
 * functions. The major drawback of Unix time representation is that, if kept as a 
 * 32 bit signed quantity, on January 19, 2038 it will go negative, resulting in 
 * chaos in programs unprepared for this. Modern Unix and C implementations define 
 * the result of the time() function as type time_t, which leaves the door open for 
 * remediation (by changing the definition to a 64 bit integer, for example) before 
 * the clock ticks the dreaded doomsday second.
 * 
 * mktime -- Get Unix timestamp for a date
 * int mktime (int hour, int minute, int second, int month, int day, int year);
 * 
 * Warning: Note the strange order of arguments, which differs from the order of 
 * arguments in a regular Unix mktime() call and which does not lend itself well to 
 * leaving out parameters from right to left (see below). It is a common error to 
 * mix these values up in a script.
 * 
 * Returns the Unix timestamp corresponding to the arguments given. This timestamp 
 * is a long integer containing the number of seconds between the Unix Epoch 
 * (January 1 1970) and the time specified.
 * 
 * Example:
 * <code>
 * date_default_timezone_set('UTC');
 * 
 * include('./Arabic.php');
 * $Ar = new Arabic('ArMktime');
 * 
 * $time = $Ar->ArMktime->mktime(0,0,0,9,1,1427);
 * 
 * echo "<p>Calculated first day of Ramadan 1427 unix timestamp is: $time</p>";
 * 
 * $Gregorian = date('l F j, Y',$time);
 * 
 * echo "<p>Which is $Gregorian in Gregorian calendar</p>";            
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
// namespace Arabic/ArMktime;

/**
 * This PHP class is an Arabic customization for PHP mktime function
 *  
 * @category  Text 
 * @package   Arabic
 * @author    Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
 * @copyright 2009 Khaled Al-Shamaa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */ 
class ArMktime
{
    private static $_ISLAMIC_EPOCH = 1948439.5;
    
    /**
     * This will return current Unix timestamp for given Hegri date (Islamic calendar)
     *          
     * @param integer $hour       Time hour
     * @param integer $minute     Time minute
     * @param integer $second     Time second
     * @param integer $hj_month   Hegri month (Islamic calendar)
     * @param integer $hj_day     Hegri day   (Islamic calendar)
     * @param integer $hj_year    Hegri year  (Islamic calendar)
     * @param integer $correction To apply correction factor (+/- 1-2) to standard hijri calendar
     *             
     * @return integer Returns the current time measured in the number of
     *                seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function mktime($hour, $minute, $second, $hj_month, $hj_day, $hj_year, $correction = 0)
    {
        list($year, $month, $day) = $this->_convert($hj_year, $hj_month, $hj_day);

        $unixTimeStamp = mktime($hour, $minute, $second, $month, $day, $year);
        
        $unixTimeStamp = $unixTimeStamp + 3600*24*$correction; 
        
        return $unixTimeStamp;
    }
    
    /**
     * This will convert given Hegri date (Islamic calendar) into Gregorian date
     *          
     * @param integer $Y Hegri year (Islamic calendar)
     * @param integer $M Hegri month (Islamic calendar)
     * @param integer $D Hegri day (Islamic calendar)
     *      
     * @return array Gregorian date [int Year, int Month, int Day]
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _convert($Y, $M, $D)
    {
        // Converts Julian Day Count to a string containing the
        // Gregorian date in the format of "month/day/year".
        
        // To get these functions to work, you have to compile PHP with --enable-calendar 
        // http://www.php.net/manual/en/calendar.installation.php
        //$str = JDToGregorian($this->islamic_to_jd($Y, $M, $D));
        
        $str = $this->_jdToGreg($this->_islamicToJd($Y, $M, $D));
        
        list($month, $day, $year) = split('/', $str);
        
        return array($year, $month, $day);
    }
    
    /**
     * This will convert given Hegri date (Islamic calendar) into Julian day
     *          
     * @param integer $year  Hegri year
     * @param integer $month Hegri month
     * @param integer $day   Hegri day
     *      
     * @return integer Julian day
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _islamicToJd($year, $month, $day)
    {
        $temp = ($day + ceil(29.5 * ($month - 1)) + ($year - 1) * 354 + floor((3 + (11 * $year)) / 30) + self::$_ISLAMIC_EPOCH) - 1;

        return $temp; 
    }
    
    /**
     * Converts Julian Day Count to Gregorian date
     *      
     * @param integer $julian A julian day number as integer
     *       
     * @return integer The gregorian date as a string in the form "month/day/year"
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _jdToGreg($julian) 
    {
        $julian = $julian - 1721119;
        $calc1 = 4 * $julian - 1;
        $year = floor($calc1 / 146097);
        $julian = floor($calc1 - 146097 * $year);
        $day = floor($julian / 4);
        $calc2 = 4 * $day + 3;
        $julian = floor($calc2 / 1461);
        $day = $calc2 - 1461 * $julian;
        $day = floor(($day + 4) / 4);
        $calc3 = 5 * $day - 3;
        $month = floor($calc3 / 153);
        $day = $calc3 - 153 * $month;
        $day = floor(($day + 5) / 5);
        $year = 100 * $year + $julian;
        
        if ($month < 10) {
            $month = $month + 3;
        } else {
            $month = $month - 9;
            $year = $year + 1;
        }

        return $month.'/'.$day.'/'.$year;
    }
}
?>
