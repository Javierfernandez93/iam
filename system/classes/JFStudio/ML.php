<?php
/**
*
*/

namespace JFStudio;

use DummieTrading\ReplyPerCatalogTagIntent;
use DummieTrading\CatalogTagIntent;
use DummieTrading\UserTradingAccount;
use DummieTrading\Parser;
use DummieTrading\Intent;
use DummieTrading\UserData;
use DummieTrading\UserTemp;

use BlockChain\Wallet;

require_once TO_ROOT . '/vendor/autoload.php';

class ML
{
	private $values = [];
	private $vocabulary = [];

	public function hasSamples($samples = null) {
		return true;
		return sizeof($samples) ? true : false;
	}

	public function getSamples(string $sentence = null) : array {
		$words = explode(" ", trim($sentence));
		$values = [];
		$result = [];

		foreach ($words as $token) {
			$key = $this->searchToken($token);

			if($key !== null)
			{
				$values[$key] = $this->getValues()[$key];
			}
		}

		for($i = 0; $i <= sizeof($this->getValues()); $i++)
		{
			$result[$i] = isset($values[$i]) ? $values[$i] : 0;
		}

		return $result;
	}

	public function getTargetsByBD(array $array = null) 
	{
		return array_column($array, "words");
	}

	public function getSamplesByBD(array $array = null) 
	{
		return array_column($array, "tag");
	}

	public function searchToken(string $_word = null) 
	{
		$_key = null;

		foreach ($this->getVocabulary() as $key => $word) 
		{
			if(strtolower($word) === strtolower($_word))
			{
				$_key = $key;
			}
		}

		return $_key;
	}

	public function getVocabulary() : array {
		return $this->vocabulary;
	}

	public function setVocabulary(array $vocabulary) : void {
		$this->vocabulary = $vocabulary;
	}

	public function getValues() : array {
		return $this->values;
	}

	public function setValues(array $values = null) : void
	{
		$this->values = $values;
	}

	public function convertValues(array $samples) : array
	{
		$r = [];

		foreach ($samples as $p) {
		    foreach ($p as $key => $value) 
		    {
		        if(empty($r[$key]) == true)
		        {
		            $r[$key] = $value;
		        }  else{
		            $r[$key] += $value;
		        }
		    }
		}

		return $r;
	}

	public static function hasNumbers(string $string = null) : bool
	{
		if(!str_contains($string,"token="))
		{
			return preg_match('~[0-9]+~', $string);
		}

		return false;
	}

	public static function getResponseIATag(string $query = null,int $attach_catalog_tag_intent_id = null)
	{
		if(self::hasNumbers($query)) {
			$query .= " is_a_number";
		}

		$intents = Intent::getIntents($query,$attach_catalog_tag_intent_id);
		// $intents = Intent::getIntents($query,8);

		if ($tag = self::predict($intents, $query)) {
			return $tag['tag'];
		}

		return $query;
	}

	public static function isSingleData(array $data = null)
	{
		return sizeOf(array_unique(array_column($data, "tag"))) <= 1;
	}

	public static function predict(array $data = null, string $sentence = null)
	{
		if (self::isSingleData($data) === true) {		
			return [
				"tag" => $data[0]['tag'],
				"probability" => 100
			];
		} else {
			$data = array_values($data);

			$classifier = new \Phpml\Classification\SVC(
				\Phpml\SupportVectorMachine\Kernel::LINEAR, // $kernel
				1.0,            // $cost
				3,              // $degree
				null,           // $gamma
				0.0,            // $coef0
				0.001,          // $tolerance
				100,            // $cacheSiz
				true,           // $shrinking
				true            // $probabilityEstimates, set to true
			);

			$ML = new ML;

			$samples = $ML->getTargetsByBD($data);
			$targets = $ML->getSamplesByBD($data);

			$vectorizer = new \Phpml\FeatureExtraction\TokenCountVectorizer(new \Phpml\Tokenization\WordTokenizer);
			$tfIdfTransformer = new \Phpml\FeatureExtraction\TfIdfTransformer;

			$vectorizer->fit($samples);
			$vectorizer->transform($samples);
			
			$tfIdfTransformer->fit($samples);
			$tfIdfTransformer->transform($samples);
			
			$dataset = new \Phpml\Dataset\ArrayDataset($samples, $targets);

			$ML->setValues($ML->convertValues($dataset->getSamples()));
			$ML->setVocabulary($vectorizer->getVocabulary());

			$classifier->train($samples, $targets);
			
			if ($samples = $ML->getSamples($sentence)) 
			{
				if ($tag = $classifier->predict($samples)) 
				{
					if ($probabilities = $classifier->predictProbability($samples)) 
					{
						arsort($probabilities);

						$keys = array_keys($probabilities);
						
						$probabilityTag = isset($keys[0]) ? $keys[0] : false;
						$probability = $probabilities[$probabilityTag];

						if ($probabilityTag) 
						{
							$probability *= 100;

							if ($probability > 10) {
								return [
									"tag" => $tag,
									"probabilityTag" => $probabilityTag,
									"probabilities" => $probabilities
								];
							}
						}
					}
				}
			}
		}

		return ["tag"=>"unknown","probabilities"=>100];
	}

	public static function hasExtrectedVars(string $message = null): string
	{
		return '';
	}

	public static function getMLReplay(string $tag = null, $catalog_tag_intent_id = null): string
	{
		if (isset($tag, $catalog_tag_intent_id) === true) {
			return ReplyPerCatalogTagIntent::getReplyRandom($catalog_tag_intent_id);
		}

		return ReplyPerCatalogTagIntent::getDefaultReply();
	}

	public static function getResponseIA(array $data = null) : array
	{
		$CatalogTagIntent = new CatalogTagIntent;

		if($data['catalog_tag_intent'] = $CatalogTagIntent->getCatalogTagIntentIdByTag($data['tag']))
		{
			if($data['catalog_tag_intent']['attach_catalog_tag_intent_id'])
			{
				UserTemp::setVar($data['user_login_id'],'catalog_tag_intent_id',$data['catalog_tag_intent']['attach_catalog_tag_intent_id']);
			}

			if($CatalogTagIntent->hasResponse($data['catalog_tag_intent']['catalog_tag_intent_id']))
			{
				$data['response'] = self::getMLReplay($data['tag'],$data['catalog_tag_intent']['catalog_tag_intent_id']);
			}

			$data['tag'] = $data['tag'];

			$data['extracted_vars'] = ExtractVariables::extract([
				'message' => $data['message'],
				'tag' => $data['tag']
			]);

			$data['response'] = strip_tags(Parser::doParser($data['response'],[
				...[
					'names' => trim((new UserData)->getNames($data['user_login_id'])),
					'login' => (new UserTradingAccount)->getTradingAccountFollowingLogin(1),
					'walletBalance' => number_format(Wallet::getCurrentBalance($data['user_login_id']),2),
					// 'referral_link' => Connection::getMainPath().$UserLogin->getReferralLanding()
				],
				...$data['extracted_vars']
			]));
		}

		return $data;
	}
}