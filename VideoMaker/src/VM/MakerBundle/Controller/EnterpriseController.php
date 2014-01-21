<?php

namespace VM\MakerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Model\UserInterface;
use VM\MakerBundle\Entity\Maker;
use VM\MakerBundle\Entity\MakerAdministrator;
use VM\StandardBundle\Twig\VMExtension;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MakerController extends Controller
{
    public function moShowAction($slug_ent) {

        $maker = $this->get('maker_repository')->getElements(array('action' => 'one', 'by_slug' => $slug_ent));
        
        if($maker){
            // BREADCRUMBS
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem($maker->getName());
            return $this->render('VMMakerBundle:Maker/Middle:show.html.twig', array('maker'=>$maker,'isCollaborator'=> 1));
        }
    }

    public function boIndexAction() {
        $paginate = $this->get("index_paginate");
        $paginate->setH1('Toutes les maker');
        $paginate->setView('VMMakerBundle:Maker/Back:index.html.twig');
        $paginate->setAddNew('bo_maker_new');

        $query = $this->get('maker_repository')->getElements($paginate->getParamsForQuery());

        $paginate->setQuery($query);
        return $this->render($paginate->getTemplate(), $paginate->getParams());
    }

    public function boShowAction($slug_ent) {
        $maker = $this->get('maker_repository')->getElements(array('by_slug' => $slug_ent, 'action' => 'one'));

        if($maker){
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem("Show maker", $this->get("router")->generate("bo_makers"));
            $breadcrumbs->addItem('');
            return $this->render('VMMakerBundle:Maker/Back:show.html.twig', array( 'maker' => $maker));
        }else{

        }
    }
    
    public function boDeleteAction() {
        //delete an maker by id if exist
    } 
    
    public function createMakerForUser(UserInterface $user,$userForm=array()){       
        
        
        $em =array_key_exists('em', $userForm)?$userForm['em']:$this->getDoctrine()->getManager();
        $form=array_key_exists('form', $userForm)?$userForm['form']:Null;
        $request=array_key_exists('request', $userForm)?$userForm['request']:$this->getRequest();
        
        $makerAdministrator = new MakerAdministrator();
            if (is_array($userForm) && array_key_exists('maker', $userForm)) {
                $maker= new Maker();
                if (array_key_exists('name', $userForm['maker']) && $userForm['maker']['name'] != '') {
                    $maker->setName($userForm['maker']['name']);
                }
                if (array_key_exists('code_siret', $userForm['maker']) && $userForm['maker']['code_siret'] != '') {
                    $maker->setCodeSiret($userForm['maker']['code_siret']);
                }
                if (array_key_exists('phone', $userForm['maker']) && $userForm['maker']['phone'] != '') {
                    $maker->setPhone($userForm['maker']['phone']);
                }
                if (array_key_exists('url_site', $userForm['maker']) && $userForm['maker']['url_site'] != '') {
                    $maker->setUrlSite($userForm['maker']['url_site']);
                }
                $em->persist($maker);
                $em->flush();
                $this->uploadImage($form['maker']['logo'],$maker,$request,$em);
                $makerAdministrator->setMaker($maker);
                $makerAdministrator->setUser($user); 
                $makerAdministrator->setRoles(array('ROLE_COLLAB')); 
                $em->persist($makerAdministrator);
                $em->flush();
            }else if(array_key_exists('makerObj', $userForm)){
                $maker= $userForm['makerObj'];
                $makerAdministrator->setMaker($maker);
                $makerAdministrator->setUser($user); 
                $makerAdministrator->setRoles(array('ROLE_COLLAB')); 
            }
            return $maker;
    }
    public function uploadImage($formObject,$maker,$request,$em){
        if ($formObject->getData()) {
            $extensionObj = new VMExtension();
            $filename = $formObject->getData()->getClientOriginalName();
            $position = strpos($filename, ".");
            // enleve l'extention, tout ce qui se trouve apres le '.'
            $imageName = substr($filename, 0, $position);
            $extension = strrchr($filename, '.');
            $extension = substr($extension, 1);
            //echo 'uploads/gallery/makers/'.$maker->getId().'/pictures/'.$extensionObj->slugify($imageName).'.'.$extension; die;
            $maker->setLogo('uploads/gallery/makers/'.$maker->getId().'/pictures/'.$extensionObj->slugify($imageName).'.'.$extension);            
            $em->persist($maker);
            $em->flush();
            $path_to_training = $request->server->get('DOCUMENT_ROOT') . '/uploads/gallery/makers/' . $maker->getId() . '/pictures/';

            //if already exists logo                                
            if ($request->get('logo_hidden') != '') {

                $logo = $request->get('logo_hidden');
                $positionOld = strpos($logo, ".");
                // enleve l'extention, tout ce qui se trouve apres le '.'
                $imageNameOld = substr($logo, 0, $positionOld);
                $extensionOld = strrchr($logo, '.');
                $extensionOld = substr($extensionOld, 1);

                //delete all old logos
                if (file_exists($path_to_training . $imageNameOld . '.' . $extensionOld)) {
                    unlink($path_to_training . $imageNameOld . '.' . $extensionOld);
                }
                if (file_exists($path_to_training . $imageNameOld . '_tiny.' . $extensionOld)) {
                    unlink($path_to_training . $imageNameOld . '_tiny.' . $extensionOld);
                }
                if (file_exists($path_to_training . $imageNameOld . '_small.' . $extensionOld)) {
                    unlink($path_to_training . $imageNameOld . '_small.' . $extensionOld);
                }

                if (file_exists($path_to_training . $imageNameOld . '_medium.' . $extensionOld)) {
                    unlink($path_to_training . $imageNameOld . '_medium.' . $extensionOld);
                }
                if (file_exists($path_to_training . $imageNameOld . '_big.' . $extensionOld)) {
                    unlink($path_to_training . $imageNameOld . '_big.' . $extensionOld);
                }
            }

            $uploadDestPath = 'http://' . $request->getHttpHost() . '/uploads/gallery/makers/' . $maker->getId() . '/pictures/';

            $formObject->getData()->move($request->server->get('DOCUMENT_ROOT') . '/uploads/gallery/makers/' . $maker->getId() . '/pictures', $extensionObj->slugify($imageName) . '.' . $extension);

            $src = $uploadDestPath . $extensionObj->slugify($imageName) . '.' . $extension;
            $dst = $path_to_training . $extensionObj->slugify($imageName) . '_tiny.' . $extension;
            $extensionObj->image_resize($src, $dst, 70);
            $dst = $path_to_training . $extensionObj->slugify($imageName) . '_small.' . $extension;
            $extensionObj->image_resize($src, $dst, 100);
            $dst = $path_to_training . $extensionObj->slugify($imageName) . '_medium.' . $extension;
            $extensionObj->image_resize($src, $dst, 150);
            $dst = $path_to_training . $extensionObj->slugify($imageName) . '_big.' . $extension;
            $extensionObj->image_resize($src, $dst, 250);
        }
    }
}
