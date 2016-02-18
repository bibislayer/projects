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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Process\Process;
use Openbuildings\Swiftmailer\CssInlinerPlugin;


class DefaultController extends Controller
{
    /**
     * @Route("/files/")
     * @Route("/files", name="get_files")
     * @Template()
     */
    public function indexAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$file = new File($this->get('security.context')->getToken()->getUser());
	    $form = $this->createFormBuilder($file)
	        ->add('file')
	        ->getForm();
	    $files = $em->getRepository('FACFileBundle:File')
	    		->findBy(array('user' => $this->get('security.context')->getToken()->getUser(), 'type' => 'Directory'), array('name' => 'ASC'));
        return array('menuShow' => 'true', 'form' => $form->createView(), 'files' => $files);
    }

    /**
     * @Route("/upload/{id_folder}")
     * @Template()
     */
    public function uploadAction()
    {
        $em = $this->getDoctrine()->getManager();
        $file = new File($this->get('security.context')->getToken()->getUser());
        $folder = $em->getRepository('FACFileBundle:File')->findOneBy(array('id' => $this->getRequest()->get('id_folder')));
        $form = $this->createFormBuilder($file)
            ->add('file')
            ->getForm();
        $this->get('session')->set('pagesUpload', $folder->getId());
        return array('menuShow' => 'false', 'form' => $form->createView(), 'folder' => $folder);
    }
    /**
     * @Route("/uploads")
	 */
	public function uploadsAction()
	{
	    $file = new File($this->get('security.context')->getToken()->getUser());
	    $form = $this->createFormBuilder($file)
	        ->add('file')
	        ->getForm();

	    if ($this->getRequest()->isMethod('POST')) {
	        $form->handleRequest($this->getRequest());
	        if ($form->isValid()) {
	            $em = $this->getDoctrine()->getManager();
	            $file->setPath($this->get('session')->get('path'));
	            $em->persist($file);
	            $em->flush();
	            if($file->getType() == 'video/x-msvideo' || $file->getType() == 'video/x-matroska' || $file->getType() == 'video/mp4'){
	            	$webpath = $this->get('kernel')->getRootDir() . '/../web';
					$path = $webpath.$file->getPath().'/';
					if($file->getType() != 'video/mp4'){
	            		$noExt = substr($file->getName(),0,-4);
	            		$cmd = 'ffmpeg -i '.$path.$file->getName().' -crf 19 -preset slow -acodec aac -strict experimental -b 345k -ac 2 -y '.$path.$noExt.'.mp4';
	            		$cmd2 = 'ffmpeg -i '.$path.$file->getName().' -acodec libvorbis -ac 2 -b 345k -y '.$path.$noExt.'.ogg';
	            		$cmd3 = 'ffmpeg -i '.$path.$file->getName().' -acodec libvorbis -ac 2 -b 345k -y '.$path.$noExt.'.webm';
	            		$process=new Process($cmd);
	            		$process->start();
	            		$process2=new Process($cmd2);
	            		$process2->start();
	            		$process3=new Process($cmd3);
	            		$process3->start();
            		}
				}
				$serializer = $this->get('jms_serializer');
				$jsonObject = $serializer->serialize($file, 'json');
				$file =  json_decode($jsonObject);

	            return new JsonResponse(array(
			        'success' => true,
			        'file' => $file
			    ));
	        }else{
		        return new JsonResponse(array(
				        'error' => $form->getErrors(true, false)
				    ));
		    }
	    } else{
	        return new JsonResponse(array(
			        'error' => 'no get method allowed'
			    ));
	    }
	}

	/**
     * @Route("/create-folder")
	 * @Template()
	 */
	public function createFolderAction()
	{
	    if ($this->getRequest()->isMethod('POST')) {
        	$em = $this->getDoctrine()->getManager();
        	$serializer = $this->get('jms_serializer');
	        $usr = $this->get('security.context')->getToken()->getUser();

	        $parent_file = $em->getRepository('FACFileBundle:File')->findOneBy(array('id' => $this->getRequest()->request->get('parent_id')));

	        $file = new File($usr);
	        $file->setName($this->getRequest()->request->get('folder_name'));
	        $file->setParentId($this->getRequest()->request->get('parent_id'));
	        $file->setLevel($parent_file->getLevel() + 1);
	        $file->setPath($this->get('session')->get('path').'/'.$this->getRequest()->request->get('folder_name'));
	        $file->setType('Directory');
	        $file->setStatus($parent_file->getStatus());
	        $webpath = $this->get('kernel')->getRootDir() . '/../web';
			$filepath = $webpath.$file->getPath();
			$fs = new Filesystem();
			$fs->mkdir($filepath, 0777);
	        $em->persist($file);

	        $usr->setSelectedFolder($file->getId());
	        $em->persist($usr);
            $em->flush();
	    	
	    	$jsonObject = $serializer->serialize($file, 'json');
			$file =  json_decode($jsonObject);

	    	return new JsonResponse(array(
		        'success' => true,
		        'file' => $file
		    ));
	    }
	}

	/**
     * @Route("/remove-folder")
	 * @Template()
	 */
	public function removeFolderAction()
	{
	    if ($this->getRequest()->isMethod('POST')) {
        	$em = $this->getDoctrine()->getManager();
	        $usr = $this->get('security.context')->getToken()->getUser();

	        $file = $em->getRepository('FACFileBundle:File')->findOneBy(array(
	        														'id' => $this->getRequest()->request->get('id'),
	        														'user' => $usr
	        													));
	        if($file){
	        	$childrens = $em->getRepository('FACFileBundle:File')->findBy(array(
	        														'parentId' => $this->getRequest()->request->get('id'),
	        														'user' => $usr
	        													));
	        	foreach($childrens as $child){
	        		$em->remove($child);
	        	}
		        $em->remove($file);
	            $em->flush();

		    	return new JsonResponse(array(
			        'success' => true,
			        'id' => $this->getRequest()->request->get('id')
			    ));
		    }else{
		    	return new JsonResponse(array(
			        'success' => false,
			        'message' => 'file not found'
			    ));
		    }
	    }
	}

	/**
     * @Route("/remove-files")
	 * @Template()
	 */
	public function removeFilesAction()
	{
	    if ($this->getRequest()->isMethod('POST')) {
	    	if($this->getRequest()->request->get('id')){
	    		$em = $this->getDoctrine()->getManager();
		        $usr = $this->get('security.context')->getToken()->getUser();
		        $arrIds;
	    		foreach($this->getRequest()->request->get('id') as $id){
	    			if(gettype($id) != 'array'){
	    				$file = $em->getRepository('FACFileBundle:File')->findOneBy(array(
	        														'id' => $id,
	        														'user' => $usr
	        													));
	    				if($file){
	    					$arrIds[] = $id;
					        $em->remove($file);
				            $em->flush();
					    }
	    			}
	    		}
	    		return new JsonResponse(array(
			        'success' => true,
			        'id' => $arrIds
			    ));
	    	}	        
	    }
	}

	/**
     * @Route("/move-files")
	 * @Template()
	 */
	public function moveFilesAction()
	{
	    if ($this->getRequest()->isMethod('POST')) {
	    	if($this->getRequest()->request->get('id') && $this->getRequest()->request->get('folder')){
	    		$em = $this->getDoctrine()->getManager();
		        $usr = $this->get('security.context')->getToken()->getUser();
		        $arrIds;
		        $parent = $em->getRepository('FACFileBundle:File')->findOneBy(array(
	        														'id' => $this->getRequest()->request->get('folder'),
	        														'user' => $usr
	        													));
	    		foreach($this->getRequest()->request->get('id') as $id){
	    			if(gettype($id) != 'array'){
	    				$file = $em->getRepository('FACFileBundle:File')->findOneBy(array(
	        														'id' => $id,
	        														'user' => $usr
	        													));
	    				if($file){
	    					$file->setParentId($parent->getId());
	    					$webpath = $this->get('kernel')->getRootDir() . '/../web';
							$filepath = $webpath.$file->getPath();
	    					$fs = new Filesystem();
	        				try {
				                $fs->rename($filepath.'/'.$file->getName(), $webpath.$parent->getPath().'/'.$file->getName());
				            } catch (IOExceptionInterface $e) {
				                echo "An error occurred while deleting at ".$e->getPath();
				            }
				            $file->setPath($parent->getPath());
	    					$arrIds[] = $id;
					        $em->persist($file);
				            $em->flush();
					    }
	    			}
	    		}
	    		return new JsonResponse(array(
			        'success' => true,
			        'id' => $arrIds
			    ));
	    	}	        
	    }
	}

	/**
    * @Route("/selected-folder", name="selected-folder")
	* @Template()
	*/
	public function selectedFolderAction()
	{
	    if ($this->getRequest()->isMethod('POST')) {
	    	$cacheManager = $this->container->get('liip_imagine.cache.manager');
	        $id = $this->getRequest()->request->get('id');
	        if($id){
	        	$em = $this->getDoctrine()->getManager();
	        	$securityContext = $this->get('security.context');
		        $usr = $securityContext->getToken()->getUser();
				if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			        $usr->setSelectedFolder($id);
			        $usr->setBackParams(array('display' => $this->getRequest()->request->get('display')));
			        $em->persist($usr);
		            $em->flush();
		        }
	            //folder gestion
	            $file = $em->getRepository('FACFileBundle:File')->findOneBy(array('id' => $id));
	            $allowedUsers = array($file->getAllowedUsers());
				$query = $em->createQuery('SELECT f.id, f.name, f.parentId, f.path, f.size, f.type, f.uniqueKey, f.level
											   FROM FACFileBundle:File f
					    					   WHERE f.parentId = '.$id.'');

				$files = $query->getResult();
					
				foreach($files as $k => $f){
					if($f['type'] == 'image/jpeg' || $f['type'] == 'image/png'){
						$files[$k]['thumb'] = $cacheManager->getBrowserPath($f['path'].'/'.$f['name'], 'tiny');
						$files[$k]['full'] = $cacheManager->getBrowserPath($f['path'].'/'.$f['name'], 'full');
					}
				}
				
	            if ($file->getUser() == $usr){
			    	$this->get('session')->set('path', $file->getPath());
					$link = ($this->getRequest()->isSecure()) ? 'https' : 'http';
					$link .= '://'.$this->getRequest()->getHost().$this->generateUrl('folder_show', array('user_name' => $usr->getUsername(), 
			    															'folder_name' => $file->getName()));
			    	$folderArray = array(
			    		'allowed_users' => $file->getAllowedUsers(),
			    		'status' => $file->getStatus(),
			    		'password' => $file->getPassword(),
			    		'link' => $link
		    		);

			    	$serializer = $this->get('jms_serializer');
			    	$jsonObject = $serializer->serialize($files, 'json');
					$array =  json_decode($jsonObject);

					//
					$jsonObject2 = $serializer->serialize($folderArray, 'json');
					$array2 =  json_decode($jsonObject2);
                    
			    	return new JsonResponse(array(
				        'success' => true,
				        'files' => $array,
				        'user_id' => $usr->getId(),
				        'folder' => $array2,
				        'pagesUpload' => $this->get('session')->get('pagesUpload'),
				    ));
			    }
	            //shared users
	            else if($file->getStatus() == 2 || in_array($usr, $allowedUsers)){
					$this->get('session')->set('path', $file->getPath());
					$link = ($this->getRequest()->isSecure()) ? 'https' : 'http';
					$link .= '://'.$this->getRequest()->getHost().$this->generateUrl('folder_show', array('user_name' => $file->getUser()->getUsername(), 
			    															'folder_name' => $file->getName()));
			    	$folderArray = array(
			    		'allowed_users' => $file->getAllowedUsers(),
			    		'status' => $file->getStatus()
		    		);

			    	$serializer = $this->get('jms_serializer');
			    	$jsonObject = $serializer->serialize($files, 'json');
					$array =  json_decode($jsonObject);

					//
					$jsonObject2 = $serializer->serialize($folderArray, 'json');
					$array2 =  json_decode($jsonObject2);

			    	return new JsonResponse(array(
				        'success' => 'show',
				        'files' => $array,
				        'folder' => $array2
				    ));
			    }
			    else{
			    	return new JsonResponse(array(
				        'success' => false,
				        'error' => 'access denied'
				    ));
			    }
		    }
	    }
	}

	private function recusiveChildren($parentId, $status){
		$em = $this->getDoctrine()->getManager();
		$childs = $em->getRepository('FACFileBundle:File')->findBy(array('parentId' => $parentId, 'type' => 'Directory'));
		if($childs){
			foreach($childs as $child){
				$child->setStatus($status);
				$em->persist($child);
				$this->recusiveChildren($child->getId(), $status);
			}
	        $em->flush();
    	}else{
    		return '';
    	}
	}
	/**
     * @Route("/change-status")
	 * @Template()
	 */
	public function changeStatusAction()
	{
	    if ($this->getRequest()->isMethod('POST')) {
	        $status = $this->getRequest()->request->get('status');
        	$em = $this->getDoctrine()->getManager();
	        $usr = $this->get('security.context')->getToken()->getUser();

	    	$file = $em->getRepository('FACFileBundle:File')->findOneBy(array('id' => $usr->getSelectedFolder()));
	    	if($status == 2 && $file->getPassword() != '' || $status == 2 && !$file->getPassword()){
	    		$generator = new SecureRandom();
				$random = bin2hex($generator->nextBytes(8));
	    		$file->setPassword($random);
	    	}
	    	$file->setStatus($status);
	    	//child shared
    		$this->recusiveChildren($file->getId(), $status);
	    	$em->persist($file);
            $em->flush();

	    	return new JsonResponse(array(
		        'success' => true,
		        'password' => $file->getPassword(),
		        'name' => $file->getName(),
		        'username' => $usr->getUsername()
		    ));
	    }
	}

	/**
     * @Route("/invite-user")
	 * @Template()
	 */
	public function inviteUserAction()
	{
	    if ($this->getRequest()->isMethod('POST')) {
	        $folder_id = $this->getRequest()->request->get('folder_id');
	        $email = $this->getRequest()->request->get('email');
        	$em = $this->getDoctrine()->getManager();
	        $usr = $this->get('security.context')->getToken()->getUser();

			$newUser = new User();
			$arr = explode("@", $this->getRequest()->request->get('email'), 2);
			$username = $arr[0];

			$tokenGenerator = $this->get('fos_user.util.token_generator');
			$confirmation_token = substr($tokenGenerator->generateToken(), 0, 16);

			$newUser->setUsername($username);
			$newUser->setPassword($confirmation_token);
			$newUser->setConfirmationToken($confirmation_token);
			$newUser->setEmail($this->getRequest()->request->get('email'));
			$em->persist($newUser);
			$em->flush();

	    	$file = $em->getRepository('FACFileBundle:File')->findOneBy(array('id' => $folder_id, 'user' => $usr));
			$file->addAllowedUser($newUser);

            $em->flush();

	        $url = $this->generateUrl('fos_user_registration_confirm', array('token' => $newUser->getConfirmationToken()), true);
	        $sharedLink = $this->generateUrl('folder_show', array('user_name' => $usr->getUsername(), 'folder_name' => $file->getName()), true);

            $message = \Swift_Message::newInstance()
                    ->setSubject('Registration confirmation')
                    ->setFrom('donotreply@family-and-co.com')
                    ->setTo($newUser->getEmail())
                    ->setContentType('text/html')
                    ->setBody(
                    $this->renderView(
                            "FACUserBundle:Emails:invitation-email.html.twig", array(
                        'user' => $newUser,
                        'confirmationUrl' => $url,
                        'sharedLink' => $sharedLink))
                    )
            ;
            $sent = $this->get('mailer')->send($message);
            //exit;
	    	return new JsonResponse(array(
		        'success' => true,
		    ));
	    }
	}

	/**
     * @Route("/get_file/{id}/{ext}")
	 * @Template()
	 */
	public function getFileAction()
	{
		$id = $this->getRequest()->get('id');
        if($id){
        	$em = $this->getDoctrine()->getManager();
	        $usr = $this->get('security.context')->getToken()->getUser();
	    	$file = $em->getRepository('FACFileBundle:File')->findOneBy(array('id' => $id));

	    	$noExt = substr($file->getName(),0,-4);
			$filename = $noExt.'.'.$this->getRequest()->get('ext');
	    	$webpath = $this->get('kernel')->getRootDir() . '/../web';
			$filepath = $webpath.$file->getPath().'/'.$filename;
			$fs = new FileSystem();
	        if (!$fs->exists($filepath)) {
	            throw $this->createNotFoundException();
	        }

	        $response = new Response();
		    $response->setContent(file_get_contents($filepath));
		    $response->headers->set('Content-Type', 'application/force-download'); // modification du content-type pour forcer le téléchargement (sinon le navigateur internet essaie d'afficher le document)
		    $response->headers->set('Content-disposition', 'filename='. $filename);
		         
		    return $response;
	    }
	}
}