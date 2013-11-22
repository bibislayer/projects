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

class ProcessController extends Controller
{  
     protected $container;
    
    function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    
//For sending Email
    public function sendMail($params=array()) {
        if((array_key_exists('template', $params) || array_key_exists('mailerObj', $params)) && array_key_exists('to', $params) && array_key_exists('from', $params) && (array_key_exists('temp_params', $params) && is_array($params['temp_params']))){
            
            if(array_key_exists('mailerObj', $params) && $params['mailerObj']->getId()){
                $mailer = $params['mailerObj'];
            }else{
                $mailer = $this->container->get('mailer_repository')->getElements(array('by_template' => $params['template'], 'action' => 'one'));
            }
            
            $extension = new FPExtension();
            if($mailer){
                $content = $extension->evaluateString(new \Twig_Environment(), $params['temp_params'] , $mailer->getContent());
                $message = \Swift_Message::newInstance()
                        ->setSubject($mailer->getSubject())
                        ->setFrom($params['from'])
                        ->setTo($params['to'])
                        ->setContentType('text/html')
                        ->setBody($content);
                
               $this->container->get('mailer')->send($message); 
            }             
        }else{
            echo 'you have passed wrong parameters';
            exit;
        }
     }
     
     
}

?>
