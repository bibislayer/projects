<?php

namespace VM\RecordingSessionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use VM\RecordingSessionBundle\Form\RecordingSessionType;
use VM\RecordingSessionBundle\Entity\RecordingSession;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use VM\GeneralBundle\Twig\VMExtension;

class FormController extends Controller {

    /**
     * Form action to choose Questionnaire process creation
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function recordingSessionCreationAction($slug_ent) {

        $enterprise = $this->get('enterprise_repository')->getElements(array('action' => 'one', 'by_slug' => $slug_ent));

        return $this->render('VMRecordingSessionBundle:Form:recording_session_creation.html.twig', array('enterprise' => $enterprise));
    }

    //Form action for Questionnaire
    public function recordingSessionFormAction($slug_quest = NULL) {

        $request = $this->get('request');
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $formConf = $this->get('form_model');

        //getting environment
        $env = $formConf->getEnv();      

        $formConf->setView('VMRecordingSessionBundle:Form:recording_session_form.html.twig');

        $formConf->setElement('recording_session');
        
        $formConf->setUrlParams(array('slug_quest' => $slug_quest));
        //$breadcrumbs->addItem('Questionnaires', $this->get("router")->generate($env . "_recordingSessions"));
        
        //$originalQCategory = array();
        // REDIRECTION PART
        if (in_array($formConf->getAction(), array('edit', 'update'))) {
            if ($slug_quest) {
                $object = $this->get('recording_session_repository')->getElements(array('by_slug' => $slug_quest, 'action' => 'one'));
                if ($object) {
                    $formConf->setH1($this->get('translator')->trans('mo.recording_session.title.edit', array('%quest%' => $object->getName())));
                    if ($env == 'bo')
                        $breadcrumbs->addItem($object->getName(), $this->get("router")->generate("bo_recording_session_show", array('slug_ent' => $enterprise['slug'], 'slug_quest' => $object->getSlug())));
                    else
                        $breadcrumbs->addItem($object->getName(), $this->get("router")->generate("mo_recording_session_show", array('slug_quest' => $object->getSlug())));
                    $breadcrumbs->addItem('Modifier');
                    /*
                    foreach ($object->getQuestionnaireCategory() as $qcObj)
                        $originalQCategory[$qcObj->getId()] = $qcObj;
                    */
                    //$formConf->setExtraParams(array('categoryObj' => $originalQCategory));
                    $formConf->setForm(new QuestionnaireType($this->getDoctrine()), $object, array('env' => $env, 'formule' => ($object->getTextPayment() ? 2 : 1)));
                }
            } else {
                
            }
        } else {
            $formConf->addGeneralHelp();
            $object = new RecordingSession();

            $formConf->setH1($this->get('translator')->trans('mo.recording_session.title.new'));

            //$breadcrumbs->addItem('Nouveau');

            $formConf->setForm(new RecordingSessionType($this->getDoctrine()), $object, array('env' => $env));  
        }


        if ($this->get('request')->getMethod() == 'POST') {
            // Make Validation Part
            $params = array();
            $params['data'] = $this->get('request')->get('recording_session');

            $params['env'] = $env;

            $params = $this->recordingSessionProcessForm($formConf->getForm(), $formConf->getObject(), $params);


            // See if is success or not and redirect to the show page or return page with error
            if (isset($params['url_success'])) {
                if ($this->get('request')->isXmlHttpRequest()) {
                    return new Response($params['url_success']);
                } else {
                    return $this->redirect($params['url_success']);
                }
            }
            //if errors occuring
            if (isset($params['errors'])) {
                $formConf->setErrors($params['errors']);
            }
        }

        return $this->render($formConf->getTemplate(), $formConf->getParams());
    }

    //function to perform adding and updating a recordingSession
    private function recordingSessionProcessForm($form, $obj, $params) {
        $data = $this->getRequest()->get($form->getName());
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $sess = $form->getData();
            $user = $this->get('security.context')->getToken()->getUser();
            $sess->setUser($user);
            foreach ($sess->getRecordingSessionKeywordList() as $keyword) {
               $keyword->setRecordingSession($sess);
               $em->persist($keyword);
               $em->flush();
            }
            $em->persist($sess);
            $em->flush();

            if ($params['env'] == 'mo')
                $params['url_success'] = $this->generateUrl('mo_dashboard');
            else
                $params['url_success'] = $this->generateUrl('bo_recordingSession_elements', array('slug_quest' => $obj->getSlug()));
        } else {
            $params['errors'] = $form->getErrors();
        }

        return $params;
    }
}

