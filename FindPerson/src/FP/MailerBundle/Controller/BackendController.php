<?php

namespace FP\MailerBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FP\MailerBundle\Filter\MailerFilterType;

class BackendController extends Controller
{
    //to show mailer category by id
    public function showCategoryAction($id) {
        $cMailer = $this->get('mailer_category_repository')->getElements(array('by_id' => $id, 'action' => 'one'));

        if($cMailer){
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem("Catégories de mail", $this->get("router")->generate("bo_mailer_categories"));
            $breadcrumbs->addItem($cMailer->getName());
            return $this->render('FPMailerBundle:Back:showCategory.html.twig', array('element' => $cMailer));
        }else{

        }
    }
    
    //to show mailer template
    public function showMailerAction($id) {

        $mailer = $this->get('mailer_repository')->getElements(array('by_id' => $id, 'action' => 'one'));

        if($mailer){
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem("Templates de mail", $this->get("router")->generate("bo_mailers"));
            $breadcrumbs->addItem($mailer->getName());

            return $this->render('FPMailerBundle:Back:showMailer.html.twig', array( 'mailer' => $mailer));
        }else{

        }
    }
    
    //to list all categories of mailer
    public function indexCategoryAction() {

        $paginate = $this->get("index_paginate");
        $paginate->setH1('Toutes les catégories de mail');
        $paginate->setView('FPMailerBundle:Back:indexCategory.html.twig');
        $paginate->setAddNew('bo_mailer_category_new');

        $query = $this->get('mailer_category_repository')->getElements($paginate->getParamsForQuery());

        $paginate->setQuery($query);

        return $this->render($paginate->getTemplate(), $paginate->getParams());
    } 
    
    //to list all templates of mailer
    public function indexMailerAction() {

       $paginate = $this->get("index_paginate");
       $paginate->setH1('Tous les templates de mail');
       $paginate->setView('FPMailerBundle:Back:indexMailer.html.twig');
     
       $paginate->setAddNew('bo_mailer_new');
       $paginate->addFilters(new MailerFilterType(), array('by_category','by_category2','by_category3','by_category4','by_category5'));

       $query = $this->get('mailer_repository')->getElements($paginate->getParamsForQuery());

       $paginate->setQuery($query);

       $breadcrumbs = $this->get("white_october_breadcrumbs");
       $breadcrumbs->addItem("Templates de mail");

       return $this->render($paginate->getTemplate(), $paginate->getParams());
    } 
    
    //for send testing email without params. Just id template for getting params to fields
     public function testMailAction($id) {
         $request= $this->getRequest();
	 $user = $this->get('security.context')->getToken()->getUser();
         $data=array();
         if ($user) {            
             $mailer = $this->get('mailer_repository')->getElements(array('by_id' => $id, 'action' => 'one'));
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
            return $this->render('FPMailerBundle:Back:testMail.html.twig',array('data'=>$data));
         }else {
            
         }
     }
}

?>