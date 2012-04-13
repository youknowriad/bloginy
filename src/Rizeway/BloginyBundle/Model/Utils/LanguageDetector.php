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

namespace Rizeway\BloginyBundle\Model\Utils;

class LanguageDetector
{
    const GOOGLE_URL = 'http://www.google.com/uds/GlangDetect?v=1.0&q=';
    const BING_URL = 'http://api.microsofttranslator.com/V2/Http.svc/Detect?appId=%s&text=';

    /**
     * @var string
     */
    private $alchemy_api_key;

    /**
     * @var string
     */
    private $bing_app_id;

    public function __construct($alchemy_apikey, $bing_app_id)
    {
        $this->alchemy_api_key = $alchemy_apikey;
        $this->bing_app_id = $bing_app_id;
    }

    /**
     *
     * @param string $text
     * @param array $languages
     * @return string
     */
    public function detect($text, $languages = null)
    {
        try
        {
            $language = $this->bingDetect($text);
            $language = ($language) ? $language : $this->googleDetect($text);
            $language = ($language) ? $language : $this->alchemyDetect($text);

            if (!$language || (\is_array($languages) && !isset($languages[$language])))
            {
                return 'other';
            }

            return $language;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }
    
    public function googleDetect($text)
    {
        $url = self::GOOGLE_URL . urlencode($text);
        $result = \json_decode(file_get_contents($url), true);

        if ($result['responseStatus'] == 200)
        {
            return $result['responseData']['language'];
        }
        else
        {
            return false;
        }
    }
    
    public function alchemyDetect($text)
    {
        $alchemyObj = new \AlchemyAPI();
        $alchemyObj->setAPIKey($this->alchemy_api_key);
        $result = \json_decode($alchemyObj->TextGetLanguage($text, \AlchemyAPI::JSON_OUTPUT_MODE), true);

        return (isset($result['iso-639-1'])) ? $result['iso-639-1'] : false;     
    }
    
    public function bingDetect($text)
    {
        $url = sprintf(self::BING_URL, $this->bing_app_id) . urlencode($text);
        $result = \strip_tags(file_get_contents($url));

        return $result;
    }
}
