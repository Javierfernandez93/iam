<?php

namespace DummieTrading;

class WebFlow {
    public static function getProperty(array $properties = null, string $property_name = null) 
    {
        $list = array_column($properties, "key", 'value');

        if(isset($list))
        {
            if(in_array($property_name, $list))
            {
                $flipped = array_flip($list);

                return $flipped[$property_name];
            }
        }

        return false;
    }
}