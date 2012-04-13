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
 * Class Name: Muslim Prayer Times
 *  
 * Filename:   Salat.class.php
 *  
 * Original    Author(s): Khaled Al-Sham'aa <khaled.alshamaa@gmail.com>
 *  
 * Purpose:    The five Islamic prayers are named Fajr, Zuhr, Asr, Maghrib
 *             and Isha. The timing of these five prayers varies from place
 *             to place and from day to day. It is obligatory for Muslims
 *             to perform these prayers at the correct time.
 *              
 * ----------------------------------------------------------------------
 *  
 * Source: http://qasweb.org/qasforum/index.php?showtopic=177&st=0
 * By: Mohamad Magdy <mohamad_magdy_egy@hotmail.com>
 *  
 * ----------------------------------------------------------------------
 *  
 * Muslim Prayer Times
 *
 * Using this PHP Class you can calculate the time of Muslim prayer
 * according to the geographic location.
 * 
 * The five Islamic prayers are named Fajr, Zuhr, Asr, Maghrib and Isha. The timing 
 * of these five prayers varies from place to place and from day to day. It is 
 * obligatory for Muslims to perform these prayers at the correct time.
 * 
 * The prayer times for any given location on earth may be determined mathematically 
 * if the latitude and longitude of the location are known. However, the theoretical 
 * determination of prayer times is a lengthy process. Much of this tedium may be 
 * alleviated by using computer programs.
 * 
 * Definition of prayer times
 * 
 * - FAJR starts with the dawn or morning twilight. Fajr ends just before sunrise.
 * - ZUHR begins after midday when the trailing limb of the sun has passed the 
 *   meridian. For convenience, many published prayer timetables add five minutes to 
 *   mid-day (zawal) to obtain the start of Zuhr. Zuhr ends at the start of Asr time.
 * - The timing of ASR depends on the length of the shadow cast by an object. 
 *   According to the Shafi school of jurisprudence, Asr begins when the length of 
 *   the shadow of an object exceeds the length of the object. According to the 
 *   Hanafi school of jurisprudence, Asr begins when the length of the shadow exceeds 
 *   TWICE the length of the object. In both cases, the minimum length of shadow 
 *   (which occurs when the sun passes the meridian) is subtracted from the length 
 *   of the shadow before comparing it with the length of the object.
 * - MAGHRIB begins at sunset and ends at the start of isha.
 * - ISHA starts after dusk when the evening twilight disappears.      
 *
 * Example:
 * <code>
 *     date_default_timezone_set('UTC');
 *     
 *     include('./Arabic.php');
 *     $Ar = new Arabic('Salat');
 * 
 *     $Ar->Salat->setLocation(33.513,36.292,2);
 *     $Ar->Salat->setDate(date('j'), date('n'), date('Y'));
 * 
 *     $times = $Ar->Salat->getPrayTime();
 * 
 *     echo '<b>Damascus, Syria</b><br />';
 *     echo date('l F j, Y').'<br /><br />';
 *        
 *     echo "<b class=hilight>Fajr:</b> {$times[0]}<br />";
 *     echo "<b class=hilight>Sunrise:</b> {$times[1]}<br />";
 *     echo "<b class=hilight>Zuhr:</b> {$times[2]}<br />";
 *     echo "<b class=hilight>Asr:</b> {$times[3]}<br />";
 *     echo "<b class=hilight>Maghrib:</b> {$times[4]}<br />";
 *     echo "<b class=hilight>Isha:</b> {$times[5]}<br />";    
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
// namespace Arabic/Salat;

/**
 * This PHP class calculate the time of Muslim prayer according to the geographic 
 * location.
 *  
 * @category  Text 
 * @package   Arabic
 * @author    Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
 * @copyright 2009 Khaled Al-Shamaa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */ 
class Salat
{
    // السنة
    private $_year = 1975;
    
    // الشهر
    private $_month = 8;
    
    // اليوم
    private $_day = 2;
    
    // فرق التوقيت العالمى
    private $_zone = 2;
    
    // خط الطول الجغرافى للمكان
    private $_long = 37.15861;
    
    // خط العرض الجغرافى
    private $_lat = 36.20278;
    
    // زاوية الشروق والغروب
    private $_AB2 = -0.833333;
    
    // زاوية العشاء
    private $_AG2 = -18;
    
    // زاوية الفجر
    private $_AJ2 = -18;
    
    // المذهب
    private $_school = 'Shafi';
    
    /**
     * Setting date of day for Salat calculation
     *      
     * @param integer $d Day of date you want to calculate Salat in
     * @param integer $m Month of date you want to calculate Salat in
     * @param integer $y Year (four digits) of date you want to calculate Salat in
     *      
     * @return boolean TRUE if success, or FALSE if fail
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function setDate($d = 2, $m = 8, $y = 1975)
    {
        $flag = true;
        
        if (is_numeric($y) && $y > 0 && $y < 3000) {
            $this->_year = floor($y);
        } else {
            $flag = false;
        }
        
        if (is_numeric($m) && $m >= 1 && $m <= 12) {
            $this->_month = floor($m);
        } else {
            $flag = false;
        }
        
        if (is_numeric($d) && $d >= 1 && $d <= 31) {
            $this->_day = floor($d);
        } else {
            $flag = false;
        }
        
        return $flag;
    }
    
    /**
     * Setting location information for Salat calculation
     *      
     * @param decimal $l1 Longitude of location you want to calculate Salat time in
     * @param decimal $l2 Latitude of location you want to calculate Salat time in
     * @param integer $z  Time Zone, offset from UTC (see also Greenwich Mean Time)
     *      
     * @return boolean TRUE if success, or FALSE if fail
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function setLocation($l1 = 37.15861, $l2 = 36.20278, $z = 2)
    {
        $flag = true;
        
        if (is_numeric($l1) && $l1 >= -180 && $l1 <= 180) {
            $this->_long = $l1;
        } else {
            $flag = false;
        }
        
        if (is_numeric($l2) && $l2 >= -180 && $l2 <= 180) {
            $this->_lat = $l2;
        } else {
            $flag = false;
        }
        
        if (is_numeric($z) && $z >= -12 && $z <= 12) {
            $this->_zone = floor($z);
        } else {
            $flag = false;
        }
        
        return $flag;
    }
    
    /**
     * Setting rest of Salat calculation configuration
     *      
     * @param string  $sch        [Shafi|Hanafi] to define Muslims Salat 
     *                            calculation method (affect Asr time)
     * @param decimal $sunriseArc Sun rise arc (default value is -0.833333)
     * @param decimal $ishaArc    Isha arc (default value is -18)
     * @param decimal $fajrArc    Fajr arc (default value is -18)
     *      
     * @return boolean TRUE if success, or FALSE if fail
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function setConf($sch = 'Shafi', $sunriseArc = -0.833333, $ishaArc = -18, $fajrArc = -18)
    {
        $flag = true;
        
        $sch = ucfirst($sch);
        
        if ($sch == 'Shafi' || $sch == 'Hanafi') {
            $this->_school = $sch;
        } else {
            $flag = false;
        }
        
        if (is_numeric($sunriseArc) && $sunriseArc >= -180 && $sunriseArc <= 180) {
            $this->_AB2 = $sunriseArc;
        } else {
            $flag = false;
        }
        
        if (is_numeric($ishaArc) && $ishaArc >= -180 && $ishaArc <= 180) {
            $this->_AG2 = $ishaArc;
        } else {
            $flag = false;
        }
        
        if (is_numeric($fajrArc) && $fajrArc >= -180 && $fajrArc <= 180) {
            $this->_AJ2 = $fajrArc;
        } else {
            $flag = false;
        }
        
        return $flag;
    }
    
    /**
     * Calculate Salat times for the date set in setSalatDate methode, and 
     * location set in setSalatLocation.
     *                        
     * @return array of Salat times + sun rise in the following format
     *               hh:mm where hh is the hour in local format and 24 mode
     *               mm is minutes with leading zero to be 2 digits always
     *               array items is [Fajr, Sunrise, Zuhr, Asr, Maghrib, Isha]
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     * @author Mohamad Magdy <mohamad_magdy_egy@hotmail.com>
     * @source http://qasweb.org/qasforum/index.php?showtopic=177&st=0
     */
    public function getPrayTime()
    {
        $prayTime = array();
        
        // نحسب اليوم الجوليانى
        $d = ((367 * $this->_year) - (floor((7 / 4) * ($this->_year + floor(($this->_month + 9) / 12)))) + floor(275 * ($this->_month / 9)) + $this->_day - 730531.5);
        
        // نحسب طول الشمس الوسطى
        $L = fmod(280.461 + 0.9856474 * $d, 360);
        
        // ثم نحسب حصة الشمس الوسطى
        $M = fmod(357.528 + 0.9856003 * $d, 360);
        
        // ثم نحسب طول الشمس البروجى
        $lambda = $L + 1.915 * sin($M * pi() / 180) + 0.02 * sin(2 * $M * pi() / 180);
        
        // ثم نحسب ميل دائرة البروج
        $obl = 23.439 - 0.0000004 * $d;
        
        // ثم نحسب المطلع المستقيم
        $alpha = atan(cos($obl * pi() / 180) * tan($lambda * pi() / 180)) * 180 / pi();
        $alpha = $alpha - (360 * floor($alpha / 360));
        
        // ثم نعدل المطلع المستقيم
        $alpha = $alpha + 90 * ((int)($lambda / 90) - (int)($alpha / 90));
        
        // نحسب الزمن النجمى بالدرجات الزاوية
        $ST = fmod(100.46 + 0.985647352 * $d, 360);
        
        // ثم نحسب ميل الشمس الزاوى
        $Dec = asin(sin($obl * pi() / 180) * sin($lambda * pi() / 180)) * 180 / pi();
        
        // نحسب زوال الشمس الوسطى
        $noon = fmod(abs($alpha - $ST), 360);
        
        // ثم الزوالى العالمى
        $un_noon = $noon - $this->_long;
        
        // ثم الزوال المحلى
        $local_noon = fmod(($un_noon/15) + $this->_zone, 24);
        
        // وقت صلاة الظهر
        $Dhuhr = $local_noon / 24;
        $Dhuhr_h = (int)($Dhuhr * 24 * 60 / 60);
        $Dhuhr_m = sprintf('%02d', ($Dhuhr * 24 * 60) % 60);
        $prayTime[2] = $Dhuhr_h.':'.$Dhuhr_m;
        
        if ($this->_school == 'Shafi') {
            // نحسب إرتفاع الشمس لوقت صلاة العصر حسب المذهب الشافعي
            $T = atan(1 + tan(abs($this->_lat - $Dec) * pi() / 180)) * 180 / pi();
            
            // ثم نحسب قوس الدائر أى الوقت المتبقى من وقت الظهر حتى صلاة العصر حسب المذهب الشافعي
            $V = acos((sin((90 - $T) * pi() / 180) - sin($Dec * pi() / 180) * sin($this->_lat * pi() / 180)) / (cos($Dec * pi() / 180) * cos($this->_lat * pi() / 180))) * 180 / pi() / 15;
            
            // وقت صلاة العصر حسب المذهب الشافعي
            $X = $local_noon + $V;
            $SAsr = $Dhuhr + $V / 24;
            $SAsr_h = (int)($SAsr * 24 * 60 / 60);
            $SAsr_m = sprintf('%02d', ($SAsr * 24 * 60) % 60);
            $prayTime[3] = $SAsr_h.':'.$SAsr_m;
        } else {
            // نحسب إرتفاع الشمس لوقت صلاة العصر حسب المذهب الحنفي
            $U = atan(2 + tan(abs($this->_lat - $Dec) * pi() / 180)) * 180 / pi();
            
            // ثم نحسب قوس الدائر أى الوقت المتبقى من وقت الظهر حتى صلاة العصر حسب المذهب الحنفي
            $W = acos((sin((90 - $U) * pi() / 180) - sin($Dec * pi() / 180) * sin($this->_lat * pi() / 180)) / (cos($Dec * pi() / 180) * cos($this->_lat * pi() / 180))) * 180 / pi() / 15;
            
            // وقت صلاة العصر حسب المذهب الحنفي
            $Z = $local_noon + $W;
            $HAsr = $Z / 24;
            $HAsr_h = (int)($HAsr * 24 * 60 / 60);
            $HAsr_m = sprintf('%02d', ($HAsr * 24 * 60) % 60);
            $prayTime[3] = $HAsr_h.':'.$HAsr_m;
        }
        
        // نحسب نصف قوس النهار
        $AB = acos((SIN($this->_AB2 * pi() / 180) - sin($Dec * pi() / 180) * sin($this->_lat * pi() / 180)) / (cos($Dec * pi() / 180) * cos($this->_lat * pi() / 180))) * 180 / pi();
        
        // وقت الشروق
        $AC = $local_noon - $AB / 15;
        $Sunrise = $AC / 24;
        $Sunrise_h = (int)($Sunrise * 24 * 60 / 60);
        $Sunrise_m = sprintf('%02d', ($Sunrise * 24 * 60) % 60);
        $prayTime[1] = $Sunrise_h.':'.$Sunrise_m;
        
        // وقت الغروب
        $AE = $local_noon + $AB / 15;
        $Sunset = $AE / 24;
        $Sunset_h = (int)($Sunset * 24 * 60 / 60);
        $Sunset_m = sprintf('%02d', ($Sunset * 24 * 60) % 60);
        $prayTime[4] = $Sunset_h.':'.$Sunset_m;
        
        // نحسب فضل الدائر وهو الوقت المتبقى من وقت صلاة الظهر إلى وقت العشاء
        $AG = acos((sin($this->_AG2 * pi() / 180) - sin($Dec * pi() / 180) * sin($this->_lat * pi() / 180)) / (cos($Dec * pi() / 180) * cos($this->_lat * pi() / 180))) * 180 / pi();
        
        // وقت صلاة العشاء
        $AH = $local_noon + ($AG / 15);
        $Isha = $AH / 24;
        $Isha_h = (int)($Isha * 24 * 60 / 60);
        $Isha_m = sprintf('%02d', ($Isha * 24 * 60) % 60);
        $prayTime[5] = $Isha_h.':'.$Isha_m;
        
        // نحسب فضل دائر الفجر وهو الوقت المتبقى من وقت صلاة الفجر حتى وقت صلاة الظهر
        $AJ = acos((sin($this->_AJ2 * pi() / 180) - sin($Dec * pi() / 180) * sin($this->_lat * pi() / 180)) / (cos($Dec * pi() / 180) * cos($this->_lat * pi() / 180))) * 180 / pi();
        
        // وقت صلاة الفجر
        $AK = $local_noon - $AJ / 15;
        $Fajr = $AK / 24;
        $Fajr_h = (int)($Fajr * 24 * 60 / 60);
        $Fajr_m = sprintf('%02d', ($Fajr * 24 * 60) % 60);
        $prayTime[0] = $Fajr_h.':'.$Fajr_m;
        
        return $prayTime;
    }
}
?>