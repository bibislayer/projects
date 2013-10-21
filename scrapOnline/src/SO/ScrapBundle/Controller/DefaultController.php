<?php

namespace SO\ScrapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

    //Defined for allocine api
    private $_api_url = 'http://api.allocine.fr/rest/v3';
    private $_partner_key = '100043982026';
    private $_secret_key = '29d185d98c984a359e6e6f26a0474269';
    private $_user_agent = 'Dalvik/1.6.0 (Linux; U; Android 4.2.2; Nexus 4 Build/JDQ39E)';

    public function indexAction($name) {
        return $this->render('SOScrapBundle:Default:index.html.twig', array('name' => $name));
    }

    private function _do_request($method, $params) {
        // build the URL
        $query_url = $this->_api_url . '/' . $method;

        // new algo to build the query
        $sed = date('Ymd');
        $sig = urlencode(base64_encode(sha1($this->_secret_key . http_build_query($params) . '&sed=' . $sed, true)));
        $query_url .= '?' . http_build_query($params) . '&sed=' . $sed . '&sig=' . $sig;

        // do the request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $query_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->_user_agent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    private function scrapGoogle($q, $code) {
        //for ($nbPage = 0; $nbPage <= 3; $nbPage++) {
        $nbPage = 0;
        $start = $nbPage * 10;
        $a = $q . "+'http://www.mixturecloud.com/media'";
        $target_url = 'http://www.google.fr/search?q=' . $a . '&start=' . $start;
        $userAgent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_URL, $target_url);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $html = curl_exec($ch);
        if (!$html) {
            echo "cURL error number:" . curl_errno($ch);
            echo "cURL error:" . curl_error($ch);
            exit;
        }

        preg_match_all('#http\:\/\/www\.mixturecloud\.com\/media(.*?){9,9} #', $html, $results);
        $i = 0;
        $arrayLink = array();
        foreach ($results[1] as $result):
            if (strlen($result) > 9 && strlen($result) < 15) {
                $i++;
                $result = str_replace('.', '', $result);
                $result = str_replace('</b>', '', $result);
                $arrayLink[$i]['codeUrl'] = $result;
                $arrayLink[$i]['code'] = $code;
                $i++;
            }
        endforeach;
        return $this->container->get('templating')->renderResponse('SOScrapBundle:Default:scrap_google.html.twig', array(
                    'arrayLink' => $arrayLink,
        ));
        //}
    }

    public function searchLinksAction() {
        $request = $this->getRequest();
        if ($request->get('name')) {
            $q = $request->get('name');
            $q = str_replace(" ", "+", $q);
            $code = ($request->get('code')) ? $request->get('code') : '';
            return $this->scrapGoogle($q, $code);
        }
        exit;
    }

}
