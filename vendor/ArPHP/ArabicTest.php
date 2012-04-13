<?php
/*
Installing PHPUnit

PHPUnit should be installed using the PEAR Installer. This installer is the 
backbone of PEAR, which provides a distribution system for PHP packages, and is 
shipped with every release of PHP since version 4.3.0.

The PEAR channel (pear.phpunit.de) that is used to distribute PHPUnit needs to 
be registered with the local PEAR environment:

pear channel-discover pear.phpunit.de

This has to be done only once. Now the PEAR Installer can be used to install 
packages from the PHPUnit channel:

pear install phpunit/PHPUnit

After the installation you can find the PHPUnit source files inside your local 
PEAR directory; the path is usually /usr/lib/php/PHPUnit. 
--------------------------------------------------------------------------------

The Command-Line Test Runner

The PHPUnit command-line test runner can be invoked through the phpunit command. 
The following code shows how to run tests with the PHPUnit command-line test 
runner:

phpunit ArabicTest
PHPUnit 3.2.10 by Sebastian Bergmann.

..

Time: 0 seconds


OK (2 tests)

For each test run, the PHPUnit command-line tool prints one character to 
indicate progress:

.   Printed when the test succeeds. 
F   Printed when an assertion fails while running the test method. 

*/

  require_once 'PHPUnit/Framework.php';
  require_once 'Arabic.php';
  
  class ArabicTest extends PHPUnit_Framework_TestCase
  {
      protected $strUTF;
      protected $strWIN;
      protected $strISO;
      protected $arabic;
      
      protected function setUp()
      {
          date_default_timezone_set('UTC');
          
          $this->strUTF = 'ЎЃЎІўДЎѓ ЎІўДЎіўЕЎєЎ©';
          $this->strWIN = 'ќ«бѕ «б‘гЏ…';
          $this->strISO = '';
          
          $this->arabic = new Arabic();
          $this->arabic->setInputCharset('windows-1256');
          $this->arabic->setOutputCharset('windows-1256');
      }
      
      protected function tearDown()
      {
          $this->strUTF = null;
          $this->strWIN = null;
          $this->strISO = null;
      }
      
      public function testConvertUtf8ToWin1256()
      {
          $this->arabic->load('ArCharsetC');
          
          $this->arabic->setInputCharset('utf-8');
          $this->arabic->setOutputCharset('windows-1256');
          
          $result = $this->arabic->convert($this->strUTF);
          
          $this->assertEquals($this->strWIN, $result);
      }

      public function testConvertWin1256ToUtf8()
      {
          $this->arabic->load('ArCharsetC');
          
          $this->arabic->setInputCharset('windows-1256');
          $this->arabic->setOutputCharset('utf-8');
          
          $result = $this->arabic->convert($this->strWIN);
          
          $this->assertEquals($this->strUTF, $result);
      }

      public function testDateHegri()
      {
          $this->arabic->load('ArDate');
          
          $time = 176205600;
          $hejri = '«б”»  26 —ћ» 1395 10:00:00 ’»«Ќ«р';
          
          $result = $this->arabic->date('l dS F Y h:i:s A', $time);
          
          $this->assertEquals($hejri, $result);
      }

      public function testDateHegriMinusOneDay()
      {
          $this->arabic->load('ArDate');
          
          $time = 176205600;
          $hejri = 'ЎІўДЎђўЕЎєЎ© 25 Ў±ЎђЎ® 1395 10:00:00 ЎµЎ®ЎІЎ≠ЎІўЛ';
          
          $this->arabic->setOutputCharset('utf-8');
          $result = $this->arabic->date('l dS F Y h:i:s A', $time, null, -1);
          
          $this->assertEquals($hejri, $result);
      }
      
      public function testDateArabic()
      {
          $this->arabic->load('ArDate');
          
          $time = 176205600;
          $arabic = '«б”»  02 ¬»/√џ”Ў” 1975 10:00:00 ’»«Ќ«р';
          
          $this->arabic->setMode(4);
          $result = $this->arabic->date('l dS F Y h:i:s A', $time);
          
          $this->assertEquals($arabic, $result);
      }

      public function testDateLibyan()
      {
          $this->arabic->load('ArDate');
          
          $time = 176205600;
          $libyan = '«б”»  02 е«дн»«б 1343 10:00:00 ’»«Ќ«р';
          
          $this->arabic->setMode(5);
          $result = $this->arabic->date('l dS F Y h:i:s A', $time);
          
          $this->assertEquals($libyan, $result);
      }
      
      public function testEnglishArabicTransliteration()
      {
          $this->arabic->load('ArTransliteration');
          
          $en = 'Formula1';
          $ar = ' Ёж—гжб«1';
          
          $result = $this->arabic->en2ar($en);
          
          $this->assertEquals($ar, $result);
      }
      
      public function testArabicEnglishTransliteration()
      {
          $this->arabic->load('EnTransliteration');
          
          $ar = 'ќ«бцѕ «б‘угЏу…';
          $en = " Khalid Al-Sham'ah";
          
          $result = $this->arabic->ar2en($ar);
          
          $this->assertEquals($en, $result);
      }
      
      public function testArabicGenderMale()
      {
          $this->arabic->load('ArGender');
          
          $name = 'гдё– «бЏбн';
          $female = false;
          
          $result = $this->arabic->isFemale($name);
          
          $this->assertEquals($female, $result);
      }
      
      public function testArabicGenderFemale()
      {
          $this->arabic->load('ArGender');
          
          $name = 'бжде «б‘»б';
          $female = true;
          
          $result = $this->arabic->isFemale($name);
          
          $this->assertEquals($female, $result);
      }
      
      public function testSpellNumbersInArabicIdiom1()
      {
          $this->arabic->load('ArNumbers');
          
          $number = 2147483647;
          $spell = 'гбн«—«д ж г∆… ж ”»Џ ж √—»Џжд гбнжд ж √—»Џг∆… ж Ћб«Ћ ж Ћг«джд √бЁ ж ” г∆… ж ”»Џ ж √—»Џжд';
          
          $this->arabic->setFeminine(1);
          $this->arabic->setFormat(1);
          $result = $this->arabic->int2str($number);
          
          $this->assertEquals($spell, $result);
      }
      
      public function testSpellNumbersInArabicIdiom2()
      {
          $this->arabic->load('ArNumbers');
          
          $number = 2147483647;
          $spell = 'гбн«—нд ж г∆… ж ”»Џ… ж √—»Џнд гбнжд ж √—»Џг∆… ж Ћб«Ћ… ж Ћг«днд √бЁ ж ” г∆… ж ”»Џ… ж √—»Џнд';
          
          $this->arabic->setFeminine(2);
          $this->arabic->setFormat(2);
          $result = $this->arabic->int2str($number);
          
          $this->assertEquals($spell, $result);
      }
      
      public function testSpellNumbersInArabicIdiom3()
      {
          $this->arabic->load('ArNumbers');
          
          $number = 2749.317;
          $spell = '√бЁнд ж ”»Џг∆… ж  ”Џ… ж √—»Џнд Ё«’б… Ћб«Ћг∆… ж ”»Џ… Џ‘—';
          
          $this->arabic->setFeminine(2);
          $this->arabic->setFormat(2);
          $result = $this->arabic->int2str($number);
          
          $this->assertEquals($spell, $result);
      }
      
      public function testHegriMktime()
      {
          $this->arabic->load('ArMktime');
          
          $time = 1159056000;
          
          $result = $this->arabic->mktime(0, 0, 0, 9, 1, 1427);
          
          $this->assertEquals($time, $result);
      }
      
      public function testHegriMktimePlusOneDay()
      {
          $this->arabic->load('ArMktime');
          
          $time = 1159142400;
          
          $result = $this->arabic->mktime(0, 0, 0, 9, 1, 1427, +1);

          $this->assertEquals($time, $result);
      }
      
      public function testSwapEnglishToArabicKeyboard()
      {
          $this->arabic->load('ArKeySwap');
          
          $english = "Hpf lk hgkhs hglj'vtdkK Hpf hg`dk dldg,k f;gdjil Ygn ,p]hkdm hgHl,v tb drt,k ljv]]dk fdk krdqdk>";
          $arabic = '√Ќ» гд «бд«” «бг Ў—Ёнд° √Ќ» «б–нд нгнбжд »ябн ег ≈бм жЌѕ«дн… «б√гж— Ёб« нёЁжд г —ѕѕнд »нд дён÷нд.';
          
          $result = $this->arabic->swap_ea($english);
          
          $this->assertEquals($arabic, $result);
      }
      
      public function testSwapArabicToEnglishKeyboard()
      {
          $this->arabic->load('ArKeySwap');
          
          $arabic = "цмџ емЁЋггебЋмЁ »ќќг ƒ‘м …‘дЋ Ё«емб” б«еббЋё …ќёЋ ƒќ…ЌгЋЅ ‘мн …ќёЋ —еќгЋмЁ“ еЁ Ё‘дЋ” ‘ ЁќЏƒ« ќ» бЋмеЏ” ‘мн ‘ гќЁ ќ» ƒќЏё‘бЋ Ёќ …ќ—Ћ ем Ё«Ћ ќЌЌќ”еЁЋ неёЋƒЁеќм“";
          $english = 'Any intelligent fool can make things bigger more complex and more violent. it takes a touch of genius and a lot of courage to move in the opposite direction.';
          
          $result = $this->arabic->swap_ae($arabic);
          
          $this->assertEquals($english, $result);
      }
      
      public function testMaxArabicCharsInA4Line()
      {
          $this->arabic->load('ArGlyphs');
          
          $font_size = 16;
          $max = 101;
          
          $result = $this->arabic->a4_max_chars($font_size);
          
          $this->assertEquals($max, $result);
      }
      
      public function testSplitArabicTextIntoA4Lines()
      {
          $this->arabic->load('ArGlyphs');
          
          $font_size = 16;
          $text = 'Ён Ќёнё… «б√г—° бёѕ ”»ё б‘—я… Microsoft –« е« «б Џ«гб гЏ  ёдн… Ajax е–е гд– √ж«ќ—  ”Џнд«  «бё—д «бг«÷н, б« »б √де« б«  “«б  ” ќѕг  бя «б ёдн… Ён  Џ“н“ гёѕ—… »—д«гће« «б‘ен— Outlook бб»—нѕ «б≈бя —ждн. жЏбм «б—џг гд яжд  ёдн… Ajax ёѕнг…  ёдн… «бЏеѕ д”»н«° ≈б« √де« бг  бё (Ќнд ўеж—е« √жб г—…) «бяЋн— гд «б«е г«г° ≈б« √д «бЁ÷б нЏжѕ ≈бм ‘—я… Google Ён дЁ÷ «бџ»«— Џде« жб≈Џ«ѕ… ≈я ‘«Ёе« гд ћѕнѕ° ж–бя гд ќб«б Ў«∆Ё… гд  Ў»нё« е« «бћѕнѕ… ж«б н нёЏ Џбм —√”е« яб гд џжџб Maps ≈÷«Ё… ≈бм гќѕг «б»—нѕ «б≈бя —ждн Gmail ж«бб–нд ‘яб« ЁЏб« Џб«г… Ё«—ё… Ён Џ«бг «бжн» ж≈‘«—… ж«÷Ќ… ≈бм г« ” ƒжб ≈бне  Ў»нё«  «бжн» Ён «бг” ё»б «бё—н». Ёеб √Џћ» я «бЁя—…њ ”ж—н«° Ќб» Ён 13 √н«— 2007 г№';
          $lines = 7;
          
          $result = $this->arabic->a4_lines($text, $font_size);
          
          $this->assertEquals($lines, $result);
      }
      
      public function testArabicGlyphsRender()
      {
          $this->arabic->load('ArGlyphs');
          
          $text = '«б—Ќгд «б—Ќнг';
          $glyphs = 'пїҐпїіпЇ£пЇЃпїЯпЇН пї¶пї§пЇ£пЇЃпїЯпЇН';
          
          $result = $this->arabic->utf8Glyphs($text);
          
          $this->assertEquals($glyphs, $result);
      }
      
      public function testArabicSoundex()
      {
          $this->arabic->load('ArSoundex');
          
          $name = 'ябнд жд';
          $soundex = 'K453';
          
          $result = $this->arabic->soundex($name);
          
          $this->assertEquals($soundex, $result);
      }
      
      public function testArabicNounTrue1()
      {
          $this->arabic->load('ArWordTag');
          
          $word = '≈Ќ’«∆н…';
          $word_befor = 'жЌ”»';
          $noun = true;
          
          $result = $this->arabic->isNoun($word, $word_befor);
          
          $this->assertEquals($noun, $result);
      }
      
      public function testArabicNounTrue2()
      {
          $this->arabic->load('ArWordTag');
          
          $word = 'гЏ ёб«';
          $word_befor = '375';
          $noun = true;
          
          $result = $this->arabic->isNoun($word, $word_befor);
          
          $this->assertEquals($noun, $result);
      }
      
      public function testArabicNounTrue3()
      {
          $this->arabic->load('ArWordTag');
          
          $word = 'џж«д «д«гж';
          $word_befor = 'Ён';
          $noun = true;
          
          $result = $this->arabic->isNoun($word, $word_befor);
          
          $this->assertEquals($noun, $result);
      }
      
      public function testArabicNounTrue4()
      {
          $this->arabic->load('ArWordTag');
          
          $word = '«бЌ«ћ';
          $word_befor = '”«гн';
          $noun = true;
          
          $result = $this->arabic->isNoun($word, $word_befor);
          
          $this->assertEquals($noun, $result);
      }
      
      public function testArabicNounFalse1()
      {
          $this->arabic->load('ArWordTag');
          
          $word = ' жће';
          $word_befor = 'бг';
          $noun = false;
          
          $result = $this->arabic->isNoun($word, $word_befor);
          
          $this->assertEquals($noun, $result);
      }
      
      public function testArabicNounFalse2()
      {
          $this->arabic->load('ArWordTag');
          
          $word = 'нё»Џжд';
          $word_befor = 'гЏ ёб«';
          $noun = false;
          
          $result = $this->arabic->isNoun($word, $word_befor);
          
          $this->assertEquals($noun, $result);
      }
      
      public function testArabicNounHighlight()
      {
          $this->arabic->load('ArWordTag');
          
          $text = 'г« “«б дЌж 375 гЏ ёб« нё»Џжд Ён џж«д «д«гж ';
          $tagged = '  г«  “«б  дЌж 
<span style="background-color: #FFEEAA">  375  гЏ ёб«</span> 
  нё»Џжд  Ён 
<span style="background-color: #FFEEAA">  џж«д «д«гж</span> 
';
          
          $result = $this->arabic->highlightText($text, '#FFEEAA');
          
          $this->assertEquals($tagged, $result);
      }
      
      public function testDetectArabicUtf8Charset()
      {
          $this->arabic->load('ArCharsetD');
          
          $result = $this->arabic->getCharset($this->strUTF);
          
          $this->assertEquals('utf-8', $result);
      }
      
      public function testDetectArabicWin1256Charset()
      {
          $this->arabic->load('ArCharsetD');
          
          $result = $this->arabic->getCharset($this->strWIN);
          
          $this->assertEquals('windows-1256', $result);
      }
      
      public function testArabicStemWhereCondition1()
      {
          $this->arabic->load('ArQuery');
          
          $keyword = 'Ёб”Ўндн« ';
          $where = "(headline REGEXP 'Ёб”Ўндн(…|(«|√|≈|¬) )?')";
          
          $this->arabic->setStrFields('headline');
          $this->arabic->setMode(0);
          
          $result = $this->arabic->getWhereCondition($keyword);
          
          $this->assertEquals($where, $result);
      }
      
      public function testArabicStemWhereCondition2()
      {
          $this->arabic->load('ArQuery');
          
          $keyword = '≈—«ѕ… г” ёб…';
          $where = "(headline REGEXP '(«|√|≈|¬)—(«|√|≈|¬)ѕ(…|(«|√|≈|¬) )?') OR (headline REGEXP 'г” ёб(…|(«|√|≈|¬) )?')";
          
          $this->arabic->setStrFields('headline');
          $this->arabic->setMode(0);
          
          $result = $this->arabic->getWhereCondition($keyword);
          
          $this->assertEquals($where, $result);
      }
      
      public function testArabicStemOrderby1()
      {
          $this->arabic->load('ArQuery');
          
          $keyword = 'Ёб”Ўндн« ';
          $where = "((CASE WHEN headline REGEXP 'Ёб”Ўндн(…|(«|√|≈|¬) )?' THEN 1 ELSE 0 END)) DESC";
          
          $this->arabic->setStrFields('headline');
          $this->arabic->setMode(0);
          
          $result = $this->arabic->getOrderBy($keyword);
          
          $this->assertEquals($where, $result);
      }
      
      public function testArabicStemOrderby2()
      {
          $this->arabic->load('ArQuery');
          
          $keyword = '≈—«ѕ… г” ёб…';
          $where = "((CASE WHEN headline REGEXP '(«|√|≈|¬)—(«|√|≈|¬)ѕ(…|(«|√|≈|¬) )?' THEN 1 ELSE 0 END) + (CASE WHEN headline REGEXP 'г” ёб(…|(«|√|≈|¬) )?' THEN 1 ELSE 0 END)) DESC";
          
          $this->arabic->setStrFields('headline');
          $this->arabic->setMode(0);
          
          $result = $this->arabic->getOrderBy($keyword);
          
          $this->assertEquals($where, $result);
      }
      
      public function testMuslimPrayerTimes()
      {
          $this->arabic->load('Salat');
          
          $Fajr = '4:42';
          $Sunrise = '6:08';
          $Zuhr = '11:57';
          $Asr = '15:14';
          $Maghrib = '17:45';
          $Isha = '19:11';
          
          $this->arabic->setLocation(33.513, 36.292, 2);
          $this->arabic->setDate(7, 3, 2008);
          
          $result = $this->arabic->getPrayTime();
          
          $this->assertEquals($Fajr, $result[0]);
          $this->assertEquals($Sunrise, $result[1]);
          $this->assertEquals($Zuhr, $result[2]);
          $this->assertEquals($Asr, $result[3]);
          $this->assertEquals($Maghrib, $result[4]);
          $this->assertEquals($Isha, $result[5]);
      }
      
      public function testUtf8ArabicTextIdentifier()
      {
          $this->arabic->load('ArIdentifier');
          
          $text = 'Peace &nbsp; Ў≥ўДЎІўЕ &nbsp; &nbsp; Hasоtо';
          $pos = array(0 => 13, 1 => 22);
          
          $result = $this->arabic->identify($text);
          
          $this->assertEquals($pos[0], $result[0]);
          $this->assertEquals($pos[1], $result[1]);
      }

      public function testStrToTime1()
      {
          $this->arabic->load('ArStrToTime');
          
          $time = time();
          $this->arabic->setInputCharset('utf-8');

          $ar_text = 'ЎІўДЎЃўЕўКЎ≥ ЎІўДўВЎІЎѓўЕ';
          $en_text = 'Next Thursday';
          
          $ar_result = $this->arabic->strtotime($ar_text, $time);
          $en_result = strtotime($en_text, $time);

          $this->assertEquals($ar_result, $en_result);
      }

      public function testStrToTime2()
      {
          $this->arabic->load('ArStrToTime');
          
          $time = time();
          $this->arabic->setInputCharset('utf-8');

          $ar_text = 'ўВЎ®ўД Ў™Ў≥ЎєЎ© Ў£ўКЎІўЕ';
          $en_text = '-9 Days';
          
          $ar_result = $this->arabic->strtotime($ar_text, $time);
          $en_result = strtotime($en_text, $time);

          $this->assertEquals($ar_result, $en_result);
      }

      public function testStrToTime3()
      {
          $this->arabic->load('ArStrToTime');
          
          $time = time();
          $this->arabic->setInputCharset('utf-8');

          $ar_text = 'Ў®ЎєЎѓ Ў£Ў≥Ў®ўИЎє ўИЎЂўДЎІЎЂЎ© Ў£ўКЎІўЕ';
          $en_text = '+1 Week +3 Days';
          
          $ar_result = $this->arabic->strtotime($ar_text, $time);
          $en_result = strtotime($en_text, $time);

          $this->assertEquals($ar_result, $en_result);
      }

      public function testStrToTime4()
      {
          $this->arabic->load('ArStrToTime');
          
          $time = time();

          $hijri = '1 —г÷«д 1429';
          $unix_time_stamp  = 1220313600;
          
          $result = $this->arabic->strtotime($hijri, $time);

          $this->assertEquals($unix_time_stamp, $result);
      }
      
      public function testWindows1256ToHtml()
      {
          $this->arabic->load('ArCharsetC');
          
          $text = 'ќ«бѕ «б‘гЏ…';
          $html = '&#1582;&#1575;&#1604;&#1583; &#1575;&#1604;&#1588;&#1605;&#1593;&#1577;';
          
          $result = $this->arabic->win2html($text);
          
          $this->assertEquals($html, $result);
      }
      
      public function testAllWordForms()
      {
          $this->arabic->load('ArQuery');
          
          $word  = '«б≈’б«Ќнжд';
          $forms = '«б≈’б«Ќнжд ≈’б«Ќнжд ≈’б«Ќн ≈’б«Ќн… ≈’б«Ќн нд ≈’б«Ќннд ≈’б«Ќн«д ≈’б«Ќн«  ≈’б«Ќнж« «б«’б«Ќнжд «’б«Ќнжд «’б«Ќн «’б«Ќн… «’б«Ќн нд «’б«Ќннд «’б«Ќн«д «’б«Ќн«  «’б«Ќнж«';
          
          $result = $this->arabic->allForms($word);
          
          $this->assertEquals($forms, $result);
      }
      
      public function testIsArabicTrue()
      {
          $this->arabic->load('ArIdentifier');
          
          $arabic = 'ќ«бѕ «б‘гЏ…';
          $is_it  = true;
          
          $result = $this->arabic->isArabic($arabic, 'windows-1256');
          
          $this->assertEquals($is_it, $result);
      }
      
      public function testIsArabicFalse()
      {
          $this->arabic->load('ArIdentifier');
          
          $arabic = 'Khaled Al-Shamaa';
          $is_it  = false;
          
          $result = $this->arabic->isArabic($arabic);
          
          $this->assertEquals($is_it, $result);
      }
      
      public function testArabicStem1()
      {
          $this->arabic->load('ArStemmer');
          
          $word = 'ббћ«∆Џнд';
          $stem = 'ћ«∆Џ';
          
          $result = $this->arabic->stem($word);
          
          $this->assertEquals($stem, $result);
      }
      
      public function testArabicStem2()
      {
          $this->arabic->load('ArStemmer');
          
          $word = '»«бЌ«”ж»нд';
          $stem = 'Ќ«”ж»';
          
          $result = $this->arabic->stem($word);
          
          $this->assertEquals($stem, $result);
      }
      
      public function testArabicStem3()
      {
          $this->arabic->load('ArStemmer');
          
          $word = '«бќнбн« ';
          $stem = 'ќнб';
          
          $result = $this->arabic->stem($word);
          
          $this->assertEquals($stem, $result);
      }
      
      public function testArabicStem4()
      {
          $this->arabic->load('ArStemmer');
          
          $word = '»«бЎ—нё…';
          $stem = 'Ў—нё';
          
          $result = $this->arabic->stem($word);
          
          $this->assertEquals($stem, $result);
      }
      
      public function testArabicStandard()
      {
          $this->arabic->load('ArStandard');
          
          $sentence = 'е–« д’ Џ—»н ° ж Ёне Џб«г«   —ёнг »Ќ«ћ… ≈бм ÷»Ў ж гЏ«н—… !ж я–бя д’ж’( »нд √ёж«” )√ж Ќ м гƒЎ—…"»≈‘«—«  ≈ё »«” "√ж- Џб«г«  ≈Џ —«÷ -«бќ...... ';
          $standard = 'е–« д’ Џ—»н° жЁне Џб«г«   —ёнг »Ќ«ћ… ≈бм ÷»Ў жгЏ«н—…! жя–бя д’ж’ (»нд √ёж«”) √ж Ќ м гƒЎ—… "»≈‘«—«  ≈ё »«”" √ж -Џб«г«  ≈Џ —«÷- «бќ...';
          
          $result = $this->arabic->standard($sentence);
          
          $this->assertEquals($standard, $result);
      }
  }
?>