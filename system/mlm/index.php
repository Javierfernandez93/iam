<?php 

require_once __DIR__ . '/vendor/autoload.php';

use Phpml\Classification\NaiveBayes;

$data = [ 1 , 1 , 1 , 0 , 0 , 0 , 0 , 0 , 1];
$data_2 = [ 1 , 1 , 1 , 0 , 0 , 0 , 0 , 1.5 , 0];

$samples = [
	[ 1 , 1 , 1 , 0 , 0 , 0 , 0 , 0 , 1], 
    [ 1 , 1 , 1 , 0 , 0 , 0 , 0 , 1 , 0], 
    [ 1 , 1 , 1 , 0 , 0 , 0 , 0 , 1 , 1]
];

$labels = [0, 1, 2];

$classifier = new NaiveBayes;
$classifier->train($samples, $labels);
getPrediction($classifier,$data);

use Phpml\Classification\SVC;
use Phpml\SupportVectorMachine\Kernel;



$samples = [
	[ 1 , 1 , 1 , 0 , 0 , 0 , 0 , 0 , 0], 
    [ 1 , 1 , 0 , 0 , 1 , 1 , 0 , 0 , 0], 
    [ 1 , 1 , 1 , 0 , 0 , 0 , 1 , 1 , 1]
];
$labels = [0, 1, 2];

$classifier = new SVC(
    Kernel::LINEAR, // $kernel
    1.0,            // $cost
    3,              // $degree
    null,           // $gamma
    0.0,            // $coef0
    0.001,          // $tolerance
    100,            // $cacheSize
    true,           // $shrinking
    true            // $probabilityEstimates, set to true
);

$classifier->train($samples, $labels);

// $result = $classifier->predict($data);
// $s = $classifier->predictProbability($data);
getPrediction($classifier,$data);
getPrediction($classifier,$data_2);

function getPrediction($classifier,$data)
{
	$result = $classifier->predict($data);

	if($result == 0) {
	    echo "Resultado Conjuntivitis alergica";  
	} else if($result == 1) {
	    echo "Resultado Conjuntivitis bacteriana";  
	} else {
	    echo "Resultado Conjuntivitis viral";  
	}

	echo "<br>";  
}


// echo "<pre>";print_r($s);
// return 'b'



// return 'b'

// $samples = [[5, 1, 1], [1, 5, 1], [1, 1, 5]];
// $labels = ['a', 'b', 'c'];

// $classifier = new NaiveBayes();
// $classifier->train($samples, $labels);
// echo $classifier->predict([1, 3, 5]);
