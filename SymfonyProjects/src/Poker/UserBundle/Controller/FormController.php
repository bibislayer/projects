<?php


namespace Poker\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Poker\UserBundle\Entity\User;
use Poker\UserBundle\Form\Type\UserRoleFormType;

/**
 * Controller managing the Form
 *
 */
class FormController extends Controller {

    public function userRoleFormAction($user_id=Null){
        
        $formConf = $this->get('form_model');
        $formConf->setView('PokerUserBundle:Form:role_form.html.twig');
        $formConf->setUrlParams(array('user_id'=>$user_id));
        $formConf->setElement('user_role');
        $env = $formConf->getEnv();
        $breadcrumbs = $this->get("white_october_breadcrumbs");

        // REDIRECTION PART
        if(in_array($formConf->getAction(), array('edit', 'update'))){
            if($user_id){
                $object = $this->get('user_repository')->getUsers(array('by_id' => $user_id, 'action' => 'one'));
                if($object){
                    $formConf->setUrlParams(array('user_id' => $object->getId()));
                    $formConf->setH1('Modifier l\'user role '.$object->getFirstname());

                    if($env == 'bo'){
                        $breadcrumbs->addItem('user role', $this->get("router")->generate("bo_users"));
                        $breadcrumbs->addItem($object->getFirstname(), $this->get("router")->generate("bo_user_show", array('id' => $object->getId())));
                    }
                    $breadcrumbs->addItem('Modifier');
                }else{

                }
            }else{
            }
        }else{
            $object = new User();            
            $formConf->setH1('Ajouter user role');

            if($formConf->getEnv() == 'bo'){
                $breadcrumbs->addItem('User ', $this->get("router")->generate("bo_users"));
                $breadcrumbs->addItem('Ajout');
            }
        }

        $formConf->setForm(new UserRoleFormType(), $object);
        $params=$formConf->getParams();

        if($this->get('request')->getMethod() == 'POST'){
            // Make Validation Part
            $params = $this->userRoleProcessForm($form=$formConf->getForm(), $formConf->getObject(), $params);

            // See if is success or not and redirect to the show page or return page with error
            if (isset($params['url_success'])) {
                if ($this->get('request')->isXmlHttpRequest()) { return new Response($params['url_success']); }
                else { return $this->redirect($params['url_success']); }
            }
        } 

        return $this->render($formConf->getTemplate(), $params);
        
    }
    
    private function userRoleProcessForm($form, $obj, $params) {  
        
        $roleForm=$this->getRequest()->get($form->getName());
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if(array_key_exists('roles', $roleForm) && $roleForm['roles']!=''){
                $rolesArray=$obj->getRoles();
                $rolesArray[]=$roleForm['roles'];
                $obj->setRoles(array_unique($rolesArray));
            }
            $em->persist($obj);
            $em->flush();
            if($params['params']['env'] == 'bo_'){
                $params['url_success'] = $this->generateUrl('bo_user_show',array('id'=>$obj->getId()));  
            }                     
        } else {
           $params['errors'] = $form->getErrors(); 
        }

        return $params;
    }
    
    
    // Remove Roles
    public function removeRoleAction($id,$role){
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('user_repository')->getUsers(array('by_id' => $id, 'action' => 'one'));
        
        if ($user) {
            $rolesArray=$user->getRoles();
            if(is_array($rolesArray)){
                $key=array_search($role, $rolesArray);
                if($key!=-1){
                    unset ($rolesArray[$key]);
                    $user->setRoles($rolesArray);
                    $em->persist($user);
                    $em->flush();
                }else{
                    echo 'Invalid Arguments';die;
                }
            }
        }
        
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

}
