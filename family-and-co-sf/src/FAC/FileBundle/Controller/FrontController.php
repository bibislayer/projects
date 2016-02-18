<?php

namespace FAC\FileBundle\Controller;

use FAC\FileBundle\Entity\File;
use FAC\UserBundle\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Openbuildings\Swiftmailer\CssInlinerPlugin;


class FrontController extends Controller
{
	/**
     * @Route("/u/{user_name}/{folder_name}/file-login", name="file_login")
	 * @Template("FACFileBundle:Default:file-login.html.twig")
	 */
	public function fileLoginAction()
	{
	    $em = $this->getDoctrine()->getManager();
	    $usr = $em->getRepository('FACUserBundle:User')->findOneBy(array('username' => $this->getRequest()->get('user_name')));
	    if($usr){
	    	$file = $em->getRepository('FACFileBundle:File')->findOneBy(array(
		        														'name' => $this->getRequest()->get('folder_name'),
		        														'user' => $usr
		        													));
		    $form = $this->createFormBuilder($file)
		        ->getForm();
		    if ($this->getRequest()->isMethod('POST')) {
		    	$form->handleRequest($this->getRequest());
		        if ($form->isValid()) {
		        	if($this->getRequest()->request->get('password') == $file->getPassword()){
			        	$this->get('session')->set('anonyme', md5($file->getUniqueKey()));
			        	return $this->redirect( $this->generateUrl('folder_show',
													array('user_name' => $this->getRequest()->get('user_name'), 
														  'folder_name' => $this->getRequest()->get('folder_name'))));
		        	}else{
		        		return array('form' => $form->createView(), 'error' => 'Une erreur est survenu');
		        	}
		        }
		    }
		    return array('form' => $form->createView(), 'error' => '');
		}else{
			return new JsonResponse(array(
				'success' => false,
			    'message' => 'file not found'
			));
		}
	}

	/**
	* @Route("/u/{user_name}/{folder_name}/")
    * @Route("/u/{user_name}/{folder_name}", name="folder_show")
	* @Template("FACFileBundle:Default:show.html.twig")
	*/
	public function getShowFolderAction()
	{
		$user = $this->get('security.context')->getToken()->getUser();
		$user_name = $this->getRequest()->get('user_name');
        if($user_name){
        	$em = $this->getDoctrine()->getManager();
        	$usr = $em->getRepository('FACUserBundle:User')->findOneBy(array('username' => $user_name));
        	if($user){
        		$folder_name = $this->getRequest()->get('folder_name');
	        	$file = $em->getRepository('FACFileBundle:File')->findOneBy(array('name' => $folder_name, 'user' => $usr));
	        	if(!$this->get('session')->get('anonyme') || $this->get('session')->get('anonyme') != md5($file->getUniqueKey()))
					return $this->redirect( $this->generateUrl('file_login', array('user_name' => $this->getRequest()->get('user_name'), 
													  'folder_name' => $this->getRequest()->get('folder_name'))) );
	        	if($file->getStatus() == 2 || in_array($user, $file->getAllowedUsers())){
	        		$files = $em->getRepository('FACFileBundle:File')
	        				->findBy(array('user' => $usr, 'status' => 2, 'type' => 'Directory'), array('name' => 'ASC'));
	        		return array('menuShow' => true, 'files' => $files);
	        	}
        	}
        }
	}
}