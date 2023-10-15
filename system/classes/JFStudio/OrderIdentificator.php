<?php

namespace JFStudio;

use JFStudio\ExtractVariables;

class OrderIdentificator
{
	public static function isSell(string $side = null) : bool
    {
        return in_array(strtolower($side),['sell','vender','venta']);
    }

    public static function isBuy(string $side = null) : bool
    {
        return in_array(strtolower($side),['buy','compra','comprar']);
    }

	public static function getVarIndex(array $variables = null,string $variable = null) 
    {
        return array_search($variable,$variables);
    }

	public static function getValueIndex(array $values = null,string $value = null) 
    {
        return array_search($value,$values);
    }
	
    public static function getVarsIndexValue(array $data = null) : array
    {
        $vars = [];

        foreach($data['variables'] as $key => $_variable)
        {
            if($data['variable'] == strtolower($_variable))
            {
                $vars[] = $data['values'][$key];
            }
        }

        return $vars;
    }

    public static function getVarIndexValue(array $data = null) : string|bool|null
    {
        $index = self::getVarIndex($data['variables'],$data['variable']);

        if($index !== false)
        {
            return $data['values'][$index];
        }

        return false;
    }

	public static function identify(string $message = null) 
    {
        $keys = ExtractVariables::_extract($message);
        $variables = array_column($keys,"variable");
        $values = array_column($keys,"value");
    
        $sideValue = self::getVarIndexValue([
            "variables" => $variables,
            "values" => $values,
            "variable" => "side"
        ]);

        $varsLimits = self::getVarsIndexValue([
            "variables" => $variables,
            "values" => $values,
            "variable" => "limit"
        ]);

        if(!empty($varsLimits))
        {
            $min = min($varsLimits);
            $max = max($varsLimits);

            if(self::isSell($sideValue))
            {
                /* sl mayor y tp menor */
                
                $tpIndex = self::getValueIndex($values,$min);
                $slIndex = self::getValueIndex($values,$max);
    
    
                $keys[$slIndex]['variable'] = 'stopLoss';
                $keys[$tpIndex]['variable'] = 'takeProfit';
            } else if(self::isBuy($sideValue)) {
                /* tp mayor y sl menor */
    
                $slIndex = self::getValueIndex($values,$min);
                $tpIndex = self::getValueIndex($values,$max);
                
                $keys[$slIndex]['variable'] = 'stopLoss';
                $keys[$tpIndex]['variable'] = 'takeProfit';
            }
        }


        return $keys;
	}

    public static function asSingleArray(array $variables = null) : array
	{
		$singleArray = [];
		
		foreach($variables as $variable)
		{
			$singleArray[$variable['variable']] = $variable['value'];
		}

		return $singleArray;
	}
}