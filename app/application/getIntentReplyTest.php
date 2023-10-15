<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

if($data['message'] ?? false)
{	
    $intents = DummieTrading\Intent::getIntents($data['message']);
    
    if($tag = predict($intents,$data['message']))
    {
		$data['tag'] = $tag;
    } 
} else {
    $data['s'] = 0;
    $data['r'] = "NOT_MESSAGE";
}

function predict(array $data = null, string $sentence = null) 
{
    require_once TO_ROOT . '/vendor/autoload.php';

	if(isSingleData($data) === true)
	{
		return [
			"tag" => $data[0]['tag'],
			"probability" => 100
		];
	} else {
		$data = array_values($data);

		$classifier = new Phpml\Classification\SVC(
		    Phpml\SupportVectorMachine\Kernel::LINEAR, // $kernel
		    1.0,            // $cost
		    3,              // $degree
		    null,           // $gamma
		    0.0,            // $coef0
		    0.001,          // $tolerance
		    100,            // $cacheSize
		    true,           // $shrinking
		    true            // $probabilityEstimates, set to true
		);

		$ML = new JFStudio\ML;

		$samples = $ML->getTargetsByBD($data);
		$targets = $ML->getSamplesByBD($data);

		$vectorizer = new Phpml\FeatureExtraction\TokenCountVectorizer(new Phpml\Tokenization\WordTokenizer);
		$tfIdfTransformer = new Phpml\FeatureExtraction\TfIdfTransformer;

		$vectorizer->fit($samples);
		$vectorizer->transform($samples);

		$tfIdfTransformer->fit($samples);
		$tfIdfTransformer->transform($samples);

		$dataset = new Phpml\Dataset\ArrayDataset($samples, $targets);

		$ML->setValues($ML->convertValues($dataset->getSamples()));
		$ML->setVocabulary($vectorizer->getVocabulary());
    
		$classifier->train($samples, $targets);

		if($samples = $ML->getSamples($sentence))
		{
			if($tag = $classifier->predict($samples))
			{
				if($probabilities = $classifier->predictProbability($samples))
				{
					if($probability = $probabilities[$tag])
					{
						$probability *= 100;
						
						if($probability > 10)
						{
							return [
								"tag" => $tag,
								"probabilities" => $probabilities
							];
						}
					}
				}
			}
		}
	}

	return false;
}

function getMLReplay(string $tag = null,$catalog_tag_intent_id = null) : string
{
	if(isset($tag,$catalog_tag_intent_id) === true)
	{
		return DummieTrading\ReplyPerCatalogTagIntent::getReplyRandom($catalog_tag_intent_id);
	}

	return DummieTrading\ReplyPerCatalogTagIntent::getDefaultReply();
}

function isSingleData(array $data = null) 
{
	return sizeOf(array_unique(array_column($data,"tag"))) <= 2;
} 

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 