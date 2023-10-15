<?php

class Verification {

    public static function generateVerificatorDigit($text = false,$verification_type)
    {
        if($text)
        {
            switch ($verification_type) {
                case 'b10':
                    $text = self::b10($text);
                    break;

                default:
                    $text = self::b10($text);
                    break;
            }

            return $text;
        }

        return false;
    }

    public static function b10($text)
    {
        $start = 2;
        $total = 0;
        $array_aux = array();
        $aux_text = $text;
        $array = str_split($aux_text);

        for($i=strlen($aux_text)-1; $i>=0; $i--)
        {
            // echo $array[$i]."*".$start."=".$array[$i] * $start."<br>";
            $array_aux[] = $array[$i] * $start;

            if ($start==2)
                $start = 1;
            else
                $start = 2;
        }

        foreach ($array_aux as $key => $number) {
            if($number>9)
            {
                $number_aux = 0;
                $numbers = str_split($number);

                foreach ($numbers as $_key => $number)
                    $number_aux += $number;

                $array_aux[$key] = $number_aux;
            }
        }


        foreach ($array_aux as $key => $number)
            $total += $number;

        $total %= 10;

        if($total != 0)
            $total = 10 - $total;

        return $text.$total;
    }
}
?>