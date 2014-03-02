<?php

namespace VM\VideoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

    public function indexAction($name) {
        return $this->render('VMVideoBundle:Default:index.html.twig', array('name' => $name));
    }

    public function makeLinkAction() {
        $flux = '';
        if ($this->get('request')->getMethod() == 'POST' && $this->get('request')->get('make_link')) {
            $form = $this->get('request')->get('make_link');
            if (array_key_exists('link', $form)) {
                $flux = $this->scrap($form['link']);
            }
        }
        return $this->render('VMVideoBundle:Default:make_link.html.twig', array('flux' => $flux));
    }

    private function scrap($link) {
        define('COOKIE_FILEPATH', './cookie.txt');

        //connexion purevid
        $login = 'http://www.purevid.com/?m=login';
        $pass = strip_tags("@x600p4512@");

        //$_POST variables to pass
        $post_items[] = 'username=bibislayer';
        $post_items[] = 'password=' . $pass;

        //format the $post_items into a string
        $post_string = implode('&', $post_items);

        // exit;
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $login);
        curl_setopt($curl, CURLOPT_COOKIESESSION, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_COOKIEFILE, COOKIE_FILEPATH);
        curl_setopt($curl, CURLOPT_COOKIEJAR, COOKIE_FILEPATH);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        if (curl_exec($curl) === false) {
            echo 'Erreur Curl : ' . curl_error($curl);
            exit;
        } else {
            curl_setopt($curl, CURLOPT_URL, $link);
            curl_setopt($curl, CURLOPT_HEADER, FALSE);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($curl, CURLOPT_COOKIEFILE, COOKIE_FILEPATH);
            curl_setopt($curl, CURLOPT_COOKIEJAR, COOKIE_FILEPATH);

            if (curl_exec($curl) === false) {
                echo 'Erreur Curl : ' . curl_error($curl);
                exit;
            } else {
                $data = curl_exec($curl);
            }
            //$doc = new \DOMDocument();
            //@$doc->loadHTML($data);
            //$video = $doc->getElementById('playerContainer');
        }
        curl_close($curl);
        return $data;

        /* $target_url = $link;
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

          return $html; */
    }

}
