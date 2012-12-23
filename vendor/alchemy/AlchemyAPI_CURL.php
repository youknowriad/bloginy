<?php

class AlchemyAPI
{
	const XML_OUTPUT_MODE = "xml";
	const JSON_OUTPUT_MODE = "json";

	private $_apiKey = '';
	private $_hostPrefix = 'access';

	public function setAPIHost($apiHost)
	{
		$this->_hostPrefix = $apiHost;

		if (strlen($this->_hostPrefix) < 2)
		{
			throw new Exception("Error setting API host.");
		}
	}

	public function setAPIKey($apiKey)
	{
		$this->_apiKey = $apiKey;

		if (strlen($this->_apiKey) < 5)
		{
			throw new Exception("Error setting API key.");
		}
	}

	public function loadAPIKey($filename)
	{
		$handle = fopen($filename, 'r');
		$theData = fgets($handle, 512);
		fclose($handle);
		$this->_apiKey = rtrim($theData);

		if (strlen($this->_apiKey) < 5)
		{
			throw new Exception("Error loading API key.");
		}
	}

	public function URLGetAuthor($url, $outputMode = self::XML_OUTPUT_MODE, $params = null)
	{

		$this->CheckURL($url, $outputMode);
		$this->CheckParamType("AlchemyAPI_Params", $params);

		if(is_null($params))
			$params = new AlchemyAPI_Params();

		$params->setUrl($url);
		$params->SetOutputMode($outputMode);

		return $this->GET("URLGetAuthor", "url", $params);

	}

	public function HTMLGetAuthor($html, $url, $outputMode = self::XML_OUTPUT_MODE, $params = null)
	{
		$this->CheckHTML($html, $url, $outputMode);
		$this->CheckParamType("AlchemyAPI_Params", $params);

		if(is_null($params))
			$params = new AlchemyAPI_Params();

		$params->setHtml($html);
		$params->setUrl($url);
		$params->SetOutputMode($outputMode);

        
		return $this->POST("HTMLGetAuthor", "html", $params);
	}

	public function URLGetRankedNamedEntities($url, $outputMode = self::XML_OUTPUT_MODE, $namedEntityParams = null)
	{
		$this->CheckURL($url, $outputMode);
		$this->CheckParamType("AlchemyAPI_NamedEntityParams", $namedEntityParams);
		
		if(is_null($namedEntityParams))
			$namedEntityParams = new AlchemyAPI_NamedEntityParams();
		
		$namedEntityParams->setUrl($url);
		$namedEntityParams->setOutputMode($outputMode);

		return $this->GET("URLGetRankedNamedEntities", "url", $namedEntityParams);
	}

	public function HTMLGetRankedNamedEntities($html, $url, $outputMode = self::XML_OUTPUT_MODE, $namedEntityParams = null)
	{
		$this->CheckHTML($html, $url, $outputMode);
		$this->CheckParamType("AlchemyAPI_NamedEntityParams", $namedEntityParams);
				
		if(is_null($namedEntityParams))
			$namedEntityParams = new AlchemyAPI_NamedEntityParams();
		
		$namedEntityParams->setHtml($html);
		$namedEntityParams->setUrl($url);
		$namedEntityParams->setOutputMode($outputMode);

		return $this->POST("HTMLGetRankedNamedEntities", "html", $namedEntityParams);
	}

	public function TextGetRankedNamedEntities($text, $outputMode = self::XML_OUTPUT_MODE, $namedEntityParams = null)
	{
		$this->CheckText($text, $outputMode);
		$this->CheckParamType("AlchemyAPI_NamedEntityParams", $namedEntityParams);
		
		if(is_null($namedEntityParams))
			$namedEntityParams = new AlchemyAPI_NamedEntityParams();
		
		$namedEntityParams->setText($text);
		$namedEntityParams->setOutputMode($outputMode);

		return $this->POST("TextGetRankedNamedEntities", "text", $namedEntityParams);
	}

	public function URLGetRankedConcepts($url, $outputMode = self::XML_OUTPUT_MODE, $conceptParams = null)
	{
		$this->CheckURL($url, $outputMode);
		$this->CheckParamType("AlchemyAPI_ConceptParams", $conceptParams);
		
		if(is_null($conceptParams))
			$conceptParams = new AlchemyAPI_ConceptParams();
		
		$conceptParams->setUrl($url);
		$conceptParams->setOutputMode($outputMode);

		return $this->GET("URLGetRankedConcepts", "url", $conceptParams);
	}

	public function HTMLGetRankedConcepts($html, $url, $outputMode = self::XML_OUTPUT_MODE, $conceptParams = null)
	{
		$this->CheckHTML($html, $url, $outputMode);
		$this->CheckParamType("AlchemyAPI_ConceptParams", $conceptParams);
		
		if(is_null($conceptParams))
			$conceptParams = new AlchemyAPI_ConceptParams();
		
		$conceptParams->setHtml($html);
		$conceptParams->setUrl($url);
		$conceptParams->setOutputMode($outputMode);

		return $this->POST("HTMLGetRankedConcepts", "html", $conceptParams);
	}

	public function TextGetRankedConcepts($text, $outputMode = self::XML_OUTPUT_MODE, $conceptParams = null)
	{
		$this->CheckText($text, $outputMode);
		$this->CheckParamType("AlchemyAPI_ConceptParams", $conceptParams);
		
		if(is_null($conceptParams))
			$conceptParams = new AlchemyAPI_ConceptParams();
		
		$conceptParams->setText($text);
		$conceptParams->setOutputMode($outputMode);

		return $this->POST("TextGetRankedConcepts", "text", $conceptParams);
	}

	public function URLGetRankedKeywords($url, $outputMode = self::XML_OUTPUT_MODE, $keywordParams = null)
	{
		$this->CheckURL($url, $outputMode);
		$this->CheckParamType("AlchemyAPI_KeywordParams", $keywordParams);
		
		if(is_null($keywordParams))
			$keywordParams = new AlchemyAPI_KeywordParams();
		
		$keywordParams->setUrl($url);
		$keywordParams->setOutputMode($outputMode);

		return $this->GET("URLGetRankedKeywords", "url", $keywordParams);
	}

	public function HTMLGetRankedKeywords($html, $url, $outputMode = self::XML_OUTPUT_MODE, $keywordParams = null)
	{
		$this->CheckHTML($html, $url, $outputMode);
		$this->CheckParamType("AlchemyAPI_KeywordParams", $keywordParams);
		
		if(is_null($keywordParams))
			$keywordParams = new AlchemyAPI_KeywordParams();
		
		$keywordParams->setHtml($html);
		$keywordParams->setUrl($url);
		$keywordParams->setOutputMode($outputMode);

		return $this->POST("HTMLGetRankedKeywords", "html", $keywordParams);
	}

	public function TextGetRankedKeywords($text, $outputMode = self::XML_OUTPUT_MODE, $keywordParams = null)
	{
		$this->CheckText($text, $outputMode);
		$this->CheckParamType("AlchemyAPI_KeywordParams", $keywordParams);
		
			if(is_null($keywordParams))
			$keywordParams = new AlchemyAPI_KeywordParams();
		
		$keywordParams->setText($text);
		$keywordParams->setOutputMode($outputMode);

		return $this->POST("TextGetRankedKeywords", "text", $keywordParams);
	}

	public function URLGetLanguage($url, $outputMode = self::XML_OUTPUT_MODE, $languageParams = null)
	{
		$this->CheckURL($url, $outputMode);
		$this->CheckParamType("AlchemyAPI_LanguageParams", $languageParams);
		
		if(is_null($languageParams))
			$languageParams = new AlchemyAPI_LanguageParams();
		
		$languageParams->setUrl($url);
		$languageParams->setOutputMode($outputMode);

		return $this->GET("URLGetLanguage", "url", $languageParams);
	}

	public function HTMLGetLanguage($html, $url, $outputMode = self::XML_OUTPUT_MODE, $languageParams = null)
	{
		$this->CheckHTML($html, $url, $outputMode);
		$this->CheckParamType("AlchemyAPI_LanguageParams", $languageParams);
		
		if(is_null($languageParams))
			$languageParams = new AlchemyAPI_LanguageParams();
		
		$languageParams->setHtml($html);
		$languageParams->setUrl($url);
		$languageParams->setOutputMode($outputMode);

		return $this->POST("HTMLGetLanguage", "html", $languageParams);
	}

	public function TextGetLanguage($text, $outputMode = self::XML_OUTPUT_MODE, $languageParams = null)
	{
		$this->CheckText($text, $outputMode);
		$this->CheckParamType("AlchemyAPI_LanguageParams", $languageParams);
		
		if(is_null($languageParams))
			$languageParams = new AlchemyAPI_LanguageParams();
		
		$languageParams->setText($text);
		$languageParams->setOutputMode($outputMode);

		return $this->POST("TextGetLanguage", "text", $languageParams);
	}
	

	public function URLGetCategory($url, $outputMode = self::XML_OUTPUT_MODE, $categorizeParams = null)
	{
		$this->CheckURL($url, $outputMode);
		$this->CheckParamType("AlchemyAPI_CategoryParams", $categorizeParams);
		
		if(is_null($categorizeParams))
			$categorizeParams = new AlchemyAPI_CategoryParams();
		
		$categorizeParams->setUrl($url);
		$categorizeParams->setOutputMode($outputMode);

		return $this->GET("URLGetCategory", "url", $categorizeParams);
	}

	public function HTMLGetCategory($html, $url, $outputMode = self::XML_OUTPUT_MODE, $categorizeParams = null)
	{
		$this->CheckHTML($html, $url, $outputMode);
		$this->CheckParamType("AlchemyAPI_CategoryParams", $categorizeParams);
		
		if(is_null($categorizeParams))
			$categorizeParams = new AlchemyAPI_CategoryParams();
		
		$categorizeParams->setHtml($html);
		$categorizeParams->setUrl($url);
		$categorizeParams->setOutputMode($outputMode);

		return $this->POST("HTMLGetCategory", "html", $categorizeParams);
	}

	public function TextGetCategory($text, $outputMode = self::XML_OUTPUT_MODE, $categorizeParams = null)
	{
		$this->CheckText($text, $outputMode);
		$this->CheckParamType("AlchemyAPI_CategoryParams", $categorizeParams);
		
		if(is_null($categorizeParams))
			$categorizeParams = new AlchemyAPI_CategoryParams();
		
		$categorizeParams->setText($text);
		$categorizeParams->setOutputMode($outputMode);

		return $this->POST("TextGetCategory", "text", $categorizeParams);
	}

	public function URLGetText($url, $outputMode = self::XML_OUTPUT_MODE, $textParams = null)
	{
		$this->CheckURL($url, $outputMode);
		$this->CheckParamType("AlchemyAPI_TextParams", $textParams);
		
		if(is_null($textParams))
			$textParams = new AlchemyAPI_TextParams();
		
		$textParams->setUrl($url);
		$textParams->setOutputMode($outputMode);

		return $this->GET("URLGetText", "url", $textParams);
	}

	public function HTMLGetText($html, $url, $outputMode = self::XML_OUTPUT_MODE, $textParams = null)
	{
		$this->CheckHTML($html, $url, $outputMode);
		$this->CheckParamType("AlchemyAPI_TextParams", $textParams);
		
		if(is_null($textParams))
			$textParams = new AlchemyAPI_TextParams();
		
		$textParams->setHtml($html);
		$textParams->setUrl($url);
		$textParams->setOutputMode($outputMode);

		return $this->POST("HTMLGetText", "html", $textParams);
	}

	public function URLGetRawText($url, $outputMode = self::XML_OUTPUT_MODE, $params = null)
	{
		$this->CheckURL($url, $outputMode);
		$this->CheckParamType("AlchemyAPI_Params", $params);
		
		if(is_null($params))
			$params = new AlchemyAPI_Params();
		
		$params->setUrl($url);
		$params->setOutputMode($outputMode);
		
		return $this->GET("URLGetRawText", "url", $params);
	}

	public function HTMLGetRawText($html, $url, $outputMode = self::XML_OUTPUT_MODE, $params = null)
	{
		$this->CheckHTML($html, $url, $outputMode);
		$this->CheckParamType("AlchemyAPI_Params", $params);
		
		if(is_null($params))
			$params = new AlchemyAPI_TextParams();
		
		$params->setHtml($html);
		$params->setUrl($url);
		$params->setOutputMode($outputMode);

		return $this->POST("HTMLGetRawText", "html", $params);
	}

	public function URLGetTitle($url, $outputMode = self::XML_OUTPUT_MODE, $params = null)
	{
		$this->CheckURL($url, $outputMode);
		$this->CheckParamType("AlchemyAPI_Params", $params);
		
		if(is_null($params))
			$params = new AlchemyAPI_Params();
		
		$params->setUrl($url);
		$params->setOutputMode($outputMode);

		return $this->GET("URLGetTitle", "url", $params);
	}

	public function HTMLGetTitle($html, $url, $outputMode = self::XML_OUTPUT_MODE, $params = null)
	{
		$this->CheckHTML($html, $url, $outputMode);
		$this->CheckParamType("AlchemyAPI_Params", $params);
		
		if(is_null($params))
			$params = new AlchemyAPI_Params();
		
		$params->setHtml($html);
		$params->setUrl($url);
		$params->setOutputMode($outputMode);

		return $this->POST("HTMLGetTitle", "html", $params);
	}

	public function URLGetFeedLinks($url, $outputMode = self::XML_OUTPUT_MODE, $params = null)
	{
		$this->CheckURL($url, $outputMode);
		$this->CheckParamType("AlchemyAPI_Params", $params);
		
		if(is_null($params))
			$params = new AlchemyAPI_Params();
		
		$params->setUrl($url);
		$params->setOutputMode($outputMode);

		return $this->GET("URLGetFeedLinks", "url", $params);
	}

	public function HTMLGetFeedLinks($html, $url, $outputMode = self::XML_OUTPUT_MODE, $params = null)
	{
		$this->CheckHTML($html, $url, $outputMode);
		$this->CheckParamType("AlchemyAPI_Params", $params);
	
		if(is_null($params))
			$params = new AlchemyAPI_Params();
		
		
		$params->setHtml($html);
		$params->setUrl($url);
		$params->setOutputMode($outputMode);

		return $this->POST("HTMLGetFeedLinks", "html", $params);
	}

	public function URLGetMicroformats($url, $outputMode = self::XML_OUTPUT_MODE, $params = null)
	{
		$this->CheckURL($url, $outputMode);
		$this->CheckParamType("AlchemyAPI_Params", $params);
		
		if(is_null($params))
			$params = new AlchemyAPI_Params();
		
		$params->setUrl($url);
		$params->setOutputMode($outputMode);

		return $this->GET("URLGetMicroformatData", "url", $params);
	}

	public function HTMLGetMicroformats($html, $url, $outputMode = self::XML_OUTPUT_MODE, $params = null)
	{
		$this->CheckHTML($html, $url, $outputMode);
		$this->CheckParamType("AlchemyAPI_Params", $params);
		
		if(is_null($params))
			$params = new AlchemyAPI_Params();
		
		$params->setHtml($html);
		$params->setUrl($url);
		$params->setOutputMode($outputMode);

		return $this->POST("HTMLGetMicroformatData", "html", $params);
	}

	public function URLGetConstraintQuery($url, $query, $outputMode = self::XML_OUTPUT_MODE, $constraintParams = null)
    {
        $this->CheckURL($url, $outputMode);
		$this->CheckParamType("AlchemyAPI_ConstraintQueryParams", $constraintParams);
		
        if (strlen($query) < 2)
        {
            throw new Exception("Invalid constraint query specified.");
        }
		
		if(is_null($constraintParams))
			$constraintParams = new AlchemyAPI_ConstraintQueryParams();
		
		$constraintParams->setUrl($url);
		$constraintParams->setOutputMode($outputMode);
		$constraintParams->setCQuery($query);

        return $this->GET("URLGetConstraintQuery", "url", $constraintParams);
    }

    public function HTMLGetConstraintQuery($html, $url, $query, $outputMode = self::XML_OUTPUT_MODE, $constraintParams = null)
    {
        $this->CheckHTML($html, $url, $outputMode);
		$this->CheckParamType("AlchemyAPI_ConstraintQueryParams", $constraintParams);
		
        if (strlen($query) < 2)
        {
            throw new Exception("Invalid constraint query specified.");
        }
				
		$constraintParams = new AlchemyAPI_ConstraintQueryParams();
		
		$constraintParams->setUrl($url);
		$constraintParams->setHtml($html);
		$constraintParams->setOutputMode($outputMode);
		$constraintParams->setCQuery($query);

        return $this->POST("HTMLGetConstraintQuery", "html", $constraintParams);
    }
	
	public function URLGetTextSentiment($url, $outputMode = self::XML_OUTPUT_MODE, $baseParams = null)
	{
		$this->CheckURL($url, $outputMode);
		$this->CheckParamType("AlchemyAPI_Params", $baseParams);
		
		if(is_null($baseParams))
			$baseParams = new AlchemyAPI_Params();
		
		$baseParams->setUrl($url);
		$baseParams->setOutputMode($outputMode);

		return $this->GET("URLGetTextSentiment", "url", $baseParams);
	}

	public function HTMLGetTextSentiment($html, $url, $outputMode = self::XML_OUTPUT_MODE, $baseParams = null)
	{
		$this->CheckHTML($html, $url, $outputMode);
		$this->CheckParamType("AlchemyAPI_Params", $baseParams);
		
		if(is_null($baseParams))
			$baseParams = new AlchemyAPI_Params();
		
		$baseParams->setHtml($html);
		$baseParams->setUrl($url);
		$baseParams->setOutputMode($outputMode);

		return $this->POST("HTMLGetTextSentiment", "html", $baseParams);
	}

	public function TextGetTextSentiment($text, $outputMode = self::XML_OUTPUT_MODE, $baseParams = null)
	{
		$this->CheckText($text, $outputMode);
		$this->CheckParamType("AlchemyAPI_Params", $baseParams);
		
		if(is_null($baseParams))
			$baseParams = new AlchemyAPI_Params();
		
		$baseParams->setText($text);
		$baseParams->setOutputMode($outputMode);

		return $this->POST("TextGetTextSentiment", "text", $baseParams);
	}
	
	//---------
	
	public function URLGetTargetedSentiment($url, $target, $outputMode = self::XML_OUTPUT_MODE, $sentimentParams = null)
	{
		$this->CheckURL($url, $outputMode);
		$this->CheckParamType("AlchemyAPI_TargetedSentimentParams", $sentimentParams);

		if(is_null($sentimentParams))
			$sentimentParams = new AlchemyAPI_TargetedSentimentParams();

		$sentimentParams->setUrl($url);
		$sentimentParams->setTarget($target);
		$sentimentParams->setOutputMode($outputMode);

		return $this->GET("URLGetTargetedSentiment", "url", $sentimentParams);
	}

	public function HTMLGetTargetedSentiment($html, $url, $target, $outputMode = self::XML_OUTPUT_MODE, $sentimentParams = null)
	{
		$this->CheckHTML($html, $url, $outputMode);
		$this->CheckParamType("AlchemyAPI_TargetedSentimentParams", $sentimentParams);

		if(is_null($sentimentParams))
			$sentimentParams = new AlchemyAPI_TargetedSentimentParams();

		$sentimentParams->setUrl($url);
		$sentimentParams->setHtml($html);
		$sentimentParams->setTarget($target);
		$sentimentParams->setOutputMode($outputMode);
 
		return $this->POST("HTMLGetTargetedSentiment", "html", $sentimentParams);
        }

	public function TextGetTargetedSentiment($text, $target, $outputMode = self::XML_OUTPUT_MODE, $sentimentParams = null)
	{
		$this->CheckText($text, $outputMode);
		$this->CheckParamType("AlchemyAPI_TargetedSentimentParams", $sentimentParams);

		if(is_null($sentimentParams))
			$sentimentParams = new AlchemyAPI_TargetedSentimentParams();

		$sentimentParams->setText($text);
		$sentimentParams->setTarget($target);
		$sentimentParams->setOutputMode($outputMode);

		return $this->POST("TextGetTargetedSentiment", "text", $sentimentParams);
	}

	//---------
	
	public function URLGetRelations($url, $outputMode = self::XML_OUTPUT_MODE, $relationParams = null)
	{
		$this->CheckURL($url, $outputMode);
		$this->CheckParamType("AlchemyAPI_RelationParams", $relationParams);
		
		if(is_null($relationParams))
			$relationParams = new AlchemyAPI_RelationParams();
		
		$relationParams->setUrl($url);
		$relationParams->setOutputMode($outputMode);

		return $this->GET("URLGetRelations", "url", $relationParams);
	}

	public function HTMLGetRelations($html, $url, $outputMode = self::XML_OUTPUT_MODE, $relationParams = null)
	{
		$this->CheckHTML($html, $url, $outputMode);
		$this->CheckParamType("AlchemyAPI_RelationParams", $relationParams);
		
		if(is_null($relationParams))
			$relationParams = new AlchemyAPI_RelationParams();
		
		$relationParams->setHtml($html);
		$relationParams->setUrl($url);
		$relationParams->setOutputMode($outputMode);

		return $this->POST("HTMLGetRelations", "html", $relationParams);
	}

	public function TextGetRelations($text, $outputMode = self::XML_OUTPUT_MODE, $relationParams = null)
	{
		$this->CheckText($text, $outputMode);
		$this->CheckParamType("AlchemyAPI_RelationParams", $relationParams);
		
		if(is_null($relationParams))
			$relationParams = new AlchemyAPI_RelationParams();
		
		$relationParams->setText($text);
		$relationParams->setOutputMode($outputMode);

		return $this->POST("TextGetRelations", "text", $relationParams);
	}



	private function CheckOutputMode($outputMode)
	{
		if (strlen($this->_apiKey) < 5)
                {
                        throw new Exception("Load an API key.");
                }

                if (self::XML_OUTPUT_MODE !== $outputMode &&
                    self::JSON_OUTPUT_MODE !== $outputMode)
                {
                        throw new Exception("Illegal Output Mode specified, see *_OUTPUT_MODE constants.");
                }
	}

	private function CheckURL($url, $outputMode)
	{
		$this->CheckOutputMode($outputMode);

		if (strlen($url) < 10)
		{
			throw new Exception("Enter a valid URL to analyze.");
		}
	}

	private function CheckHTML($html, $url, $outputMode)
	{
		$this->CheckURL($url, $outputMode);

		if (strlen($html) < 10)
		{
			throw new Exception("Enter a HTML document to analyze.");
		}
	}

	private function CheckText($text, $outputMode)
	{
		$this->CheckOutputMode($outputMode);

		if (strlen($text) < 5)
		{
			throw new Exception("Enter some text to analyze.");
		}
	}
	
	private function CheckParamType($className, $class)
	{
		if(!is_null($class) && ($className != get_class($class)) )
		{
			throw new Exception("Trying to pass ".get_class($class)." into a function that requires ".$className);
		}
	}


	private function POST()
	{ // callMethod, $callPrefix, $parameterObject
		$callMethod = func_get_arg(0);
		$callPrefix = func_get_arg(1);
		$paramObj = func_get_arg(2);
		
		$outputMode = $paramObj->getOutputMode();
		
		
		$data = "apikey=".$this->_apiKey.$paramObj->getParameterString();
		$paramObj->resetBaseParams();
		$hostPrefix = $this->_hostPrefix;
		$endpoint = "http://$hostPrefix.alchemyapi.com/calls/$callPrefix/$callMethod";

		$handle = curl_init();
                curl_setopt($handle, CURLOPT_URL, $endpoint);
                curl_setopt($handle, CURLOPT_POST, 1);
                curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($handle);
                curl_close($handle);

		if (self::XML_OUTPUT_MODE == $outputMode)
		{
			$doc = simplexml_load_string($response);

                	if (!($doc))
	        	{
    	        		throw new Exception("Error making API call.");
			}

			$status = $doc->xpath("/results/status");
			if ($status[0] != "OK")
			{
				$statusInfo = $doc->xpath("/results/statusInfo");
				throw new Exception("Error making API call: $statusInfo[0]");
			}
		}
		else
		{
			$obj = json_decode($response);

			if (is_null($obj))
			{
				throw new Exception("Error making API call.");
			}
			if ("OK" != $obj->{'status'})
			{
				$statusInfo = $obj->{'statusInfo'};
				throw new Exception("Error making API call: $statusInfo");
			}
		}

		return $response;
	}
	
	private function GET()
	{ // callMethod, $callPrefix, $parameterObject
		$callMethod = func_get_arg(0);
		$callPrefix = func_get_arg(1);
		$paramObj = func_get_arg(2);
		
		$outputMode = $paramObj->getOutputMode();
		
		$data = "apikey=".$this->_apiKey.$paramObj->getParameterString();
		$paramObj->resetBaseParams();

		$hostPrefix = $this->_hostPrefix;
		$uri = "http://$hostPrefix.alchemyapi.com/calls/$callPrefix/$callMethod"."?".$data;

		$handle = curl_init();
                curl_setopt($handle, CURLOPT_URL, $uri);
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($handle);
                curl_close($handle);

		if (self::XML_OUTPUT_MODE == $outputMode)
		{
			$doc = simplexml_load_string($response);

                	if (!($doc))
	        	{
    	        		throw new Exception("Error making API call.");
			}

			$status = $doc->xpath("/results/status");
			if ($status[0] != "OK")
			{
				$statusInfo = $doc->xpath("/results/statusInfo");
				throw new Exception("Error making API call: $statusInfo[0]");
			}
		}
		else
		{
			$obj = json_decode($response);

			if (is_null($obj))
			{
				throw new Exception("Error making API call.");
			}
			if ("OK" != $obj->{'status'})
			{
				$statusInfo = $obj->{'statusInfo'};
				throw new Exception("Error making API call: $statusInfo");
			}
		}

		return $response;
	}
}


?>
