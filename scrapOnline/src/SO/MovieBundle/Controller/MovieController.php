<?php

namespace SO\MovieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SO\MovieBundle\Entity\Movie;
use SO\MovieBundle\Entity\Genre;

class MovieController extends Controller {

    public function indexAction($name) {
        return $this->render('SOMovieBundle:Default:index.html.twig', array('name' => $name));
    }

    public function showAction($slug) {
        $em = $this->getDoctrine()->getManager();
        $movie = $em->getRepository('SOMovieBundle:Movie')->findOneBy(array('slug' => $slug));
        return $this->render('SOMovieBundle:Default:show.html.twig', array('movie' => $movie));
    }
    
     public function videoAction($slug, $type, $movieCode) {
        $em = $this->getDoctrine()->getEntityManager();
        $movie = $em->getRepository('SOMovieBundle:Movie')->findOneBy(array('slug' => $slug));
        $q = "";
        $code = $movie->getCode();
        $links = $this->get('so_scrap.controller')->searchLinksAction($q, $code);
    
        return $this->render('SOMovieBundle:Default:video_movie.html.twig', array('movie' => $movie, 'links' => $links, 
                                                                                  'type' => $type, 'movieCode' => $movieCode));
    }

    public function listAction($max = 3, $request = null) {
        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT m FROM SOMovieBundle:Movie m";
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        if(!$request)
            $request = $this->getRequest();
        $pagination = $paginator->paginate($query, $request->query->get('page', 1), 9);

        return $this->render('SOMovieBundle:Default:list_movie.html.twig', array('pagination' => $pagination));
    }

    public function sidebarAction($max = 5, $request) {
        $em = $this->getDoctrine()->getManager();
        $movie = $em->getRepository('SOMovieBundle:Movie')->findBy(array(), array('publicRating' => 'DESC'), $max);

        return $this->render('SOMovieBundle:Default:sidebar_movie.html.twig', array('movies' => $movie));
    }

    private $_api_url = 'http://api.allocine.fr/rest/v3';
    private $_partner_key = '100043982026';
    private $_secret_key = '29d185d98c984a359e6e6f26a0474269';
    private $_user_agent = 'Dalvik/1.6.0 (Linux; U; Android 4.2.2; Nexus 4 Build/JDQ39E)';

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

    private function colorPrompt($string, $color) {
        $color_end = "\033[0m";
        switch ($color):
            case 'green':
                return "\033[0;32m" . $string . $color_end;
                break;
            case 'blue':
                return "\033[0;34m" . $string . $color_end;
                break;
            case 'purple':
                return "\033[0;35m" . $string . $color_end;
        endswitch;
    }

    public function saveMovies($datas, $i = 1) {
        $movie = array();
        $movieList = array();
        $em = $this->getDoctrine()->getEntityManager();

        if ($datas):
            foreach ($datas as $data):
                if (array_key_exists('movie', $data)) {
                    echo $this->colorPrompt(count($data['movie']) . " movies founded.", "blue") . "\n";
                    foreach ($data['movie'] as $movie):
                        if (is_array($movie)) {
                            echo $this->colorPrompt($movie['originalTitle'], "green");
                            $movies = json_decode($this->_get($movie['code']), TRUE);
                            //print_r($movies);exit;
                            $movies = $movies['movie'];
                            if (is_array($movies)) {
                                $moviesList[] = $movies;
                                $objMovie = $em->getRepository('SOMovieBundle:Movie')->findOneBy(array('code' => $movie['code']));
                                if (!$objMovie)
                                    $objMovie = new Movie();
                                $objMovie->setCode($movies['code']);
                                $objMovie->setOriginalTitle($movies['originalTitle']);
                                $objMovie->setTitle($movies['originalTitle']);
                                if (array_key_exists('title', $movies))
                                    $objMovie->setTitle($movies['title']);
                                if (array_key_exists('statistics', $movies)) {
                                    (array_key_exists('pressRating', $movies['statistics'])) ? $objMovie->setPressRating($movies['statistics']['pressRating']) : '';
                                    (array_key_exists('userRating', $movies['statistics'])) ? $objMovie->setPublicRating($movies['statistics']['userRating']) : '';
                                }
                                
                                if (array_key_exists('media', $movies)) {
                                    //print_r($movies); exit;
                                    foreach($movies['media'] as $media):
                                        if($media['class'] == 'video')
                                            (array_key_exists('trailerEmbed', $media)) ? $objMovie->setTrailerEmbed($media['trailerEmbed']) : '';
                                    endforeach;
                                }
                                
                                if (array_key_exists('poster', $movies)) {
                                    (array_key_exists('href', $movies['poster'])) ? $objMovie->setPoster($movies['poster']['href']) : '';
                                }

                                (array_key_exists('productionYear', $movies)) ? $objMovie->setProductionYear($movies['productionYear']) : '';
                                (array_key_exists('keywords', $movies)) ? $objMovie->setKeywords($movies['keywords']) : '';
                                (array_key_exists('synopsis', $movies)) ? $objMovie->setSynopsis($movies['synopsis']) : '';
                                (array_key_exists('synopsisShort', $movies)) ? $objMovie->setSynopsisShort($movies['synopsisShort']) : '';
                                (array_key_exists('runtime', $movies)) ? $objMovie->setRuntime($movies['runtime']) : '';

                                echo $this->colorPrompt(" saved", "purple");
                                echo "\n";
                                //echo '<pre>'; print_r($movies);exit;
                                if (array_key_exists('genre', $movies)) {
                                    $arr = array();
                                    foreach ($objMovie->getGenre() as $result) {
                                        $arr[] = $result->getCode();
                                    }
                                    foreach ($movies['genre'] as $g) {
                                        $objGenre = $em->getRepository('SOMovieBundle:Genre')->findOneBy(array('code' => $g['code']));
                                        if (!$objGenre) {
                                            echo $g['code'];
                                            $objGenre = new Genre();
                                            $objGenre->setCode($g['code']);
                                            $objGenre->setName($g['$']);
                                            $em->persist($objGenre);
                                            $em->flush();
                                        }
                                        if (!in_array($g['code'], $arr)) {
                                            echo "\n GENRE SAVED \n";
                                            $objMovie->addGenre($objGenre);
                                        }
                                    }
                                }
                                $em->persist($objMovie);
                                $em->flush();
                            }
                        }
                    endforeach;
                }
            endforeach;
        endif;
        return $movieList;
    }

}
