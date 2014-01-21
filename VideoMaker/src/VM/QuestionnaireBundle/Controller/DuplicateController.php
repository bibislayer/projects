<?php

namespace VM\QuestionnaireBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VM\QuestionnaireBundle\Entity\Questionnaire;
use VM\QuestionnaireBundle\Entity\QuestionnaireElement;
use VM\QuestionnaireBundle\Entity\QuestionnaireQuestionChoice;
use VM\QuestionnaireBundle\Entity\Question;
use VM\QuestionnaireBundle\Form\QuestionnaireExportType;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DuplicateController extends Controller {
    protected $container;
    
    function __construct(ContainerInterface $container = NULL ) {
        $this->container = $container;
    }
    
    //Duplicate Questionnaire 
    public function questionnaireDuplicateAction($actionType) {
        $request = $this->get('request');
        $env = substr($request->get('_route'), 0, 2);

        if ($env == 'mo') {
            $access = $this->get('session')->get('access_admin');
            $enterprise = $access['current'];
            if (empty($enterprise)) {
                return $this->redirect($this->getRequest()->headers->get('referer'));
            }
        }

        $slug_quest = $this->get('request')->get('slug_quest');
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug' => $slug_quest, 'action' => 'one'));

        if ($questionnaire) {
            //Function create new questionnaire               
            if ($env == 'bo') {
                //Id Export Model 
                if ($actionType == 'exportModel') {
                    //Creating form to select enterprise
                    $formConf = $this->get('form_model');
                    $formConf->setUrlParams(array('slug_quest' => $slug_quest, 'actionType' => $actionType));
                    $formConf->setH1($this->get('translator')->trans('bo.questionnaire.title.export'));
                    $formConf->setView('VMQuestionnaireBundle:Form:questionnaire_export_form.html.twig');
                    $formConf->setElement('questionnaire_duplicate');
                    $object = new Questionnaire();
                    $formConf->setForm(new QuestionnaireExportType(), $object);


                    //If form submitted
                    if ($request->getMethod() == 'POST') {
                        $export = $request->get('QuestionnaireExport');
                        $enterprise_id = $export['enterprise']['id'];

                        //If enterprise not selected
                        if (!$enterprise_id) {
                            $formConf->setErrors("Please Select enterprise");
                        } else {
                            $this->addQuestionnaire($questionnaire, $actionType, $enterprise_id);
                            return $this->redirect($this->generateUrl('bo_questionnaires'));
                        }
                    }
                    return $this->render($formConf->getTemplate(), $formConf->getParams());
                } else {
                    $this->addQuestionnaire($questionnaire, $actionType);
                }
            }

            if ($env == 'mo') {
                if ($actionType == 'duplicate')
                    $this->addQuestionnaire($questionnaire, $actionType);
                $params = array('slug_ent' => $questionnaire->getEnterprise()->getSlug());
            }
            else {
                $params = array();
            }
            //redirect after duplicate questionnaire
            return $this->redirect($this->generateUrl($env . '_questionnaires', $params));
        } else {
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }

    //To Add questionnaire
    public function addQuestionnaire($questionnaire, $actionType, $ent_id = '') {

        $questObj = new Questionnaire();
        $em = $this->getDoctrine()->getManager();
        $questObj->setName($questionnaire->getName());

        $questObj->setStdQuestionnaireType($questionnaire->getStdQuestionnaireType());
        $questObj->setTextIntroduction($questionnaire->getTextIntroduction());
        $questObj->setTextPresentation($questionnaire->getTextPresentation());
        $questObj->setMailInvitation($questionnaire->getMailInvitation());
        $questObj->setMailAccepted($questionnaire->getMailAccepted());
        $questObj->setMailRefused($questionnaire->getMailRefused());

        $questObj->setTextPayment($questionnaire->getTextPayment());
        $questObj->setPaymentAmountBefore($questionnaire->getPaymentAmountBefore());
        $questObj->setPaymentAmountAfter($questionnaire->getPaymentAmountAfter());
        $questObj->setPaymentVat($questionnaire->getPaymentAmountBefore());

        if ($actionType == 'createModel') {
            $questObj->setIsModel(1);
            $questObj->setEnterprise(NULL);
        } else {
            $questObj->setIsModel(0);

            if ($actionType == 'duplicate') {
                $questObj->setEnterprise($questionnaire->getEnterprise());
            } else {
                $enterprise = $this->get('enterprise_repository')->getElements(array('action' => 'one', 'by_id' => $ent_id));
                $questObj->setEnterprise($enterprise);
            }
        }

        $questObj->setAnonymous($questionnaire->getAnonymous());
        $em->persist($questObj);
        $em->flush();

        if ($questObj->getId()) {

            //If Questionnaire has elements then duplicate it also
            if (count($questionnaire->getQuestionnaireElement()) > 0) {
                foreach ($questionnaire->getQuestionnaireElement() as $qe) {

                    if ($qe->getLevel() == 0) {

                        $type = $qe->getStdQuestionnaireTypeElement()->getSlug();

                        //duplicates its linked elements
                        $this->elementDuplicateAction(array('type' => $type, 'id' => $qe->getId(), 'questionnaire' => $questObj->getId()));
                    }
                }
            }
        }
        if ($actionType == 'createModel') {
            $this->indexationQuestionnaire($questObj);
        }
    }
    
    //Indexation of model
    public function indexationQuestionnaire($questObj){
       
           // create a client instance
            $client = $this->container->get('solarium.client');

            // get an update query instance
            $update = $client->createUpdate();
            $model = $update->createDocument();
            $model->id = $questObj->getId();
            
            $model->name = $questObj->getName();
            $model->intro_s = $questObj->getTextIntroduction();
            $model->desc_s = $questObj->getTextPresentation();
            $model->category_s = $questObj->getStdQuestionnaireType()->getName();
           
            // add the documents and a commit command to the update query
            $update->addDocument($model);
            $update->addCommit();

            // this executes the query and returns the result
            $client->update($update);
    }

    //Duplicate Elements ie quetsion , description, group
    public function elementDuplicateAction($params = array()) {


        $request = $this->get('request');
        $env = substr($request->get('_route'), 0, 2);
        $type = $request->get('type') ? $request->get('type') : (isset($params['type']) ? $params['type'] : '');

        //If middle office
        if ($env == 'mo') {
            $access = $this->get('session')->get('access_admin');
            $enterprise = $access['current'];

            if (empty($enterprise)) {
                return $this->redirect($this->getRequest()->headers->get('referer'));
            }
        }

        $questionnaire = isset($params['questionnaire']) ? $params['questionnaire'] : '';

        $id = $request->get('id') ? $request->get('id') : (isset($params['id']) ? $params['id'] : '');
        $slug_quest = $request->get('slug_quest');

        //Get element information 
        $element = $this->get('questionnaire_element_repository')->getElements(array('by_id' => $id, 'action' => 'one'));

        //If valid enterpirse and its element
        if ($element) {

            $parent = $element->getParent() ? $element->getParent()->getId() : '';

            if ($type == 'question') {
                //Function create new questionnaire
                $this->addQuestion($element, array('parent' => $parent, 'questionnaire' => $questionnaire));
            }

            //For duplicate description
            if ($type == 'description') {
                $this->addElement($element, array('parent' => $parent, 'questionnaire' => $questionnaire));
            }

            //For duplicate group 
            if ($type == 'groupe') {
                //Add element first
                $elem = $this->addElement($element, array('parent' => $parent, 'questionnaire' => $questionnaire));

                //If has child elements also
                if (count($element->getChildren()) > 0 && $elem) {
                    foreach ($element->getChildren() as $child) {
                        $childParams = array('parent' => $elem->getId(), 'pos' => $child->getPosition(), 'questionnaire' => $questionnaire);
                        //If child is description type element
                        if ($child->getStdQuestionnaireTypeElement()->getSlug() == 'description') {
                            $this->addElement($child, $childParams);
                        }
                        //If child is question type element
                        if ($child->getStdQuestionnaireTypeElement()->getSlug() == 'question') {
                            $this->addQuestion($child, $childParams);
                        }
                        //If child is group type element
                        if ($child->getStdQuestionnaireTypeElement()->getSlug() == 'groupe') {
                            $subelem = $this->addElement($child, $childParams);

                            //If subchild exists
                            if (count($child->getChildren()) > 0 && $subelem) {

                                foreach ($child->getChildren() as $subchild) {
                                    $subParams = array('parent' => $subelem->getId(), 'pos' => $subchild->getPosition(), 'questionnaire' => $questionnaire);

                                    //If subchild is description type element 
                                    if ($subchild->getStdQuestionnaireTypeElement()->getSlug() == 'description') {
                                        $this->addElement($subchild, $subParams);
                                    }
                                    //If subchild is question type element 
                                    if ($subchild->getStdQuestionnaireTypeElement()->getSlug() == 'question') {
                                        $this->addQuestion($subchild, $subParams);
                                    }

                                    //If subchild is question type element 
                                    if ($subchild->getStdQuestionnaireTypeElement()->getSlug() == 'groupe') {
                                        $this->addElem($subchild, $subParams);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($env == 'mo') {
            return $this->redirect($this->generateUrl($env . '_questionnaire_elements', array('slug_ent' => $enterprise['slug'], 'slug_quest' => $slug_quest)));
        } else {
            return $this->redirect($this->generateUrl($env . '_questionnaire_elements', array('slug_quest' => $slug_quest)));
        }
    }

    //Add element process
    public function addElement($element, $params = array()) {

        $em = $this->getDoctrine()->getManager();

        if (array_key_exists('questionnaire', $params) && $params['questionnaire'] == '') {
            $questionnaire_id = $element->getQuestionnaire()->getId();
            $questionnaire = $element->getQuestionnaire();
        } else {
            $questionnaire_id = $params['questionnaire'];
            $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_id' => $questionnaire_id, 'action' => 'one'));
        }

        //Getting position to be inserted element
        if ($params['parent'] == '') {
            //getting last position of root element                     
            $questElem = $this->get('questionnaire_element_repository')->getElements(array('max_count' => 'position', 'by_questionnaire_id' => $questionnaire_id, 'by_level' => 0, 'action' => 'one'));
            $position = $questElem['max_value'] + 1;
            $parentElem = NULL;
        } else {

            //getting parent element information
            $parentElem = $this->get('questionnaire_element_repository')->getElements(array('by_id' => $params['parent'], 'action' => 'one'));

            if (!isset($params['pos'])) {
                //getting last position of parent subchild
                $questElem = $this->get('questionnaire_element_repository')->getElements(array('max_count' => 'position', 'by_questionnaire_id' => $questionnaire_id, 'by_level' => $parentElem->getLevel() + 1, 'action' => 'one'));

                if (isset($questElem['max_value'])) {
                    $position = $questElem['max_value'] + 1;
                } else {
                    $position = 1;
                }
            } else {
                //If child element given
                $position = $params['pos'];
            }
        }

        $questionElemObj = new QuestionnaireElement();

        //setting question element information
        $questionElemObj->setQuestionnaire($questionnaire);
        $questionElemObj->setParent($parentElem);
        $questionElemObj->setStdQuestionnaireTypeElement($element->getStdQuestionnaireTypeElement());
        $questionElemObj->setName($element->getName());
        $questionElemObj->setTextDescription($element->getTextDescription());
        $questionElemObj->setTimeLimit($element->getTimeLimit());
        $questionElemObj->setTag($element->getTag());
        $questionElemObj->setTimeLimit($element->getTimeLimit());
        $questionElemObj->setPosition($position);

        $em->persist($questionElemObj);
        $em->flush();

        return $questionElemObj;
    }

    //Adding duplicate question
    public function addQuestion($element, $params) {
        $em = $this->getDoctrine()->getManager();
        $question = $element->getQuestion();

        $questionObj = new Question();
        //Function adds element
        $questionElemObj = $this->addElement($element, $params);

        //Set question information
        if ($questionElemObj->getId()) {
            $questionObj->setStdQuestionType($question->getStdQuestionType());
            $questionObj->setHelp(NULL);
            $questionObj->setRankin($question->getRankin());
            $questionObj->setOptions($question->getOptions());
            $questionObj->setQuestionTime($question->getQuestionTime());
            $questionObj->setResponseTime($question->getResponseTime());
            $questionObj->setIsConditional($question->getIsConditional());
            $questionObj->setEliminateQuestion($question->getEliminateQuestion());
            $questionObj->setAntiPlagiat($question->getAntiPlagiat());
            $questionObj->setQuestionnaireElement($questionElemObj);
            $questionObj->setNeeded($question->getNeeded());
            $questionObj->setCharLimit($question->getCharLimit());

            $em->persist($questionObj);
            $em->flush();

            //If question inserted
            if ($questionObj->getId()) {

                //If has choices
                if (count($question->getQuestionnaireQuestionChoice()) > 0) {
                    //getting all info for choices and adding choices for new question
                    foreach ($question->getQuestionnaireQuestionChoice() as $choix) {
                        $objChoice = new QuestionnaireQuestionChoice();
                        $objChoice->setName($choix->getName());
                        $objChoice->setQuestion($questionObj);
                        $objChoice->setPosition($choix->getPosition());
                        $objChoice->setRanking($choix->getRanking());
                        $objChoice->setGoodResponse($choix->getGoodResponse());

                        $em->persist($objChoice);
                        $em->flush();
                    }
                }
            }
        }
    }

}

?>