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
 * Class Name: Arabic Queary Class
 *  
 * Filename: ArQuery.class.php
 *  
 * Original  Author(s): Khaled Al-Sham'aa <khaled.alshamaa@gmail.com>
 *  
 * Purpose:  Build WHERE condition for SQL statement using MySQL REGEXP and
 *           Arabic lexical  rules
 *            
 * ----------------------------------------------------------------------
 *  
 * Arabic Queary Class
 *
 * PHP class build WHERE condition for SQL statement using MySQL REGEXP and 
 * Arabic lexical  rules.
 *    
 * With the exception of the Qur'an and pedagogical texts, Arabic is generally 
 * written without vowels or other graphic symbols that indicate how a word is 
 * pronounced. The reader is expected to fill these in from context. Some of the 
 * graphic symbols include sukuun, which is placed over a consonant to indicate that 
 * it is not followed by a vowel; shadda, written over a consonant to indicate it is 
 * doubled; and hamza, the sign of the glottal stop, which can be written above or 
 * below (alif) at the beginning of a word, or on (alif), (waaw), (yaa'), 
 * or by itself on the line elsewhere. Also, common spelling differences regularly 
 * appear, including the use of (haa') for (taa' marbuuta) and (alif maqsuura) 
 * for (yaa'). These features of written Arabic, which are also seen in Hebrew as 
 * well as other languages written with Arabic script (such as Farsi, Pashto, and 
 * Urdu), make analyzing and searching texts quite challenging. In addition, Arabic 
 * morphology and grammar are quite rich and present some unique issues for 
 * information retrieval applications.
 * 
 * There are essentially three ways to search an Arabic text with Arabic queries: 
 * literal, stem-based or root-based.
 * 
 * A literal search, the simplest search and retrieval method, matches documents 
 * based on the search terms exactly as the user entered them. The advantage of this 
 * technique is that the documents returned will without a doubt contain the exact 
 * term for which the user is looking. But this advantage is also the biggest 
 * disadvantage: many, if not most, of the documents containing the terms in 
 * different forms will be missed. Given the many ambiguities of written Arabic, the 
 * success rate of this method is quite low. For example, if the user searches 
 * for (kitaab, book), he or she will not find documents that only 
 * contain (`al-kitaabu, the book).
 * 
 * Stem-based searching, a more complicated method, requires some normalization of 
 * the original texts and the queries. This is done by removing the vowel signs, 
 * unifying the hamza forms and removing or standardizing the other signs. 
 * Additionally, grammatical affixes and other constructions which attach directly 
 * to words, such as conjunctions, prepositions, and the definite article, should be 
 * identified and removed. Finally, regular and irregular plural forms need to be 
 * identified and reduced to their singular forms. Performing this type of stemming 
 * leads to more successful searches, but can be problematic due to over-generation 
 * or incorrect generation of stems.
 * 
 * A third method for searching Arabic texts is to index and search for the root 
 * forms of each word. Since most verbs and nouns in Arabic are derived from 
 * triliteral (or, rarely, quadriliteral) roots, identifying the underlying root of 
 * each word theoretically retrieves most of the documents containing a given search 
 * term regardless of form. However, there are some significant challenges with this 
 * approach. Determining the root for a given word is extremely difficult, since it 
 * requires a detailed morphological, syntactic and semantic analysis of the text to 
 * fully disambiguate the root forms. The issue is complicated further by the fact 
 * that not all words are derived from roots. For example, loan words (words 
 * borrowed from another language) are not based on root forms, although there are 
 * even exceptions to this rule. For example, some loans that have a structure 
 * similar to triliteral roots, such as the English word film, are handled 
 * grammatically as if they were root-based, adding to the complexity of this type 
 * of search. Finally, the root can serve as the foundation for a wide variety of 
 * words with related meanings. The root (k-t-b) is used for many words related 
 * to writing, including (kataba, to write), (kitaab, book), (maktab, 
 * office), and (kaatib, author). But the same root is also used for regiment/
 * battalion, (katiiba). As a result, searching based on root forms results in 
 * very high recall, but precision is usually quite low.
 * 
 * While search and retrieval of Arabic text will never be an easy task, relying on 
 * linguistic analysis tools and methods can help make the process more successful. 
 * Ultimately, the search method you choose should depend on how critical it is to 
 * retrieve every conceivable instance of a word or phrase and the resources you 
 * have to process search returns in order to determine their true relevance.
 * 
 * This sidebar reprinted from #51 Volume 13 Issue 7 of MultiLingual Computing & 
 * Technology published by MultiLingual Computing, Inc., 319 North First Ave., 
 * Sandpoint, Idaho, USA, 208-263-8178, Fax: 208-263-6310.
 * 
 * Example:
 * <code>
 *     include('./Arabic.php');
 *     $Arabic = new Arabic('ArQuery');
 *     
 *     $dbuser = 'root';
 *     $dbpwd = '';
 *     $dbname = 'test';
 *     
 *     try {
 *         $dbh = new PDO('mysql:host=localhost;dbname='.$dbname, $dbuser, $dbpwd);
 * 
 *         // Set the error reporting attribute
 *         $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 * 
 *         $dbh->exec("SET NAMES 'utf8'");
 *     
 *         if ($_GET['keyword'] != '') {
 *             $keyword = @$_GET['keyword'];
 *             $keyword = str_replace('\"', '"', $keyword);
 *     
 *             $Arabic->ArQuery->setStrFields('headline');
 *             $Arabic->ArQuery->setMode($_GET['mode']);
 *     
 *             $strCondition = $Arabic->getWhereCondition($keyword);
 *         } else {
 *             $strCondition = '1';
 *         }
 *     
 *         $StrSQL = "SELECT `headline` FROM `aljazeera` WHERE $strCondition";
 * 
 *         $i = 0;
 *         foreach ($dbh->query($StrSQL) as $row) {
 *             $headline = $row['headline'];
 *             $i++;
 *             if ($i % 2 == 0) {
 *                 $bg = "#f0f0f0";
 *             } else {
 *                 $bg = "#ffffff";
 *             }
 *             echo "<tr bgcolor=\"$bg\"><td><font size=\"2\">$headline</font></td></tr>";
 *         }
 * 
 *         // Close the databse connection
 *         $dbh = null;
 * 
 *     } catch (PDOException $e) {
 *         echo $e->getMessage();
 *     }
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
// namespace Arabic/ArQuery;

/**
 * This PHP class build WHERE condition for SQL statement using MySQL REGEXP and
 * Arabic lexical  rules
 *  
 * @category  Text 
 * @package   Arabic
 * @author    Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
 * @copyright 2009 Khaled Al-Shamaa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */ 
class ArQuery
{
    private $_fields = array();
    
    /**
     * Setting value for $fields array
     *      
     * @param array $arrConfig Name of the fields that SQL statement will search
     *                         them (in array format where items are those fields names)
     *                       
     * @return boolean TRUE if success, or FALSE if fail
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function setArrFields($arrConfig)
    {
        $flag = true;
        
        if (is_array($arrConfig)) {
            // Get fields array
            $this->_fields = $arrConfig;
            
            // Error check!
            if (count($this->_fields) == 0) {
                $flag = false;
            }
        } else {
            $flag = false;
        }
        
        return $flag;
    }
    
    /**
     * Setting value for $fields array
     *      
     * @param string $strConfig Name of the fields that SQL statement will search
     *                          them (in string format using comma as delimated)
     *                          
     * @return boolean TRUE if success, or FALSE if fail
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function setStrFields($strConfig)
    {
        $flag = true;
        
        if (is_string($strConfig)) {
            // Get fields array
            $this->_fields = explode(',', $strConfig);
        } else {
            $flag = false;
        }
        return $flag;
    }
    
    /**
     * Setting $mode propority value that refer to search mode
     * [0 for OR logic | 1 for AND logic]
     *      
     * @param integer $mode Setting value to be saved in the $mode propority
     *      
     * @return boolean TRUE if success, or FALSE if fail
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function setMode($mode)
    {
        $flag = true;
        
        if (in_array($mode, array('0', '1'))) {
            // Set search mode [0 for OR logic | 1 for AND logic]
            $this->mode = $mode;
        } else {
            $flag = false;
        }
        
        return $flag;
    }
    
    /**
     * Getting $mode propority value that refer to search mode 
     * [0 for OR logic | 1 for AND logic]
     *      
     * @return integer Value of $mode properity
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function getMode()
    {
        // Get search mode value [0 for OR logic | 1 for AND logic]
        return $this->mode;
    }
    
    /**
     * Getting values of $fields Array in array format
     *      
     * @return array Value of $fields array in Array format
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function getArrFields()
    {
        $fields = $this->_fields;
        
        return $fields;
    }
    
    /**
     * Getting values of $fields array in String format (comma delimated)
     *      
     * @return string Values of $fields array in String format (comma delimated)
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function getStrFields()
    {
        $fields = implode(',', $this->_fields);
        
        return $fields;
    }
    
    /**
     * Build WHERE section of the SQL statement using defind lex's rules, search 
     * mode [AND | OR], and handle also phrases (inclosed by "") using normal 
     * LIKE condition to match it as it is.
     *      
     * @param string $arg           String that user search for in the database table
     * @param string $inputCharset  (optional) Input charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set input charset)       
     * @param string $outputCharset (optional) Output charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set output charset)       
     * @param object $main          Main Ar-PHP object to access charset converter options
     *                    
     * @return string The WHERE section in SQL statement (MySQL database engine format)
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function getWhereCondition($arg, $inputCharset = null, $outputCharset = null, $main = null)
    {
        if ($main) {
            if ($inputCharset == null) $inputCharset = $main->getInputCharset();
            $arg = $main->coreConvert($arg, $inputCharset, 'windows-1256');
        }

        $sql = '';
        $arg = mysql_escape_string($arg);
        
        // Check if there are phrases in $arg should handle as it is
        $phrase = explode("\"", $arg);
        
        if (count($phrase) > 2) {
            // Re-init $arg variable
            // (It will contain the rest of $arg except phrases).
            $arg = '';
            
            for ($i = 0; $i < count($phrase); $i++) {
                $sub_phrase = $phrase[$i]; 
                if ($i % 2 == 0 && $sub_phrase != '') {
                    // Re-build $arg variable after restricting phrases
                    $arg .= $sub_phrase;
                } elseif ($i % 2 == 1 && $sub_phrase != '') {
                    // Handle phrases using reqular LIKE matching in MySQL
                    $this->wordCondition[] = $this->_getWordLike($sub_phrase);
                }
            }
        }
        
        // Handle normal $arg using lex's and regular expresion
        $words = explode(' ', $arg);
        
        foreach ($words as $word) {
            //if (is_numeric($word) || strlen($word) > 2) {
                // Take off all the punctuation
                //$word = preg_replace("/\p{P}/", '', $word);
                $exclude = array('(', ')', '[', ']', '{', '}', ',', ';', ':', '?', '!', '°', '∫', 'ø');
                $word = str_replace($exclude, '', $word);

                $this->wordCondition[] = $this->_getWordRegExp($word);
            //}
        }
        
        if (!empty($this->wordCondition)) {
            if ($this->mode == 0) {
                $sql = '(' . implode(') OR (', $this->wordCondition) . ')';
            } elseif ($this->mode == 1) {
                $sql = '(' . implode(') AND (', $this->wordCondition) . ')';
            }
        }
        
        if ($main) {
            if ($outputCharset == null) $outputCharset = $main->getOutputCharset();
            $sql = $main->coreConvert($sql, 'windows-1256', $outputCharset);
        }
        
        return $sql;
    }
    
    /**
     * Search condition in SQL format for one word in all defind fields using 
     * REGEXP clause and lex's rules
     *      
     * @param string $arg String (one word) that you want to build a condition for
     *      
     * @return string sub SQL condition (for private use)
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _getWordRegExp($arg)
    {
        $arg = $this->_lex($arg);
        $sql = implode(" REGEXP '$arg' OR ", $this->_fields) . " REGEXP '$arg'";
        
        return $sql;
    }
    
    /**
     * Search condition in SQL format for one word in all defind fields using 
     * normal LIKE clause
     *      
     * @param string $arg String (one word) that you want to build a condition for
     *      
     * @return string sub SQL condition (for private use)
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _getWordLike($arg)
    {
        $sql = implode(" LIKE '$arg' OR ", $this->_fields) . " LIKE '$arg'";
        
        return $sql;
    }
    
    /**
     * Get more relevant order by section related to the user search keywords
     *      
     * @param string $arg           String that user search for in the database table
     * @param string $inputCharset  (optional) Input charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set input charset)       
     * @param string $outputCharset (optional) Output charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set output charset)       
     * @param object $main          Main Ar-PHP object to access charset converter options
     *                    
     * @return string sub SQL ORDER BY section 
     * @author Saleh AlMatrafe <saleh@saleh.cc>
     */
    public function getOrderBy($arg, $inputCharset = null, $outputCharset = null, $main = null)
    {
        if ($main) {
            if ($inputCharset == null) $inputCharset = $main->getInputCharset();
            $arg = $main->coreConvert($arg, $inputCharset, 'windows-1256');
        }

        // Check if there are phrases in $arg should handle as it is
        $phrase = explode("\"", $arg);
        if (count($phrase) > 2) {
            // Re-init $arg variable (It will contain the rest of $arg except phrases).
            $arg = '';
            for ($i = 0; $i < count($phrase); $i++) {
                if ($i % 2 == 0 && $phrase[$i] != '') {
                    // Re-build $arg variable after restricting phrases
                    $arg .= $phrase[$i];
                } elseif ($i % 2 == 1 && $phrase[$i] != '') {
                    // Handle phrases using reqular LIKE matching in MySQL
                    $wordOrder[] = $this->_getWordLike($phrase[$i]);
                }
            }
        }
        
        // Handle normal $arg using lex's and regular expresion
        $words = explode(' ', $arg);
        foreach ($words as $word) {
            if ($word != '') {
                $wordOrder[] = 'CASE WHEN ' . $this->_getWordRegExp($word) . ' THEN 1 ELSE 0 END';
            }
        }
        
        $order = '((' . implode(') + (', $wordOrder) . ')) DESC';
        
        if ($main) {
            if ($outputCharset == null) $outputCharset = $main->getOutputCharset();
            $order = $main->coreConvert($order, 'windows-1256', $outputCharset);
        }

        return $order;
    }

    /**
     * This method will implement various regular expressin rules based on 
     * pre-defined Arabic lexical rules
     *      
     * @param string $arg String of one word user want to search for
     *      
     * @return string Regular Expression format to be used in MySQL query statement
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _lex($arg)
    {
        $patterns = array();
        $replacements = array();
        
        // Prefix's
        array_push($patterns, '/^«·/');
        array_push($replacements, '(«·)?');
        
        // Singular
        array_push($patterns, '/(\S{3,}) Ì‰$/');
        array_push($replacements, '\\1( Ì‰|…)?');
        
        array_push($patterns, '/(\S{3,})Ì‰$/');
        array_push($replacements, '\\1(Ì‰)?');
        
        array_push($patterns, '/(\S{3,})Ê‰$/');
        array_push($replacements, '\\1(Ê‰)?');
        
        array_push($patterns, '/(\S{3,})«‰$/');
        array_push($replacements, '\\1(«‰)?');
        
        array_push($patterns, '/(\S{3,}) «$/');
        array_push($replacements, '\\1( «)?');
        
        array_push($patterns, '/(\S{3,})«$/');
        array_push($replacements, '\\1(«)?');
        
        array_push($patterns, '/(\S{3,})(…|« )$/');
        array_push($replacements, '\\1(…|« )?');
        
        // Postfix's
        array_push($patterns, '/(\S{3,})Â„«$/');
        array_push($replacements, '\\1(Â„«)?');
        
        array_push($patterns, '/(\S{3,})ﬂ„«$/');
        array_push($replacements, '\\1(ﬂ„«)?');
        
        array_push($patterns, '/(\S{3,})‰Ì$/');
        array_push($replacements, '\\1(‰Ì)?');
        
        array_push($patterns, '/(\S{3,})ﬂ„$/');
        array_push($replacements, '\\1(ﬂ„)?');
        
        array_push($patterns, '/(\S{3,}) „$/');
        array_push($replacements, '\\1( „)?');
        
        array_push($patterns, '/(\S{3,})ﬂ‰$/');
        array_push($replacements, '\\1(ﬂ‰)?');
        
        array_push($patterns, '/(\S{3,}) ‰$/');
        array_push($replacements, '\\1( ‰)?');
        
        array_push($patterns, '/(\S{3,})‰«$/');
        array_push($replacements, '\\1(‰«)?');
        
        array_push($patterns, '/(\S{3,})Â«$/');
        array_push($replacements, '\\1(Â«)?');
        
        array_push($patterns, '/(\S{3,})Â„$/');
        array_push($replacements, '\\1(Â„)?');
        
        array_push($patterns, '/(\S{3,})Â‰$/');
        array_push($replacements, '\\1(Â‰)?');
        
        array_push($patterns, '/(\S{3,})Ê«$/');
        array_push($replacements, '\\1(Ê«)?');
        
        array_push($patterns, '/(\S{3,})Ì…$/');
        array_push($replacements, '\\1(Ì|Ì…)?');
        
        array_push($patterns, '/(\S{3,})‰$/');
        array_push($replacements, '\\1(‰)?');
        
        // Writing errors
        array_push($patterns, '/(…|Â)$/');
        array_push($replacements, '(…|Â)');
        
        array_push($patterns, '/(…| )$/');
        array_push($replacements, '(…| )');
        
        array_push($patterns, '/(Ì|Ï)$/');
        array_push($replacements, '(Ì|Ï)');
        
        array_push($patterns, '/(«|Ï)$/');
        array_push($replacements, '(«|Ï)');
        
        array_push($patterns, '/(∆|Ï¡|ƒ|Ê¡|¡)/');
        array_push($replacements, '(∆|Ï¡|ƒ|Ê¡|¡)');
        
        // Normalization
        array_push($patterns, '/¯|Û||ı|Ò|ˆ|Ú|˙/');
        array_push($replacements, '(¯|Û||ı|Ò|ˆ|Ú|˙)?');
        
        array_push($patterns, '/«|√|≈|¬/');
        array_push($replacements, '(«|√|≈|¬)');
        
        $arg = preg_replace($patterns, $replacements, $arg);
        
        return $arg;
    }
    
    /**
     * Get most possible Arabic lexical forms for a given word
     *      
     * @param string $word String that user search for
     *      
     * @return string list of most possible Arabic lexical forms for a given word 
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    private function _allWordForms($word) 
    {
        $wordForms = array($word);
        
        $postfix1 = array('ﬂ„', 'ﬂ‰', '‰«', 'Â«', 'Â„', 'Â‰');
        $postfix2 = array('Ì‰', 'Ê‰', '«‰', '« ', 'Ê«');
        
        $len = strlen($word);

        if (substr($word, 0, 2) == '«·') {
            $word = substr($word, 2);
        }
        
        $wordForms[] = $word;

        $str1 = substr($word, 0, -1);
        $str2 = substr($word, 0, -2);
        $str3 = substr($word, 0, -3);

        $last1 = substr($word, -1);
        $last2 = substr($word, -2);
        $last3 = substr($word, -3);
        
        if ($len >= 6 && $last3 == ' Ì‰') {
            $wordForms[] = $str3;
            $wordForms[] = $str3 . '…';
            $wordForms[] = $word . '…';
        }
        
        if ($len >= 6 && ($last3 == 'ﬂ„«' || $last3 == 'Â„«')) {
            $wordForms[] = $str3;
            $wordForms[] = $str3 . 'ﬂ„«';
            $wordForms[] = $str3 . 'Â„«';
        }

        if ($len >= 5 && in_array($last2, $postfix2)) {
            $wordForms[] = $str2;
            $wordForms[] = $str2.'…';
            $wordForms[] = $str2.' Ì‰';

            foreach ($postfix2 as $postfix) {
                $wordForms[] = $str2 . $postfix;
            }
        }

        if ($len >= 5 && in_array($last2, $postfix1)) {
            $wordForms[] = $str2;
            $wordForms[] = $str2.'Ì';
            $wordForms[] = $str2.'ﬂ';
            $wordForms[] = $str2.'ﬂ„«';
            $wordForms[] = $str2.'Â„«';

            foreach ($postfix1 as $postfix) {
                $wordForms[] = $str2 . $postfix;
            }
        }

        if ($len >= 5 && $last2 == 'Ì…') {
            $wordForms[] = $str1;
            $wordForms[] = $str2;
        }

        if (($len >= 4 && ($last1 == '…' || $last1 == 'Â' || $last1 == ' ')) || ($len >= 5 && $last2 == '« ')) {
            $wordForms[] = $str1;
            $wordForms[] = $str1 . '…';
            $wordForms[] = $str1 . 'Â';
            $wordForms[] = $str1 . ' ';
            $wordForms[] = $str1 . '« ';
        }
        
        if ($len >= 4 && $last1 == 'Ï') {
            $wordForms[] = $str1 . '«';
        }

        $trans = array('√' => '«', '≈' => '«', '¬' => '«');
        foreach ($wordForms as $word) {
            $normWord = strtr($word, $trans);
            if ($normWord != $word) $wordForms[] = $normWord;
        }
        
        $wordForms = array_unique($wordForms);
        

        
        return $wordForms;
    }
    
    /**
     * Get most possible Arabic lexical forms of user search keywords
     *      
     * @param string $arg           String that user search for
     * @param string $inputCharset  (optional) Input charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set input charset)       
     * @param string $outputCharset (optional) Output charset [utf-8|windows-1256|iso-8859-6]
     *                              default value is NULL (use set output charset)       
     * @param object $main          Main Ar-PHP object to access charset converter options
     *                    
     * @return string list of most possible Arabic lexical forms for given keywords 
     * @author Khaled Al-Shamaa <khaled.alshamaa@gmail.com>
     */
    public function allForms($arg, $inputCharset = null, $outputCharset = null, $main = null)
    {
        if ($main) {
            if ($inputCharset == null) $inputCharset = $main->getInputCharset();
            $arg = $main->coreConvert($arg, $inputCharset, 'windows-1256');
        }

        $wordForms = array();
        $words = explode(' ', $arg);
        
        foreach ($words as $word) {
            $wordForms = array_merge($wordForms, $this->_allWordForms($word));
        }
        
        $str = implode(' ', $wordForms);

        if ($main) {
            if ($outputCharset == null) $outputCharset = $main->getOutputCharset();
            $str = $main->coreConvert($str, 'windows-1256', $outputCharset);
        }
        
        return $str;
    }
}
?>
