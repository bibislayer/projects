<?php

namespace VM\RecordingSessionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use VM\RecordingSessionBundle\Entity\RecordingSessionUser;
use VM\RecordingSessionBundle\Form\RecordingSessionUserType;

class RecordingSessionController extends Controller {

    public function indexAction($name) {
        return $this->render('VMRecordingSessionBundle:Default:index.html.twig', array('name' => $name));
    }

    public function dashboardAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $recording_sessions = $user->getRecordingSession();
        return $this->render('VMRecordingSessionBundle:Middle:dashboard.html.twig', array('recordingSessions' => $recording_sessions));
    }

    public function foShowAction($slug_sess) {
        $request = $this->getRequest();
        $session = $request->getSession();
        if (!$session->get('session_user') || $session->get('session_user') == null) {
            return $this->redirect($this->generateUrl('session_login', array('slug_sess' => $slug_sess)));
        }
        $user = $this->get('security.context')->getToken()->getUser();
        $recording_session = $this->get('recording_session_repository')->getElements(array('by_slug' => $slug_sess, 'action' => 'one'));
        if ($request->getMethod() == 'POST') {
                $session_user = $this->get('recording_session_user_repository')->getElements(array('by_id' => $session->get('session_user'), 'action' => 'one'));
                $em = $this->getDoctrine()->getManager();
                $session_user->setFilename($request->get('filename'));
                $em->persist($session_user);
                $em->flush();
                return $this->redirect($this->generateUrl('fo_recording_session_show', array('slug_sess' => $slug_sess)));
        }
        return $this->render('VMRecordingSessionBundle:Default:show.html.twig', array('recordingSession' => $recording_session));
    }
    
    public function moShowAction($slug_sess) {
        $request = $this->getRequest();
        $session = $request->getSession();
        $user = $this->get('security.context')->getToken()->getUser();
        $recording_session = $this->get('recording_session_repository')->getElements(array('by_slug' => $slug_sess, 'action' => 'one'));
        if ($request->getMethod() == 'POST') {
                
        }
        return $this->render('VMRecordingSessionBundle:Middle:show.html.twig', array('recordingSession' => $recording_session));
    }
    
    public function moSessionUserDeleteAction($slug_sess){
         $request = $this->getRequest();
         $em = $this->getDoctrine()->getManager();
         $id = $request->get('id');
         $session_user = $this->get('recording_session_user_repository')->getElements(array('by_id' => $id, 'action' => 'one'));
         $em->remove($session_user);
         $em->flush();
         
         echo $id;
         exit;
    }
    
    public function moSessionDashboardAction($slug_sess) {
        $recording_session = $this->get('recording_session_repository')->getElements(array('by_slug' => $slug_sess, 'action' => 'one'));
        return $this->render('VMRecordingSessionBundle:Middle:session_dashboard.html.twig', array('recordingSession' => $recording_session));
    }

    public function sessionLoginAction($slug_sess) {
        $request = $this->getRequest();
        $session = $request->getSession();
        if ($session->get('session_user') != null) {
            $this->redirect($this->generateUrl('fo_recording_session_show', array('slug_sess' => $slug_sess)));
        }     
          
        $recording_session = $this->get('recording_session_repository')->getElements(array('by_slug' => $slug_sess, 'action' => 'one'));
        $session_user = new RecordingSessionUser();
        $session_user->setRecordingSession($recording_session);

        $form = $this->get('form.factory')->create(new RecordingSessionUserType(), $session_user);
        
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $session_user = $form->getData();
                $em->persist($session_user);
                $em->flush();
                $session->set('session_user', $session_user->getId());
                return $this->redirect($this->generateUrl('fo_recording_session_show', array('slug_sess' => $slug_sess)));
            }
        }
        return $this->render('VMRecordingSessionBundle:Default:login.html.twig', array('form' => $form->createView()));
    }
    
    public function sessionProcessAction($slug_sess) {
        $request = $this->getRequest();
        $session = $request->getSession();
        if (!$session->get('session_user') || $session->get('session_user') == null) {
            return $this->redirect($this->generateUrl('session_login', array('slug_sess' => $slug_sess)));
        }   
          
        $session_user = $this->get('recording_session_repository')->getElements(array('by_id' => $session->get('session_user'), 'action' => 'one'));

        if ($request->getMethod() == 'POST') {
            
                $em = $this->getDoctrine()->getManager();
                $session_user->setFilename($recording_session);
                $em->persist($session_user);
                $em->flush();
                return $this->redirect($this->generateUrl('fo_recording_session_show', array('slug_sess' => $slug_sess)));
            
        }
        return $this->render('VMRecordingSessionBundle:Default:login.html.twig', array('form' => $form->createView()));
    }

}