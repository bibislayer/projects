<?php

namespace VM\RecordingSessionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VM\RecordingSessionBundle\Entity\RecordingSessionUser;
use VM\RecordingSessionBundle\Form\RecordingSessionUserType,
    VM\RecordingSessionBundle\Entity\RecordingSessionKeywordList;

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
            $cmd = 'ffmpeg -y -i '.$request->get('filename').'.flv -s 640x480 -ar 44100 -pass 1 -b 1400k -r 30 -ab 128k -f avi '.$request->get('filename').'.avi';
            exec($cmd);
            pclose(popen("nohup " . $cmd . " & ", "r"));
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

    public function moSessionUserDeleteAction($slug_sess) {
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
            $cmd = 'ffmpeg -y -i '.$recording_session.'.flv -s 640x480 -ar 44100 -pass 1 -b 1400k -r 30 -ab 128k -f avi '.$recording_session.'.avi';
            pclose(popen("nohup " . $cmd . " & ", "r"));
            
            $em = $this->getDoctrine()->getManager();
            $session_user->setFilename($recording_session);
            $em->persist($session_user);
            $em->flush();
            $kernel = $this->get('kernel');
            
            return $this->redirect($this->generateUrl('fo_recording_session_show', array('slug_sess' => $slug_sess)));
        }
        return $this->render('VMRecordingSessionBundle:Default:login.html.twig', array('form' => $form->createView()));
    }

    public function moAjaxSaveFormAction($slug_sess) {
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            if ($request->request->get('word')) {
                $text = $request->request->get('word');
                $recording_session = $this->get('recording_session_repository')->getElements(array('by_slug' => $slug_sess, 'action' => 'one'));
                $recording_session_word = new RecordingSessionKeywordList();
                $recording_session_word->setName($text);
                $recording_session_word->setRecordingSession($recording_session);
                $em = $this->getDoctrine()->getManager();
                $em->persist($recording_session_word);
                $em->flush();
                echo $text;
                exit;
            }
        }
    }
    
    public function moAjaxSaveWordAction($slug_sess) {
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            if ($request->request->get('text') && $request->request->get('type')) {
                $text = $request->request->get('text');
                $recording_session = $this->get('recording_session_repository')->getElements(array('by_slug' => $slug_sess, 'action' => 'one'));
                $em = $this->getDoctrine()->getManager();
                if ($request->request->get('type') == "name") {
                    $recording_session->setName($text);
                }
                if ($request->request->get('type') == "introduction") {
                    $recording_session->setTextIntroduction($text);
                }
                if ($request->request->get('type') == "presentation") {
                    $recording_session->setTextPresentation($text);
                }
                $em->persist($recording_session);
                $em->flush();
                echo $text;
                exit;
            }
        }
    }
    
    public function moAjaxDelWordAction($slug_sess) {
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            if ($request->request->get('text') && $request->request->get('type')) {
                $text = $request->request->get('text');
                $recording_session = $this->get('recording_session_repository')->getElements(array('by_slug' => $slug_sess, 'action' => 'one'));
                $em = $this->getDoctrine()->getManager();
                if ($request->request->get('type') == "name") {
                    $recording_session->setName($text);
                }
                if ($request->request->get('type') == "introduction") {
                    $recording_session->setTextIntroduction($text);
                }
                if ($request->request->get('type') == "presentation") {
                    $recording_session->setTextPresentation($text);
                }
                $em->persist($recording_session);
                $em->flush();
                echo $text;
                exit;
            }
        }
    }

}
