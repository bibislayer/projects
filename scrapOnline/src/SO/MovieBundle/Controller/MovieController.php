<?php

namespace SO\MovieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SO\MovieBundle\Entity\Movie;

class MovieController extends Controller {

    public function indexAction($name) {
        return $this->render('SOMovieBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function showAction($slug) {
        $em = $this->getDoctrine()->getEntityManager();
        $movie = $em->getRepository('SOMovieBundle:Movie')->findOneBy(array('slug' => $slug));
        return $this->render('SOMovieBundle:Default:show.html.twig', array('movie' => $movie));
    }
    
      public function videoAction($slug) {
        //$em = $this->getDoctrine()->getEntityManager();
        //$movie = $em->getRepository('SOMovieBundle:Movie')->findOneBy(array('code' => $code));
        return $this->render('SOMovieBundle:Default:video_movie.html.twig', array());
    }
    
    private function colorPrompt($string, $color){
        $color_end = "\033[0m";
        switch($color):
            case 'green':
                return "\033[0;32m".$string.$color_end;
            break;
            case 'blue':
                return "\033[0;34m".$string.$color_end;
            break;
            case 'purple':
                return "\033[0;35m".$string.$color_end;
        endswitch;
    }

    public function saveMovies($datas, $i = 1) {
        $movie = array();
        $movieList = array();
        $em = $this->getDoctrine()->getEntityManager();
        
        if ($datas):
            foreach ($datas as $data):
                if (array_key_exists('movie', $data)) {
                    echo $this->colorPrompt(count($data['movie']) . " movies founded.", "blue"). "\n";
                    foreach ($data['movie'] as $movie):
                        if (is_array($movie)) {
                            echo $this->colorPrompt($movie['originalTitle'], "green");
                            $movieList[] = $movie;
                            $objMovie = $em->getRepository('SOMovieBundle:Movie')->findOneBy(array('code' => $movie['code']));
                            if (!$objMovie) {
                                $objMovie = new Movie();
                                $objMovie->setCode($movie['code']);
                                $objMovie->setOriginalTitle($movie['originalTitle']);
                                $objMovie->setTitle($movie['originalTitle']);
                                if (array_key_exists('title', $movie))
                                    $objMovie->setTitle($movie['title']);
                                if (array_key_exists('statistics', $movie)) {
                                    (array_key_exists('pressRating', $movie['statistics'])) ? $objMovie->setPressRating($movie['statistics']['pressRating']) : '';
                                    (array_key_exists('userRating', $movie['statistics'])) ? $objMovie->setPublicRating($movie['statistics']['userRating']) : '';
                                }
                                if (array_key_exists('poster', $movie)) {
                                    (array_key_exists('href', $movie['poster'])) ? $objMovie->setPoster($movie['poster']['href']) : '';
                                }

                                (array_key_exists('productionYear', $movie)) ? $objMovie->setProductionYear($movie['productionYear']) : '';
                                (array_key_exists('keywords', $movie)) ? $objMovie->setKeywords($movie['keywords']) : '';
                                (array_key_exists('synopsis', $movie)) ? $objMovie->setSynopsis($movie['synopsis']) : '';
                                (array_key_exists('synopsisShort', $movie)) ? $objMovie->setSynopsisShort($movie['synopsisShort']) : '';

                                $em->persist($objMovie);
                                $em->flush();
                                echo $this->colorPrompt(" saved", "purple");
                            }
                            echo "\n";
                            if (array_key_exists('genre', $movie)) {
                                $arr = array();
                                foreach ($objMovie->getGenres() as $result) {
                                    $arr[] = $result->getCode();
                                }
                                foreach ($movie['genre'] as $g) {
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
                                        $objMovie->addGenre($objGenre);
                                    }
                                }
                            }
                        }
                    endforeach;
                }
            endforeach;
        endif;
        return $movieList;
    }

}
