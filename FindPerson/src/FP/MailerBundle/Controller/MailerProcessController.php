<?php

namespace FP\MailerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use FP\MailerBundle\Form\MailerType;
use FP\MailerBundle\Entity\Mailer;
use FP\MailerBundle\Entity\MailerCategory;
use FP\MailerBundle\Form\MailerCategoryType;
use FP\StandardBundle\Twig\FPExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MailerProcessController extends Controller
{  
     
//For sending Email
    public function testEmailAction($id) {        
        $request= $this->getRequest();
	$user = $this->get('security.context')->getToken()->getUser();
        $data=array();
        if ($user) {            
             $mailer = $this->container->get('mailer_repository')->getElements(array('by_id' => $id, 'action' => 'one'));
                if($mailer){
                    if($request->getMethod() == 'POST' && $request->get('email')){
                            $params = array();
                            $parameters = array(
                                    'to' => $request->get('email'),
                                    'from'=>array('contact@forma-search.com'=>'FP'),
                                    'temp_params'=>$request->get('temp_params')?$request->get('temp_params'):array(),
                                    'mailerObj'=>$mailer
                            );
                            $this->get('my_mailer')->sendMail($parameters);
                            return $this->redirect($this->generateUrl('bo_mailers'));
                    }					
                    preg_match_all('/\$%(.*?)\$%/',$mailer->getContent(),$match);
                    preg_match_all('/\$#(.*?)\$#/',$mailer->getContent(),$match_two);
                    preg_match_all('/\$%(.*?)\$%/',$mailer->getSubject(),$smatch);
                    preg_match_all('/\$#(.*?)\$#/',$mailer->getSubject(),$smatch_two);
                    $data = array_unique(array_merge($match[1] , $match_two[1],$smatch[1] , $smatch_two[1])) ;
                }
            return $this->render('FPMailerBundle:Back:testEmail.html.twig',array('data'=>$data));
        }else {
            
        }
     }
}

?>
