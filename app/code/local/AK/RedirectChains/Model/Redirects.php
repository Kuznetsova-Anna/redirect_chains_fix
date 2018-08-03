<?php


class AK_RedirectChains_Model_Redirects extends Mage_Core_Model_Abstract
{
    /**
     * @param $url
     * @param $store
     * @param int $l
     * @param array $urlsArray
     * @return mixed|void
     */
    public function updateRedirect($url, $store, $l = 1, $urlsArray = [])
    {
        /* push urls into the list of all chain urls */
        $urlsArray[] = $url;

        /* prevent infinite redirects loop */
        if ($l > 10) {
            $urlsString = implode(" ", $urlsArray);
            Mage::log('Redirects infinite loop (more than 10 redirect loops):');
            Mage::log($urlsString);
            /* show redirect loop urls in admin message */
            return $urlsArray[0];
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        $out = curl_exec($ch);
        $out = str_replace("\r", "", $out);

        /* only look at the headers */
        $headers_end = strpos($out, "\n\n");
        if( $headers_end !== false ) {
            $out = substr($out, 0, $headers_end);
        }
        $headers = explode("\n", $out);

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        /* if redirect exists */
        if ($code == 301) {
            foreach($headers as $header) {
                if( substr($header, 0, 10) == "Location: " ) {
                    $target = substr($header, 10);

                    if ($target != $url . '/') {
                        $target = $this->getBaseUrlNoTrailingSlash($store) . $target;
                    }
                    $redirectUrl = $this->updateRedirect($target, $store, $l + 1, $urlsArray);
                    /* if loop redirect */
                    if ($redirectUrl) {
                        return $redirectUrl;
                    }
                    continue;
                }
            }
        } else {
            /*
            ignore redirect chains like
            http://www.sample.com/clearance/accessories/ ->
            -> http://www.sample.com/catalog/category/view/id/2428 ->
            -> http://www.sample.com/catalog/category/view/id/2428/
            */
            if ($l == 3 && (($urlsArray[1] . '/') == $urlsArray[2])) {
                return;
            /*
            ignore 1-level redirects
            (http://www.sample.com/catalog/category/view/id/2428 ->
            -> http://www.sample.com/catalog/category/view/id/2428/)
            */
            } elseif ($l == 2) {
                return;
            /* ignore no-redirects */
            } elseif ($l == 1) {
                return;
            }

            $urlIdentifier = str_replace($this->getBaseUrlNoTrailingSlash($store) . '/', '', $urlsArray[0]);
            $urlIdentifier = rtrim($urlIdentifier, '/');

            $targetPath = str_replace($this->getBaseUrlNoTrailingSlash($store) . '/', '', $urlsArray[$l-1]);
            $targetPath = rtrim($targetPath, '/');

            /* set a new target path (the last url in redirect chain) */
            Mage::getModel('enterprise_urlrewrite/redirect')->loadByRequestPath($urlIdentifier, $store)
                ->setTargetPath($targetPath)->save();
        }
    }

    /**
     * @param $store
     * @return string
     */
    protected function getBaseUrlNoTrailingSlash($store)
    {
        $baseUrl = Mage::app()->getStore($store)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        $baseUrl = rtrim($baseUrl, '/');
        return $baseUrl;
    }

    /**
     * @return array
     */
    public function getStores()
    {
        $storesArray = [];

        $allStores = Mage::app()->getStores();
        foreach ($allStores as $storeId => $store)
        {
            $storeName = Mage::app()->getStore($storeId)->getName();
            $storesArray[$storeId] = $storeName;
        }

        return $storesArray;
    }
}