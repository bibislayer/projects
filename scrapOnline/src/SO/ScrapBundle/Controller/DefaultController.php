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
    
    public function searchDpStreamLinkAction() {
        $user_agent = 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)'; # <--- On dit être Firefox.
            $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
            $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
            $header[] = "Cache-Control: max-age=0";
            $header[] = "Connection: keep-alive";
            $header[] = "Keep-Alive: 300";
            $header[] = "Accept-Charset: utf-8";
            $header[] = "Accept-Language: fr"; # Certains sites changent de contenu en fonction de cette ligne, ici le contenu sera français.
            $header[] = "Pragma: "; // Simule un navigateur
            $ch = curl_init();    // initialize curl handle

            curl_setopt($ch, CURLOPT_URL, "http://www.dpstream.net/film-wolverine-le-combat-de-l-immortel-en-streaming-656736.html"); // l'url à visiter
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);              // Fail on errors
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);    // allow redirects
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_PORT, 80);            // Pas indispensable, la pluspart des sites ont le port 80 par défaut
            curl_setopt($ch, CURLOPT_TIMEOUT, 15); //  Si la page n'est pas finie d'ici 15 secondes, tant pis, curl ferme tout. Mais le script peut continuer

            curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
            $html = curl_exec($ch);
            if (!$html) {
                echo "cURL error number:" . curl_errno($ch);
                echo "cURL error:" . curl_error($ch);
                exit;
            }
            curl_close($ch);
            
            echo $html;
            exit;
    }

    public function searchDpStreamAction($i) {
            $user_agent = 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)'; # <--- On dit être Firefox.
            $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
            $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
            $header[] = "Cache-Control: max-age=0";
            $header[] = "Connection: keep-alive";
            $header[] = "Keep-Alive: 300";
            $header[] = "Accept-Charset: utf-8";
            $header[] = "Accept-Language: fr"; # Certains sites changent de contenu en fonction de cette ligne, ici le contenu sera français.
            $header[] = "Pragma: "; // Simule un navigateur
            $ch = curl_init();    // initialize curl handle

            curl_setopt($ch, CURLOPT_URL, "http://www.dpstream.tv/films-en-streaming-page-" . $i . ".html"); // l'url à visiter
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);              // Fail on errors
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);    // allow redirects
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_PORT, 80);            // Pas indispensable, la pluspart des sites ont le port 80 par défaut
            curl_setopt($ch, CURLOPT_TIMEOUT, 15); //  Si la page n'est pas finie d'ici 15 secondes, tant pis, curl ferme tout. Mais le script peut continuer

            curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
            $html = curl_exec($ch);
            if (!$html) {
                echo "cURL error number:" . curl_errno($ch);
                echo "cURL error:" . curl_error($ch);
                exit;
            }
            curl_close($ch); // On ferme curl , SCHLIIING
            $doc = new \DOMDocument();
// A corrupt HTML string
            @$doc->loadHTML($html);
            $body = $doc->getElementsByTagName('ul')->item(1)->nodeValue;
            $links = explode("\n", $body);
            foreach ($links as $q):
                $q = htmlentities($q);
                $q = trim($q);
                if (preg_match("`[[:alnum:]]`", $q)) {
                    $q = preg_replace('/\[VOSTFR\]/', "", $q);
                    $q = preg_replace('/&nbsp;/', "", $q);
                    $q = preg_replace('/  /', "", $q);
                    $q = preg_replace('/YOUWATCH/', "", $q);
                    $q = preg_replace('/PUREVID/', "", $q);
                    $datas = json_decode($this->search($q), TRUE);
                    $objMovie = $this->get('so_movie.controller');
                    $objMovie->setContainer($this->container);
                    $moviesList = $objMovie->saveMovies($datas);
                }
            endforeach;
        if($i < 7625)
            return 1;
        else
            return 0;
        //return $this->render('SOScrapBundle:Default:search_dp_stream.html.twig', array());
    }

    public function searchAlloCineAction() {
        $processed = 0;
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $arrFilms = explode(';', $request->request->get('name'));
            $processed = 1;
        }
        if ($request->get('name')) {
            $processed = 1;
            $q = $request->get('name');
            $q = str_replace(" ", "+", $q);
            $arrFilms[] = $q;
        }
        if ($processed == 1) {
            foreach ($arrFilms as $q):
                echo $q;
                $datas = json_decode($this->search($q), TRUE);
                $objMovie = $this->get('so_movie.controller');
                $objMovie->setContainer($this->container);
                $moviesList = $objMovie->saveMovies($datas);
                echo '<pre>';
                print_r($moviesList);
                echo '<hr />';
            endforeach;
        }

        return $this->render('SOScrapBundle:Default:search_allo_cine.html.twig', array());
    }

    private function search($query) {
        // build the params
        $params = array(
            'partner' => $this->_partner_key,
            'q' => $query,
            'format' => 'json',
            'count' => '50',
            'filter' => 'movie'
        );

        // do the request
        $response = $this->_do_request('search', $params);
        return $response;
    }
    
    private function _get($id) {
        // build the params
        $params = array(
            'partner' => $this->_partner_key,
            'code' => $id,
            'profile' => 'large',
            'filter' => 'movie',
            'striptags' => 'synopsis,synopsisshort',
            'format' => 'json',
        );

        // do the request
        $response = $this->_do_request('movie', $params);

        return $response;
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
        return $arrayLink;
        /*return $this->container->get('templating')->renderResponse('SOScrapBundle:Default:scrap_google.html.twig', array(
                    'arrayLink' => $arrayLink,
        ));*/
        //}
    }

    public function searchLinksAction($q, $code) {
        return $this->scrapGoogle($q, $code);
        exit;
    }

}
