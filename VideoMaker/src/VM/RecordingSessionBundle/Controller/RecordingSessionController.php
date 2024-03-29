<?php

namespace VM\RecordingSessionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VM\RecordingSessionBundle\Entity\RecordingSessionUser;
use VM\RecordingSessionBundle\Form\RecordingSessionUserType,
    VM\RecordingSessionBundle\Entity\RecordingSessionKeywordList;
use Symfony\Component\Process\Process;
use Symfony\Component\HttpFoundation\Response;

class RecordingSessionController extends Controller {

    public function indexAction($name) {
        return $this->render('VMRecordingSessionBundle:Default:index.html.twig', array('name' => $name));
    }

    public function dashboardAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $recording_sessions = $user->getRecordingSession();
        return $this->render('VMRecordingSessionBundle:Middle:dashboard.html.twig', array('recordingSessions' => $recording_sessions));
    }

    public function moDownloadMovieAction($filename) {
        $kernel = $this->get('kernel');
        $streamsPath = $kernel->getRootDir() . '/../web/uploads/streams/';
        $fichier = $streamsPath . $filename;
        $response = new Response();
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', "application/force-download");
        $response->headers->set('Content-Disposition', sprintf('attachment;filename=' . $filename, $fichier, 'force-download'));
        $response->setContent(file_get_contents($fichier));
        $response->setCharset('UTF-8');

        // prints the HTTP headers followed by the content
        $response->send();
        return $response;
    }

    public function foShowAction($slug_sess) {
        $request = $this->getRequest();
        $session = $request->getSession();
        if (!$session->get('session_user') || $session->get('session_user') == null) {
            return $this->redirect($this->generateUrl('session_login', array('slug_sess' => $slug_sess)));
        }
        $user = $this->get('security.context')->getToken()->getUser();
        $userSession = $session->get('session_user');
        $recording_session = $this->get('recording_session_repository')->getElements(array('by_slug' => $slug_sess, 'action' => 'one'));
        $kernel = $this->get('kernel');
        $streamsPath = $kernel->getRootDir() . '/../web/uploads/streams/';
        if ($request->getMethod() == 'POST') {
            $session_user = $this->get('recording_session_user_repository')->getElements(array('by_email' => $session->get('session_user'), 'action' => 'one'));
            $em = $this->getDoctrine()->getManager();
            if($session_user->getFilename()){
                $files = $session_user->getFilename();
            }else{
                $files = array();
            }
            $exist = 0;
            foreach($request->files as $uploadedFile) {
                if($uploadedFile){
                    $exist = 1;
                    $uploadedFile->move($streamsPath, $uploadedFile->getClientOriginalName());
                    $session_user->setFilename(array_merge($files, array($uploadedFile->getClientOriginalName())));
                }  
            }
            if(!$exist){
                $cmd = 'ffmpeg -y -i ' . $streamsPath . $request->get('filename') . '.flv -s 640x480 -ar 44100 -b 1400k -r 30 -ab 128k -f avi ' . $streamsPath . $request->get('filename') . '.avi >> ' . $kernel->getRootDir() . '/logs/encoder.txt';
                //$cmd = 'touch '.$streamsPath.'test.txt';
                $process = new Process($cmd);
                $process->run();

                // executes after the command finishes
                if (!$process->isSuccessful()) {
                    throw new \RuntimeException($process->getErrorOutput());
                }
                $session_user->setFilename(array_merge($files, array($request->get('filename'))));
            }
            $em->persist($session_user);
            $em->flush();
            $session->set('flag', 'Votre enregistrement à bien était transmis à notre serveur ;)');
            return $this->redirect($this->generateUrl('fo_recording_session_show', array('slug_sess' => $slug_sess)));
        }
        return $this->render('VMRecordingSessionBundle:Default:show.html.twig', array('recordingSession' => $recording_session));
    }
    
    public function moSuccessAction($slug_sess) {
        $request = $this->getRequest();
        $session = $request->getSession();
        $user = $this->get('security.context')->getToken()->getUser();
        $recording_session = $this->get('recording_session_repository')->getElements(array('by_slug' => $slug_sess, 'action' => 'one'));
        return $this->render('VMRecordingSessionBundle:Middle:success.html.twig', array('recordingSession' => $recording_session));
    }
    
     public function foSuccessAction($slug_sess) {
        $request = $this->getRequest();
        $session = $request->getSession();
        $user = $this->get('security.context')->getToken()->getUser();
        $recording_session = $this->get('recording_session_repository')->getElements(array('by_slug' => $slug_sess, 'action' => 'one'));
        return $this->render('VMRecordingSessionBundle:Default:success.html.twig', array('recordingSession' => $recording_session));
    }
    
    public function moShowAction($slug_sess) {
        $request = $this->getRequest();
        $session = $request->getSession();
        $user = $this->get('security.context')->getToken()->getUser();
        $recording_session = $this->get('recording_session_repository')->getElements(array('by_slug' => $slug_sess, 'action' => 'one'));
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
                $session_user = $this->get('recording_session_user_repository')->getElements(array('by_email' => $form->getData()->getEmail(), 'action' => 'one'));
                if(!$session_user){
                    $session_user = $form->getData();
                    $em->persist($session_user);
                    $em->flush();
                }
                $session->set('session_user', $form->getData()->getEmail());
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

        $session_user = $this->get('recording_session_repository')->getElements(array('by_email' => $session->get('session_user'), 'action' => 'one'));

        if ($request->getMethod() == 'POST') {
            $cmd = 'ffmpeg -y -i ' . $recording_session . '.flv -s 640x480 -ar 44100 -pass 1 -b 1400k -r 30 -ab 128k -f avi ' . $recording_session . '.avi';
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
    
     public function moAjaxSaveSuccessAction($slug_sess) {
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            if ($request->request->get('text')) {
                $text = $request->request->get('text');
                $recording_session = $this->get('recording_session_repository')->getElements(array('by_slug' => $slug_sess, 'action' => 'one'));
                $em = $this->getDoctrine()->getManager();
                $recording_session->setSuccess($text);
                $em->persist($recording_session);
                $em->flush();
                echo $text;
            }
        }
        exit;
    }

    public function moAjaxSaveWordAction($slug_sess) {
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
            }
        }
        exit;
    }

    public function moAjaxDelWordAction($slug_sess) {
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            if ($request->request->get('word')) {
                $name = $request->request->get('word');
                $recording_session_word = $this->get('recording_session_keyword_list_repository')->getElements(array('by_recording_session' => $slug_sess,
                    'by_name' => $name, 'action' => 'one'));
                $em = $this->getDoctrine()->getManager();
                $em->remove($recording_session_word);
                $em->flush();
            }
        }
        exit;
    }

    public function moAjaxSaveFormAction($slug_sess) {
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
            }
        }
        exit;
    }
}
