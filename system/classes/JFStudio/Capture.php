<?php

namespace JFStudio;

use HCStudio\Util;

class Capture
{
    /**
     * Capture web screenshot using google api.
     *
     * @param (string) $url Valid url
     *
     * @return blob
     */
    public function snap($url)
    {
        //Url value should not empty and validate url
        if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
            $curl_init = curl_init("https://www.googleapis.com/pagespeedonline/v2/runPagespeed?url={$url}&screenshot=true");
            curl_setopt($curl_init, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl_init);
            curl_close($curl_init);
            //call Google PageSpeed Insights API
            //decode json data
            $googlepsdata = json_decode($response, true);
            //screenshot data
            $snap = $googlepsdata['screenshot']['data'];
            $snap = str_replace(['_', '-'], ['/', '+'], $snap);
            return $snap;
        } else {
            return false;
        }
    }
}