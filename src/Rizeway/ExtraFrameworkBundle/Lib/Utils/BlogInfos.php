<?php

/**
  *  Bloginy, Blog Aggregator
  *  Copyright (C) 2012  Riad Benguella - Rizeway
  *
  *  This program is free software: you can redistribute it and/or modify
  *
  *  it under the terms of the GNU General Public License as published by
  *  the Free Software Foundation, either version 3 of the License, or
  *  any later version.
  *
  *  This program is distributed in the hope that it will be useful,
  *  but WITHOUT ANY WARRANTY; without even the implied warranty of
  *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  * GNU General Public License for more details.
  *
  *  You should have received a copy of the GNU General Public License
  *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Rizeway\ExtraFrameworkBundle\Lib\Utils;

class BlogInfos
{

  //global variable
  private static $technorati;
  private static $technorati_loaded = false;

  private static $technorati_api_key = "3ce3b21d7d4badd1c7404f7a148c614d";

  //--> for google pagerank
  public static function StrToNum($Str, $Check, $Magic)
  {
    $Int32Unit = 4294967296; // 2^32

    $length = strlen($Str);
    for ($i = 0; $i < $length; $i++)
    {
      $Check *= $Magic;
      //If the float is beyond the boundaries of integer (usually +/- 2.15e+9 = 2^31),
      //  the result of converting to integer is undefined
      //  refer to http://www.php.net/manual/en/language.types.integer.php
      if ($Check >= $Int32Unit)
      {
        $Check = ($Check-$Int32Unit*(int)($Check/$Int32Unit));
        //if the check less than -2^31
        $Check = ($Check < -2147483648)?($Check+$Int32Unit):
          $Check;
        }
        $Check += ord($Str
        {
          $i
        }
        );
      }
      return $Check;
    }

    //--> for google pagerank
    /*
     * Genearate a hash for a url
     */
    public static function HashURL($String)
    {
      $Check1 = self::StrToNum($String, 0x1505, 0x21);
      $Check2 = self::StrToNum($String, 0, 0x1003F);

      $Check1>>=2;
      $Check1 = (($Check1>>4) & 0x3FFFFC0) | ($Check1 & 0x3F);
      $Check1 = (($Check1>>4) & 0x3FFC00) | ($Check1 & 0x3FF);
      $Check1 = (($Check1>>4) & 0x3C000) | ($Check1 & 0x3FFF);

      $T1 = (((($Check1 & 0x3C0)<<4) | ($Check1 & 0x3C))<<2) | ($Check2 & 0xF0F);
      $T2 = (((($Check1 & 0xFFFFC000)<<4) | ($Check1 & 0x3C00))<<0xA) | ($Check2 & 0xF0F0000);

      return ($T1 | $T2);
    }

    //--> for google pagerank
    /*
     * genearate a checksum for the hash string
     */
    public static function CheckHash($Hashnum)
    {
      $CheckByte = 0;
      $Flag = 0;

      $HashStr = sprintf('%u', $Hashnum);
      $length = strlen($HashStr);

      for ($i = $length-1; $i >= 0; $i--)
      {
        $Re = $HashStr
        {
          $i
        }
        ;
        if (1 === ($Flag%2))
        {
          $Re += $Re;
          $Re = (int)($Re/10)+($Re%10);
        }
        $CheckByte += $Re;
        $Flag++;
      }

      $CheckByte %= 10;
      if (0 !== $CheckByte)
      {
        $CheckByte = 10-$CheckByte;
        if (1 === ($Flag%2))
        {
          if (1 === ($CheckByte%2))
          {
            $CheckByte += 9;
          }
          $CheckByte>>=1;
        }
      }

      return '7'.$CheckByte.$HashStr;
    }

    //get google pagerank
    public static function getpagerank($url)
    {
      $pagerank = 0;
      $query = "http://toolbarqueries.google.com/search?client=navclient-auto&ch=".self::CheckHash(self::HashURL($url))."&features=Rank&q=info:".$url."&num=100&filter=0";
      $data = self::file_get_contents_curl($query);
      //print_r($data);
      $pos = strpos($data, "Rank_");
      if ($pos === false)
      {
          return $pagerank;
      }
      else
      {
        $pagerank = substr($data, $pos+9);
        return $pagerank;
      }
    }


    //get technorati rank
    public static function get_technorati_informations($url)
    {
      global $technorati;

      $technorati_xml = "http://api.technorati.com/bloginfo?key=".self::$technorati_api_key."&url=".$url;
      $tech = simplexml_load_file($technorati_xml);

      self::$technorati = $tech->document->result;
	  self::$technorati_loaded = true;
    }

    //get technorati rank
    public static function get_technorati_rank($url)
    {
      if (!self::$technorati_loaded)
	  {
	  	self::get_technorati_informations($url);
	  }
      return (int)self::$technorati->weblog->rank;
    }

    //get technorati rank
    public static function get_technorati_inblogs($url)
    {
      if (!self::$technorati_loaded)
	  {
	  	self::get_technorati_informations($url);
	  }
      return (int)self::$technorati->weblog->inboundblogs;
    }

    //get alexa popularity
    public static function get_alexa_popularity($url)
    {
      global $alexa_backlink, $alexa_reach;
      $alexaxml = "http://xml.alexa.com/data?cli=10&dat=nsa&url=".$url;

      $xml_parser = xml_parser_create();
      /*
       $fp = fopen($alexaxml, "r") or die("Error: Reading XML data.");
       $data = "";
       while (!feof($fp)) {
       $data .= fread($fp, 8192);
       //echo "masuk while<br />";
       }
       fclose($fp);
       */
      $data = self::file_get_contents_curl($alexaxml);
      xml_parse_into_struct($xml_parser, $data, $vals, $index);
      xml_parser_free($xml_parser);

      //print_r($vals);
      //echo "<br />";
      //print_r($index);

      $index_popularity = $index['POPULARITY'][0];
      $index_reach = $index['REACH'][0];
      $index_linksin = $index['LINKSIN'][0];
      //echo $index_popularity."<br />";
      //print_r($vals[$index_popularity]);
      $alexarank = $vals[$index_popularity]['attributes']['TEXT'];
      $alexa_backlink = $vals[$index_linksin]['attributes']['NUM'];
      $alexa_reach = $vals[$index_reach]['attributes']['RANK'];

      return $alexarank;
    }

    //get alexa backlink
    public static function alexa_backlink($url)
    {
      global $alexa_backlink;
      if ($alexa_backlink != 0)
      {
        return $alexa_backlink;
      }
      else
      {
        $rank = self::get_alexa_popularity($url);
        return $alexa_backlink;
      }
    }

    //get alexa reach rank
    public static function alexa_reach_rank($url)
    {
      global $alexa_reach;
      if ($alexa_reach != 0)
      {
        return $alexa_reach;
      }
      else
      {
        $rank = self::get_alexa_popularity($url);
        return $alexa_reach;
      }
    }

    //get exactrank (from exactrank.com)
    public static function get_exactrank($url)
    {
      $exactrankurl = "http://exactrank.com/index.php?url=".urlencode($url);
      $data = self::file_get_contents_curl($exactrankurl);
      $spl = explode('<td style="text-align:right;">', $data);
      //print_r($spl[1]);
      $spl2 = explode('</span>', $spl[1]);
      $ret = trim($spl2[0]);
      if (strlen($ret) == 0)
      {
        return (0);
      }
      else
      {
        return ($ret);
      }
    }


    //get google backlink
    public static function google_backlink($uri)
    {
      $uri = trim(\str_replace('http://', '', $uri));
      $uri = trim(\str_replace('http', '', $uri));
      $url = 'http://www.google.com/search?hl=en&lr=&ie=UTF-8&q=link:'.$uri.'&filter=0';
      $v = self::file_get_contents_curl($url);
      preg_match('/of about \<b\>(.*?)\<\/b\>/si', $v, $r);
      preg_match('/of \<b\>(.*?)\<\/b\>/si', $v, $s);
      if (isset($s[1]) && $s[1] != 0)
      {
        return $s[1];
      }
      else
      {
        return (isset($r[1]))?$r[1]:'0';
      }
    }

    //get yahoo inlink/backlink
    public static function yahoo_inlink($uri)
    {
      $uri = trim(eregi_replace('http://', '', $uri));
      $uri = trim(eregi_replace('http', '', $uri));
      $url = 'http://siteexplorer.search.yahoo.com/advsearch?p=http://'.$uri.'&bwm=i&bwmf=s&bwmo=&fr2=seo-rd-se';
      $v = self::file_get_contents_curl($url);
      preg_match('/of about \<strong\>(.*?) \<\/strong\>/si', $v, $r);
      return ($r[1])?$r[1]:'0';
    }

    //get altavista search result count
    public static function altavista_link($sURL)
    {
      $url = "http://www.altavista.com/web/results?itag=ody&q=link%3A$sURL&kgs=0&kls=0";
      $data = self::file_get_contents_curl($url);
      $spl = explode("AltaVista found ", $data);
      $spl2 = explode(" results", $spl[1]);
      $ret = trim($spl2[0]);
      if (strlen($ret) == 0)
      {
        return (0);
      }
      else
      {
        return ($ret);
      }

    }

    //get alltheweb search result count
    public static function alltheweb_link($sURL)
    {
      $url = "http://www.alltheweb.com/search?cat=web&cs=utf-8&q=link%3A".urlencode($sURL)."&_sb_lang=any";
      $data = self::file_get_contents_curl($url);
      $spl = explode("</span> of <span class=\"ofSoMany\">", $data);
      $spl2 = explode("</span>", $spl[1]);
      $ret = trim($spl2[0]);
      if (strlen($ret) == 0)
      {
        return (0);
      }
      else
      {
        return ($ret);
      }
    }

    //get google indexed page
    public static function google_indexed($uri)
    {
      $uri = trim(eregi_replace('http://', '', $uri));
      $uri = trim(eregi_replace('http', '', $uri));
      $url = 'http://www.google.com/search?hl=en&lr=&ie=UTF-8&q=site:'.$uri.'&filter=0';
      $v = self::file_get_contents_curl($url);
      preg_match('/of about \<b\>(.*?)\<\/b\>/si', $v, $r);
      preg_match('/of \<b\>(.*?)\<\/b\>/si', $v, $s);
      if ($s[1] != 0)
      {
        return $s[1];
      }
      else
      {
        return ($r[1])?$r[1]:'0';
      }
    }

    //get yahoo indexed page
    public static function yahoo_indexed($uri)
    {
      $uri = trim(eregi_replace('http://', '', $uri));
      $uri = trim(eregi_replace('http', '', $uri));
      $url = 'http://siteexplorer.search.yahoo.com/advsearch?p=http://'.$uri.'&bwm=p&bwmf=s&bwmo=d';
      $v = self::file_get_contents_curl($url);
      preg_match('/of about \<strong\>(.*?) \<\/strong\>/si', $v, $r);
      return ($r[1])?$r[1]:'0';
    }

    //get msn indexed page
    public static function msn_indexed($uri)
    {
      $uri = trim(eregi_replace('http://', '', $uri));
      $uri = trim(eregi_replace('http', '', $uri));
      $url = 'http://search.msn.com/results.aspx?q=site:'.$uri;
      $data = self::file_get_contents_curl($url);
      $spl = explode("of", $data);
      $spl2 = explode("results", $spl[1]);
      $ret = trim($spl2[0]);
      if (strlen($ret) == 0)
      {
        return (0);
      }
      else
      {
        return ($ret);
      }
    }

    //get googlebot last access
    public static function googlebot_lastaccess($url)
    {
      $url = 'http://209.85.175.104/search?hl=en&q=cache:'.$url.'&btnG=Google+Search&meta=';
      $data = self::file_get_contents_curl($url);
      $spl = explode("as retrieved on", $data);
      //echo "<pre>".$spl[0]."</pre>";
      $spl2 = explode(".<br>", $spl[1]);
      $ret = trim($spl2[0]);
      //echo "<pre>".$spl2[0]."</pre>";
      if (strlen($ret) == 0)
      {
        return (0);
      }
      else
      {
        return ($ret);
      }
    }

    //get blogworth
    public static function blogworth($url)
    {
      $worthurl = 'http://www.business-opportunities.biz/projects/how-much-is-your-blog-worth/submit/';
      $data = array ('url'=>$url);
      $data = http_build_query($data);

      $res = self::do_post_request_curl($worthurl, $data);
      $spl = explode("is worth", $res);
      $spl2 = explode("</p>", $spl[1]);
      $ret = trim($spl2[0]);
      if (strlen($ret) == 0)
      {
        return (0);
      }
      else
      {
        return ($ret);
      }
    }

    //for POST request with curl
    public static function do_post_request_curl($url, $data)
    {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
      curl_setopt($ch, CURLOPT_POST, 1); // set POST method
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // add POST fields
      $result = curl_exec($ch); // run the whole process
      //echo $result;
      curl_close($ch);
      return $result;
    }

    //function to check whether an url is listed in DMOZ(ODP), return 1 or 0
    public static function dmoz_listed($url)
    {
      $url = trim(eregi_replace('http://', '', $url));
      $url = trim(eregi_replace('http', '', $url));
      $dmozurl = 'http://search.dmoz.org/cgi-bin/search?search='.$url;
      $data = self::file_get_contents_curl($dmozurl);
      //echo "<pre>".$data."</pre>";
      $pos = strpos($data, 'match');
      if ($pos == 0)
      {
        return 0;
      }
      else
      {
        return 1;
      }
    }

    public static function file_get_contents_curl($url)
    {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
      curl_setopt($ch, CURLOPT_URL, $url);
      $data = curl_exec($ch);
      curl_close($ch);

      return $data;
    }
  }
