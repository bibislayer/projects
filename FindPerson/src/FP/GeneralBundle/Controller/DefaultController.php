<?php

namespace FP\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function changeLanguageAction($locale){

        $this->getRequest()->getSession()->set('_locale', $locale);
        $this->getRequest()->setLocale($locale);

        if($referer = $this->getRequest()->headers->get('referer')){
            return $this->redirect($referer);
        }else{
            return $this->redirect($this->generateUrl('fo_homepage'));
        }

    }
}
