<?php

namespace VM\QuestionnaireBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VM\QuestionnaireBundle\Entity\QuestionnaireUser;
use VM\QuestionnaireBundle\Form\QuestionnaireUserType;
use VM\QuestionnaireBundle\Entity\QuestionnaireQuestionResponse;
use VM\GeneralBundle\Twig\VMExtension;

class QuestionnaireUserController extends Controller {
    /*     * *
     * This is a component to display Questionnaire's respondants in list by parameters
     * @param array $params
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */

    public function moRespondantsListAction($params = array()) {

        $list = $this->get("list_model");

        $baseArray = array(
            'template' => 'VMQuestionnaireBundle:Middle:_list_respondants.html.twig',
            'paramsQ' => array('order_by' => array('field' => 'id', 'sort' => 'asc'))
        );
        $list->defineBase($baseArray);
        $list->passedParams($params);
        $list->setQuery($this->get('questionnaire_user_repository')->getElements($list->getQueryParams()));
        return $list->getReturn();
    }

    //Front side questionnaire show page
    public function foQuestionnaireShowAction($slug_quest) {
        $session = $this->getRequest()->getSession();
        $session->set('email', '');
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug' => $slug_quest, 'action' => 'one'));
        $administrator = 0;

        //checking for administrator user
        $access = $this->get('session')->get('access_admin');
        if (!empty($access) && array_key_exists('current', $access)) {
            $administrator = 1;
        }

        //if Administrator not logged in or guest user
        if ($administrator == 0) {
            if ($questionnaire->getApprobation() != 1 || $questionnaire->getPublished() != 1) {
                return $this->redirect($this->generateUrl('fo_homepage'));
            }
        }

        return $this->render('VMQuestionnaireBundle:Front:questionnaire_show.html.twig', array('questionnaire' => $questionnaire));
    }

    //function to login Front side for giving test
    public function foQuestionnaireLoginAction($slug_quest) {
        $request = $this->get('request');
        $session = $this->getRequest()->getSession();
        $administrator = 0;

        $object = new QuestionnaireUser();
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug' => $slug_quest, 'action' => 'one'));

        $formConf = $this->get('form_model');
        $formConf->setView('VMQuestionnaireBundle:Front:questionnaire_login.html.twig');
        $formConf->setH1($this->get('translator')->trans('questionnaire.title.personnalInformations', array(), 'front'));
        $formConf->setElement('questionnaire_login');
        $formConf->setUrlParams(array('slug_quest' => $slug_quest));
        $formConf->setForm(new QuestionnaireUserType($this->getUser(), $questionnaire), $object);
        if ($this->getUser()) {
            //checking for administrator user
            $access = $this->get('session')->get('access_admin');
            if (!empty($access) && array_key_exists('current', $access)) {
                $administrator = 1;
            }
        }


        if ($request->getMethod() == 'POST') {

            // Make Validation Part
            $params = array();

            $em = $this->getDoctrine()->getManager();
            $obj = $formConf->getObject();
            $form = $formConf->getForm();
            $params['data'] = $request->get('questionnaireUser');


            if ($form->isValid()) {

                $questionnaire_user = $this->get('questionnaire_user_repository')->getElements(array('by_questionnaire' => $questionnaire->getId(), 'by_email_user' => $params['data']['email'], 'action' => 'one'));
                $em = $this->getDoctrine()->getManager();

                if ($administrator && $questionnaire_user) {
                    $questionnaire_user->setCurrentPosition(1);
                    $questionnaire_user->setScore(NULL);
                    $questionnaire_user->setComments(NULL);
                    $response = $this->get('questionnaire_question_response_repository')->removeElements(array('by_repondant_id' => $questionnaire_user->getId()));
                    $em->persist($questionnaire_user);
                    $em->flush();
                }
                if (!$questionnaire_user) {
                    $obj->setEmail($params['data']['email']);
                    if (!$questionnaire->getAnonymous()) {
                        $obj->setFirstname($params['data']['first_name']);
                        $obj->setLastname($params['data']['last_name']);
                    }
                    $obj->setPhoneNumber($params['data']['phone_number']);
                    $obj->setQuestionnaire($questionnaire);

                    //If administrator user test questionnaire
                    if ($administrator == 1) {
                        $obj->setStatus(3);
                    }

                    $obj->setCurrentPosition(1);
                    $obj->setAsFinished(0);
                    if ($this->getUser()) {
                        $obj->setUser($this->getUser());
                    }
                    $em->persist($obj);
                    $em->flush();
                }

                $session->set('email', $params['data']['email']);
                return $this->redirect($this->generateUrl('fo_questionnaire_process', array('slug_quest' => $slug_quest)));
            } else {
                $params['errors'] = $form->getErrors();
            }

            //if errors occuring
            if (isset($params['errors'])) {
                $formConf->setErrors($params['errors']);
            }
        }
        return $this->render($formConf->getTemplate(), $formConf->getParams());
    }

    //Front side success page after giving all responses
    public function foQuestionnaireSuccessAction($slug_quest) {
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug' => $slug_quest, 'action' => 'one'));
        if ($questionnaire) {
            return $this->render('VMQuestionnaireBundle:Front:questionnaire_success.html.twig', array('questionnaire' => $questionnaire, 'enterprise' => $questionnaire->getEnterprise()));
        }
    }

    //Returns form errors on FO side process
    public function getQuestionnaireErrors($params = array()) {
        $errors = NULL;
        $values = NULL;
        $needed = NULL;

        //for webcam part
        if ($params['webcam'] && count($params['webcam']) > 0) {
            foreach ($params['webcam'] as $key => $mq) {
                $question = $this->get('question_repository')->getElements(array('by_id' => $key, 'action' => 'one'));
                $needed = $question->getNeeded();

                if ($mq == '') {
                    $errors[$question->getId()] = "File required";
                }
            }
        }

        //For media type section            
        if ($params['media_question'] && count($params['media_question']) > 0) {
            foreach ($params['media_question'] as $key => $mq) {

                $question = $this->get('question_repository')->getElements(array('by_id' => $key, 'action' => 'one'));
                $needed = $question->getNeeded();

                $doc_format = $question->getOptions()['MediaType']['format'];
                if ($needed == 1 && $mq == '') {
                    $errors[$question->getId()] = "File required";
                } else {
                    if ($mq != '') {
                        //checks uploded document maches with format or not 
                        $valid_doc = $this->validateUploadedDocumentForResponse($mq, $doc_format);

                        //If not validated
                        if ($valid_doc == 0) {
                            $errors[$question->getId()] = "File not valid";
                        }
                    }
                }
            }
        }


        //If open type question response given
        if ($params['open_question'] && !empty($params['open_question'])) {
            foreach ($params['open_question'] as $key => $oq) {
                $question = $this->get('question_repository')->getElements(array('by_id' => $key, 'action' => 'one'));
                $values[$question->getId()] = $oq;
                $needed = $question->getNeeded();
                if ($question->getCharLimit() && strlen($oq) > $question->getCharLimit()) {
                    $errors[$question->getId()] = 'Limite de caractère : ' . $question->getCharLimit();
                }

                if (strlen($oq) == 0) {
                    if ($needed)
                        $errors[$question->getId()] = 'Ne dois pas etre vide';
                }
            }
        }

        //Checking for unique choice type question if all questions are good or not
        if ($params['unique_choices'] && !empty($params['unique_choices'])) {
            foreach ($params['unique_choices'] as $quest_id) {
                $question = $this->get('question_repository')->getElements(array('by_id' => $quest_id, 'action' => 'one'));
                $needed = $question->getNeeded();

                if (!$params['unique_choice_question'] && empty($params['unique_choice_question']) || (!isset($params['unique_choice_question'][$quest_id]))) {
                    if ($needed)
                        $errors[$quest_id] = 'Ne dois pas etre vide';
                }else {
                    if (isset($params['unique_choice_question'][$quest_id]))
                        $values[$question->getId()] = $params['unique_choice_question'][$quest_id];
                }
            }
        }


        //Checking for multi choice type question if all questions are good or not
        if ($params['multi_choices'] && !empty($params['multi_choices'])) {
            foreach ($params['multi_choices'] as $quest_id) {
                $question = $this->get('question_repository')->getElements(array('by_id' => $quest_id, 'action' => 'one'));
                $responses_question = $this->get('question_repository')->getElements(array('by_id' => $quest_id, 'action' => 'one'));
                $needed = $question->getNeeded();

                if (!$params['multi_choice_question'] && empty($params['multi_choice_question']) || (!isset($params['multi_choice_question'][$quest_id]))) {
                    if ($needed)
                        $errors[$quest_id] = 'Ne dois pas etre vide';
                }else {

                    if (isset($params['multi_choice_question'][$quest_id]) && count($params['multi_choice_question'][$quest_id]) > 0) {
                        foreach ($params['multi_choice_question'][$quest_id] as $mcq) {
                            $values[$question->getId()][] = $mcq;
                        }
                    }
                }
            }
        }


        //If open type question response given
        if ($params['evaluation_question'] && !empty($params['evaluation_question'])) {
            foreach ($params['evaluation_question'] as $key => $evq) {
                $question = $this->get('question_repository')->getElements(array('by_id' => $key, 'action' => 'one'));
                $values[$question->getId()] = $evq;
                $needed = $question->getNeeded();

                if ($evq == 0) {
                    if ($needed)
                        $errors[$question->getId()] = 'Ne dois pas etre vide';
                }
            }
        }


        //If  dateime type question response given
        if ($params['datetime'] && !empty($params['datetime'])) {
            foreach ($params['datetime'] as $key => $dt) {
                $question = $this->get('question_repository')->getElements(array('by_id' => $key, 'action' => 'one'));
                if ($question->getNeeded() == 1) {
                    //validate date type data 
                    $validDate = $this->validateDateTime($dt, $question);
                    //if any field is emapty
                    if ($validDate == 1) {
                        $errors[$question->getId()] = 'All fields are required';
                    }
                }
            }
        }

        return array('errors' => $errors, 'values' => $values);
    }

    /**
     *  Optimized questionaire answer function with different question types save action
     * @param type array 
     */
    public function saveQuestionnaireAnswersNew($params = array()) {
        $em = $this->getDoctrine()->getManager(); // Get object of docrine manager
        $questionnaire = $params['questionnaire']; // Get questionaire
        $questionnaire_user = $params['questionnaire_user']; // Get questionaire user
        $values = NULL;
        $idArray = array(); // Will assign submitted form question id
        $type = ''; // Will assign submitted form question type
        // Check if question type `media` & atleat single item
        if (array_key_exists('media_question', $params) && count($params['media_question'])) {
            $idArray = array_keys($params['media_question']);
            $type = 'media_question';

            // Check if question type `open` & atleat single item   
        } else if (array_key_exists('open_question', $params) && count($params['open_question'])) {
            $idArray = array_keys($params['open_question']);
            $type = 'open_question';

            // Check if question type `unique` & atleat single item
        } else if (array_key_exists('unique_choices', $params) && count($params['unique_choices'])) {
            $idArray = array_values($params['unique_choices']);
            $type = 'unique_choices';

            // Check if question type `multi` & atleat single item
        } else if (array_key_exists('multi_choices', $params) && count($params['multi_choices'])) {
            $idArray = array_values($params['multi_choices']);
            $type = 'multi_choices';

            // Check if question type `datetime` & atleat single item
        } else if (array_key_exists('datetime', $params) && count($params['datetime'])) {
            $idArray = array_keys($params['datetime']);
            $type = 'datetime';

            // Check if question type `evaluation` & atleat single item
        } else if (array_key_exists('evaluation_question', $params) && count($params['evaluation_question'])) {
            $idArray = array_keys($params['evaluation_question']);
            $type = 'evaluation_question';

            // Check if question type `webcam` & atleat single item
        } else if (array_key_exists('webcam', $params) && count($params['webcam'])) {
            $idArray = array_keys($params['webcam']);
            $type = 'webcam';
        }

        // Check if submitted question type is valid
        if ($type != '') {
            // Get all question by pass Id Array
            $question = $this->get('question_repository')->getElements(array('by_id_in' => $idArray));
            // Check question data exists
            if ($question) {
                foreach ($question as $questionObj) {
                    // Check submit question type except (`unique` & `multi`)
                    if (!in_array($type, array('datetime', 'evaluation_question', 'webcam'))) {
                        // Get dividend of score  
                        $userScore = (is_array($questionnaire_user->getScore()) && array_key_exists('auto', $questionnaire_user->getScore())) ? $questionnaire_user->getScore()['auto'] : 0; // Get `auto` score if exists
                        $userDividend = (is_array($questionnaire_user->getScore()) && array_key_exists('dividend', $questionnaire_user->getScore())) ? $questionnaire_user->getScore()['dividend'] + ($questionObj->getRankin() * 5) : $questionObj->getRankin() * 5;
                        // Check submit question type is `media`
                        if ($type == 'media_question') {
                            $questionnaire_user->setScore(($questionnaire_user->getScore() ? $questionnaire_user->getScore() + array('dividend' => $userDividend) : array('dividend' => $userDividend))); // Concat 2 different array but not perform any arithmatic equation 
                        } else {
                            // Check questionaire user have already score then merge with `dividend` otherwise set only `dividend`
                            $questionnaire_user->setScore(($questionnaire_user->getScore() ? array_merge($questionnaire_user->getScore(), array('dividend' => $userDividend)) : array('dividend' => $userDividend))); // Merge 2 different array as well value
                        }
                        $em->persist($questionnaire_user);
                        $em->flush();
                    }

                    // Check submit question type except (`unique` & `multi`)
                    if (!in_array($type, array('unique_choices', 'multi_choices'))) {
                        $question_resp_obj = new QuestionnaireQuestionResponse(); // Get Instance of question response entity
                        $question_resp_obj->setQuestionnaireUser($questionnaire_user); // Set questionaire user object
                        $question_resp_obj->setQuestion($questionObj); // Set question object                    
                    }
                    // Pass appropriate submitted question type
                    switch ($type) {
                        case 'media_question': // Case question type `media`
                            //If files are not empty
                            if ($params[$type][$questionObj->getId()] != '') {
                                $doc_format = $questionObj->getOptions()['MediaType']['format']; // Allowed media file type
                                //check for valid document
                                $valid_docs = $this->validateUploadedDocumentForResponse($params[$type][$questionObj->getId()], $doc_format);
                                if ($valid_docs == 1) {
                                    $file_info = pathinfo($params[$type][$questionObj->getId()]->getClientOriginalName()); // Get uploaded file path
                                    $extension = $file_info['extension']; // Get file extension
                                    $filename = $file_info['filename']; // Get file filename
                                    $extensionObj = new VMExtension(); // Assign instanse of class
                                    $file = $questionnaire->getId() . '_' . $questionObj->getId() . '_' . $extensionObj->slugify($filename) . '.' . $extension; // Generate dynamic uploaded file name
                                    //function uploads file in specified path
                                    $this->uploadMediaDocument($file, $params[$type][$questionObj->getId()], $questionnaire_user);
                                    //For user which are registered
                                    if ($questionnaire_user->getUser())
                                        $enclosed_file_path = '/uploads/users/' . $questionnaire_user->getUser()->getId() . '/responses';  // Dynamic file path                                  


                                        
//For user which are not registered    
                                    else
                                        $enclosed_file_path = '/uploads/users/guest/' . $questionnaire_user->getId() . '/responses';
                                    $enclosed_file_withpath = $enclosed_file_path . '/' . $file; // Associate uploaded dynamic file name & path
                                    $question_resp_obj->setEnclosedFiles($enclosed_file_withpath); // Set dynamic file path
                                } else {
                                    $question_resp_obj->setEnclosedFiles(NULL); // Set `Null` if there have no files 
                                }
                            }
                            break;
                        case 'open_question': // Case question type `open`
                            $question_resp_obj->setResponse(mysql_real_escape_string($params[$type][$questionObj->getId()])); // Set escape response content
                            break;
                        case 'unique_choices': // Case question type `unique`
                            // Check if question type is `unique` & associated with valid question id
                            if (array_key_exists('unique_choice_question', $params) && !empty($params['unique_choice_question'][$questionObj->getId()])) {
                                // Get selected unique question choice data by choice id
                                $questionChoice = $this->get('questionnaire_question_choice_repository')->getElements(array('by_id' => $params['unique_choice_question'][$questionObj->getId()], 'action' => 'one'));
                                $uniqChoiceQuestObj = new QuestionnaireQuestionResponse(); // Assign instance of class 
                                $uniqChoiceQuestObj->setQuestionnaireUser($questionnaire_user); // Set object of questionaire user entity
                                $uniqChoiceQuestObj->setQuestion($questionObj); // Set object of question entity
                                // Check unique question choice type
                                if ($questionObj->getOptions()['ChoiceType'] == 4) {
                                    $uniqChoiceQuestObj->setQuestionMark(array('auto' => ($questionChoice->getGoodResponse() ? 5 : 0))); // Set 5 if good response otherwise 0
                                    $userScore += 5 * $questionObj->getRankin(); // Calculate repondant auto score according to given rankin
                                    // Check if unique choice ranking is not null    
                                } else if (!is_null($questionChoice->getRanking())) {
                                    $userScore += $questionChoice->getRanking() * $questionObj->getRankin(); // Calculate repondant auto score according to given rankin
                                    $uniqChoiceQuestObj->setQuestionMark(array('auto' => $questionChoice->getRanking())); // Set score on given question answer
                                }
                                $uniqChoiceQuestObj->setQuestionnaireQuestionChoice($questionChoice); // Set object of question choice entity
                                $values[$questionObj->getId()] = $params['unique_choice_question'][$questionObj->getId()];
                                $em->persist($uniqChoiceQuestObj);
                                $em->flush();
                            } else {
                                $uniqChoiceQuestObj = new QuestionnaireQuestionResponse(); // Assign instance of class 
                                $uniqChoiceQuestObj->setQuestionnaireUser($questionnaire_user); // Set object of questionaire user entity
                                $uniqChoiceQuestObj->setQuestion($questionObj); // Set object of question entity
                                $em->persist($uniqChoiceQuestObj);
                                $em->flush();
                            }
                            $questionnaire_user->setScore(array_merge($questionnaire_user->getScore(), array('auto' => $userScore))); // Set Calculated auto score in questionaire user 
                            $em->persist($questionnaire_user);
                            $em->flush();
                            break;
                        case 'multi_choices': // Case question type `multi`
                            $invalid = 0; // Set default value
                            // Check if question type is `multi` & associated with valid question id
                            if (array_key_exists('multi_choice_question', $params) && !empty($params['multi_choice_question'][$questionObj->getId()])) {
                                foreach ($params['multi_choice_question'][$questionObj->getId()] as $mcq) {
                                    // Check if multi question type is `qcm multiple`
                                    if ($questionObj->getOptions()['ChoiceType'] == 3) {
                                        foreach ($questionObj->getQuestionnaireQuestionChoice() as $choice) {
                                            if ($choice->getGoodResponse() == 0 && $mcq == $choice->getId()) {
                                                $invalid = 1;
                                            }
                                        }
                                    }
                                }
                                $auto = 0; // Set default value
                                $responseArr = array(); // Initilize variable as an array
                                $is_null = 1; // Set default value
                                foreach ($params['multi_choice_question'][$questionObj->getId()] as $mcq) {
                                    // Get data of selected qcm multiple question choice by id 
                                    $questionMultiChoice = $this->get('questionnaire_question_choice_repository')->getElements(array('by_id' => $mcq, 'action' => 'one'));
                                    $multiChoiceQuestObj = new QuestionnaireQuestionResponse(); // Assign instance of class 
                                    // Check if question choice type is qcm multiple & selcted wrong option
                                    if ($invalid == 1) {
                                        $multiChoiceQuestObj->setQuestionMark(array('auto' => 0)); // Set question mark auto value 0
                                        // Check if question choice type is qcm multiple & selcted right option  
                                    } elseif ($questionObj->getOptions()['ChoiceType'] == 3) {
                                        $multiChoiceQuestObj->setQuestionMark(array('auto' => 5)); // Set question mark auto value 5
                                    }
                                    $multiChoiceQuestObj->setQuestionnaireUser($questionnaire_user); // Set object of questionaire user entity
                                    $multiChoiceQuestObj->setQuestion($questionObj); // Set object of question entity
                                    $multiChoiceQuestObj->setQuestionnaireQuestionChoice($questionMultiChoice); // Set object of question choice entity
                                    $em->persist($multiChoiceQuestObj);
                                    $em->flush();
                                    // Check question choice type is `choice multiple`
                                    if ($questionObj->getOptions()['ChoiceType'] == 2) {
                                        $auto+=$questionMultiChoice->getRanking(); // Calculate ranking of right choices
                                        $responseArr[] = $multiChoiceQuestObj->getId(); // Store valid question response id in array
                                    }
                                }
                                // Check question choice type is `choice multiple` & ranking should not null 
                                if ($questionObj->getOptions()['ChoiceType'] == 2 && $auto && count($responseArr)) {
                                    // Check if question type `choice multiple` & no `total_ranking`
                                    if (!$questionObj->getOptions()['total_ranking'])
                                        $total_rankin = 5; // Set default value 5
                                    else
                                        $total_rankin = $questionObj->getOptions()['total_ranking']; // Get total_ranking value
                                    foreach ($responseArr as $respond) {
                                        $varr = number_format(($auto * 5) / $total_rankin, 2); // Calculate auto value
                                        // Check $varr value is greater than 5
                                        if ($varr > 5)
                                            $varr = 5; // Then assign 5 as max auto value


                                            
// Get questionaire question response object by id
                                        $respondObj = $questionMultiChoice = $this->get('questionnaire_question_response_repository')->getElements(array('by_id' => $respond, 'action' => 'one'));
                                        $respondObj->setQuestionMark(array('auto' => $varr)); // Set question_mark auto value
                                    }
                                    $userScore += $varr * $questionObj->getRankin(); // Multiply individual choice auto value & question ranking
                                }
                                // Check if question choice type is not (`unique` & `multiple`)
                                if ($questionObj->getOptions()['ChoiceType'] != 1 && $questionObj->getOptions()['ChoiceType'] != 2) {
                                    if ($invalid != 1) {
                                        $userScore = $userScore + 5 * $questionObj->getRankin(); // Calculate questionaire user score + (5  * individual question rankin)
                                    }
                                }
                                $questionnaire_user->setScore(array_merge($questionnaire_user->getScore(), array('auto' => $userScore))); // Merge existing questionaire user score & auto score
                                $em->persist($questionnaire_user);
                                $em->flush();
                            } else {// Not given answer 
                                $multiChoiceQuestObj = new QuestionnaireQuestionResponse(); // Assign instance of class 
                                $multiChoiceQuestObj->setQuestionnaireUser($questionnaire_user); // Set object of questionaire user entity
                                $multiChoiceQuestObj->setQuestion($questionObj); // Set object of question entity
                                $em->persist($multiChoiceQuestObj);
                                $em->flush();
                            }
                            break;
                        case 'datetime': // Case question type `datetime`
                            // Get question date type is simple or range 
                            $datetimeType = $questionObj->getOptions()['DatetimeType'];
                            $dt = $params['datetime'][$questionObj->getId()]; // Get submitted datetime data
                            //adding 0 before month and day if less then 2 digit
                            if (isset($dt['start_date']['month'])) {
                                $smonth = sprintf('%1$02d', $dt['start_date']['month']);
                            }
                            if (isset($dt['start_date']['day'])) {
                                $sday = sprintf('%1$02d', $dt['start_date']['day']);
                            }
                            if (isset($dt['start_date']['hour']) && ($dt['start_date']['hour'] != 'hr')) {
                                $shour = sprintf('%1$02d', $dt['start_date']['hour']);
                            }
                            if (isset($dt['start_date']['minute']) && ($dt['start_date']['minute'] != 'min')) {
                                $sminute = sprintf('%1$02d', $dt['start_date']['hour']);
                            }

                            if (isset($dt['start_date']['year'])) {
                                $syear = sprintf('%1$04d', $dt['start_date']['year']);
                            }

                            //If range date time type question
                            if ($datetimeType[0] == 2) {
                                if (isset($dt['end_date']['month'])) {
                                    $emonth = sprintf('%1$02d', $dt['end_date']['month']);
                                }
                                if (isset($dt['end_date']['day'])) {
                                    $eday = sprintf('%1$02d', $dt['end_date']['day']);
                                }
                                if (isset($dt['end_date']['hour']) && ($dt['end_date']['hour'] != 'hr')) {
                                    $ehour = sprintf('%1$02d', $dt['end_date']['hour']);
                                }
                                if (isset($dt['end_date']['minute']) && ($dt['end_date']['minute'] != 'min')) {
                                    $eminute = sprintf('%1$02d', $dt['end_date']['minute']);
                                }
                                if (isset($dt['end_date']['year'])) {
                                    $eyear = sprintf('%1$04d', $dt['end_date']['year']);
                                }
                            }

                            //If format is defined for range or simple datetime                  
                            if (isset($questionObj->getOptions()['DatetimeType']) and isset($datetimeType['format'])) {
                                // If Format is d-m-y
                                if ($datetimeType['format'] == 'd-m-y') {
                                    if (!$dt['start_date']['day'] && !$dt['start_date']['month'] && !$dt['start_date']['year'])
                                        $startdatetime = NULL;
                                    else
                                        $startdatetime = $sday . '-' . $smonth . '-' . $syear;
                                    //If range date time type question
                                    if ($datetimeType[0] == 2) {
                                        if (!$dt['end_date']['day'] && !$dt['end_date']['month'] && !$dt['end_date']['year'])
                                            $enddatetime = NULL;
                                        else
                                            $enddatetime = $eday . '-' . $emonth . '-' . $eyear;
                                    }
                                }
                                // If Format is m-y
                                if ($datetimeType['format'] == 'm-y') {
                                    if (!$dt['start_date']['month'] && !$dt['start_date']['year'])
                                        $startdatetime = NULL;
                                    else
                                        $startdatetime = $smonth . '-' . $syear;

                                    //If range date time type question
                                    if ($datetimeType[0] == 2) {
                                        if (!$dt['start_date']['month'] && !$dt['start_date']['year'])
                                            $enddatetime = NULL;
                                        else
                                            $enddatetime = $emonth . '-' . $eyear;
                                    }
                                }
                                // If Format is y
                                if ($datetimeType['format'] == 'y') {
                                    if (!$dt['start_date']['year'])
                                        $startdatetime = NULL;
                                    else
                                        $startdatetime = $syear;

                                    //If range date time type question
                                    if ($datetimeType[0] == 2) {
                                        if (!$dt['end_date']['year'])
                                            $enddatetime = NULL;
                                        else
                                            $enddatetime = $syear;;
                                    }
                                }
                                // If Format is m-y
                                if ($datetimeType['format'] == 'H:i') {

                                    if ($dt['start_date']['hour'] == 'hr' || $dt['start_date']['minute'] == 'min' || !$dt['start_date']['hour'] || !$dt['start_date']['minute'])
                                        $startdatetime = NULL;
                                    else
                                        $startdatetime = $shour . ':' . $sminute;
                                    //If range date time type question
                                    if ($datetimeType[0] == 2) {
                                        if ($dt['end_date']['hour'] == 'hr' || $dt['end_date']['minute'] == 'min' || !$dt['end_date']['hour'] || !$dt['end_date']['minute'])
                                            $enddatetime = NULL;
                                        else
                                            $enddatetime = $ehour . ':' . $ehour;
                                    }
                                }
                            } else {

                                if (!$dt['start_date']['day'] && !$dt['start_date']['month'] && !$dt['start_date']['year'] && ( $dt['start_date']['hour'] == 'hr' || $dt['start_date']['minute'] == 'min' || !$dt['start_date']['hour'] || !$dt['start_date']['minute']))
                                    $startdatetime = NULL;
                                else
                                    $startdatetime = $syear . '-' . $smonth . '-' . $sday . ' ' . $shour . ':' . $sminute;

                                //If range date time type question
                                if ($datetimeType[0] == 2) {
                                    if (!$dt['start_date']['day'] && !$dt['start_date']['month'] && !$dt['start_date']['year'] && ( $dt['start_date']['hour'] == 'hr' || $dt['start_date']['minute'] == 'min' || !$dt['end_date']['hour'] || !$dt['end_date']['minute']))
                                        $enddatetime = NULL;
                                    else
                                        $enddatetime = $eyear . '-' . $emonth . '-' . $eday . ' ' . $ehour . ':' . $eminute;
                                }
                            }
                            $question_resp_obj->setStartDate($startdatetime); // Set start date in questionaire response
                            //If range date time type question
                            if ($questionObj->getOptions()['DatetimeType'][0] == 2) {
                                $question_resp_obj->setEndDate($enddatetime); // Set end date in questionaire response
                            }
                            break;
                        case 'evaluation_question': // Case question type `evaluation`
                            $question_resp_obj->setResponse($params['evaluation_question'][$questionObj->getId()]); // Set evaluation content in questionaire response
                            break;
                        case 'webcam': // Case question type `webcam`
                            $webQuestObj->setEnclosedFiles($params['webcam'][$questionObj->getId()]); // Set enclosed webcam files
                            break;
                    }
                    // Check submit question type except (`unique` & `multi`)
                    if (!in_array($type, array('unique_choices', 'multi_choices'))) {
                        $em->persist($question_resp_obj);
                        $em->flush();
                    }
                }
            }
        }
    }

    //Saving user's answers of questionnaire questions

    public function saveQuestionnaireAnswers($params = array()) {

        $em = $this->getDoctrine()->getManager();
        $questionnaire = $params['questionnaire'];
        $questionnaire_user = $params['questionnaire_user'];
        $values = NULL;

        //for media type files
        if ($params['media_question'] && count($params['media_question']) > 0) {
            foreach ($params['media_question'] as $key => $oq) {
                $question = $this->get('question_repository')->getElements(array('by_id' => $key, 'action' => 'one'));
                $userDividend = (is_array($questionnaire_user->getScore()) && array_key_exists('dividend', $questionnaire_user->getScore())) ? $questionnaire_user->getScore()['dividend'] + ($question->getRankin() * 5) : $question->getRankin() * 5;
                if ($questionnaire_user->getScore()) {
                    $questionnaire_user->setScore(array_merge($questionnaire_user->getScore(), array('dividend' => $userDividend)));
                } else {
                    $questionnaire_user->setScore(array('dividend' => $userDividend));
                }
                $em->persist($questionnaire_user);
                $em->flush();
                $mediaQuestObj = new QuestionnaireQuestionResponse();
                $mediaQuestObj->setQuestionnaireUser($questionnaire_user);
                $mediaQuestObj->setQuestion($question);

                //If files is not empty
                if ($oq != '') {

                    $doc_format = $question->getOptions()['MediaType']['format'];

                    //check for valid document
                    $valid_docs = $this->validateUploadedDocumentForResponse($oq, $doc_format);

                    if ($valid_docs == 1) {
                        $file_info = pathinfo($oq->getClientOriginalName());

                        $extension = $file_info['extension'];
                        $filename = $file_info['filename'];

                        $extensionObj = new VMExtension();

                        $file = $questionnaire->getId() . '_' . $key . '_' . $extensionObj->slugify($filename) . '.' . $extension;

                        //function uploads file in specified path
                        $this->uploadMediaDocument($file, $oq, $questionnaire_user);

                        //For user which are registered
                        if ($questionnaire_user->getUser()) {
                            $enclosed_file_path = '/uploads/users/' . $questionnaire_user->getUser()->getId() . '/responses';
                        } else {
                            //For user which are not registered
                            $enclosed_file_path = '/uploads/users/guest/' . $questionnaire_user->getId() . '/responses';
                        }

                        $enclosed_file_withpath = $enclosed_file_path . '/' . $file;
                        $mediaQuestObj->setEnclosedFiles($enclosed_file_withpath);
                    } else {
                        $mediaQuestObj->setEnclosedFiles(NULL);
                    }
                }

                $em->persist($mediaQuestObj);
                $em->flush();
            }
        }


        if ($params['open_question'] && !empty($params['open_question'])) {
            foreach ($params['open_question'] as $key => $oq) {
                $question = $this->get('question_repository')->getElements(array('by_id' => $key, 'action' => 'one'));

                $userDividend = (is_array($questionnaire_user->getScore()) && array_key_exists('dividend', $questionnaire_user->getScore())) ? $questionnaire_user->getScore()['dividend'] + ($question->getRankin() * 5) : $question->getRankin() * 5;

                if ($questionnaire_user->getScore()) {
                    $questionnaire_user->setScore(array_merge($questionnaire_user->getScore(), array('dividend' => $userDividend)));
                } else {
                    $questionnaire_user->setScore(array('dividend' => $userDividend));
                }

                $em->persist($questionnaire_user);
                $em->flush();

                $openQuestObj = new QuestionnaireQuestionResponse();
                $openQuestObj->setQuestionnaireUser($questionnaire_user);
                $openQuestObj->setQuestion($question);
                $openQuestObj->setResponse(mysql_real_escape_string($oq));
                $em->persist($openQuestObj);
                $em->flush();
            }
        }
        //If unique choice type question response given
        if ($params['unique_choices'] && !empty($params['unique_choices'])) {
            foreach ($params['unique_choices'] as $quest_id) {
                $question = $this->get('question_repository')->getElements(array('by_id' => $quest_id, 'action' => 'one'));
                $userScore = (is_array($questionnaire_user->getScore()) && array_key_exists('auto', $questionnaire_user->getScore())) ? $questionnaire_user->getScore()['auto'] : 0;
                $userDividend = (is_array($questionnaire_user->getScore()) && array_key_exists('dividend', $questionnaire_user->getScore())) ? $questionnaire_user->getScore()['dividend'] + ($question->getRankin() * 5) : $question->getRankin() * 5;
                if ($questionnaire_user->getScore()) {
                    $questionnaire_user->setScore(array_merge($questionnaire_user->getScore(), array('dividend' => $userDividend)));
                } else {
                    $questionnaire_user->setScore(array('dividend' => $userDividend));
                }

                $em->persist($questionnaire_user);
                $em->flush();

                if ($params['unique_choice_question'] && !empty($params['unique_choice_question']) && isset($params['unique_choice_question'][$quest_id])) {
                    $questionChoice = $this->get('questionnaire_question_choice_repository')->getElements(array('by_id' => $params['unique_choice_question'][$quest_id], 'action' => 'one'));
                    $uniqChoiceQuestObj = new QuestionnaireQuestionResponse();
                    $uniqChoiceQuestObj->setQuestionnaireUser($questionnaire_user);
                    $uniqChoiceQuestObj->setQuestion($question);
                    if ($question->getOptions()['ChoiceType'] == 4) {
                        $uniqChoiceQuestObj->setQuestionMark(array('auto' => ($questionChoice->getGoodResponse() ? 5 : 0)));
                        $userScore += 5 * $question->getRankin();
                    } else if (!is_null($questionChoice->getRanking())) {
                        $userScore += $questionChoice->getRanking() * $question->getRankin();
                        $uniqChoiceQuestObj->setQuestionMark(array('auto' => $questionChoice->getRanking()));
                    }
                    $uniqChoiceQuestObj->setQuestionnaireQuestionChoice($questionChoice);
                    $values[$quest_id] = $params['unique_choice_question'][$quest_id];
                    $em->persist($uniqChoiceQuestObj);
                    $em->flush();
                } else {
                    $uniqChoiceQuestObj = new QuestionnaireQuestionResponse();
                    $uniqChoiceQuestObj->setQuestionnaireUser($questionnaire_user);
                    $uniqChoiceQuestObj->setQuestion($question);
                    $em->persist($uniqChoiceQuestObj);
                    $em->flush();
                }
            }
            $questionnaire_user->setScore(array_merge($questionnaire_user->getScore(), array('auto' => $userScore)));
            $em->persist($questionnaire_user);
            $em->flush();
        }

        //If multichoice or qcm type question answer given
        if ($params['multi_choices'] && !empty($params['multi_choices'])) {
            foreach ($params['multi_choices'] as $quest_id) {
                $question = $this->get('question_repository')->getElements(array('by_id' => $quest_id, 'action' => 'one'));

                $userScore = (is_array($questionnaire_user->getScore()) && array_key_exists('auto', $questionnaire_user->getScore())) ? $questionnaire_user->getScore()['auto'] : 0;
                $userDividend = (is_array($questionnaire_user->getScore()) && array_key_exists('dividend', $questionnaire_user->getScore())) ? $questionnaire_user->getScore()['dividend'] + ($question->getRankin() * 5) : $question->getRankin() * 5;

                if ($questionnaire_user->getScore()) {
                    $questionnaire_user->setScore(array_merge($questionnaire_user->getScore(), array('dividend' => $userDividend)));
                } else {
                    $questionnaire_user->setScore(array('dividend' => $userDividend));
                }
                $em->persist($questionnaire_user);
                $em->flush();

                $invalid = 0;
                if ($params['multi_choice_question'] && !empty($params['multi_choice_question']) && isset($params['multi_choice_question'][$quest_id])) {
                    foreach ($params['multi_choice_question'][$quest_id] as $mcq) {
                        if ($question->getOptions()['ChoiceType'] == 3) {
                            foreach ($question->getQuestionnaireQuestionChoice() as $choice) {
                                if ($choice->getGoodResponse() == 0 && $mcq == $choice->getId()) {
                                    $invalid = 1;
                                }
                            }
                        }
                    }
                    $auto = 0;
                    $responseArr = array();
                    $is_null = 1;
                    foreach ($params['multi_choice_question'][$quest_id] as $mcq) {
                        $questionMultiChoice = $this->get('questionnaire_question_choice_repository')->getElements(array('by_id' => $mcq, 'action' => 'one'));
                        $multiChoiceQuestObj = new QuestionnaireQuestionResponse();
                        if ($invalid == 1) {
                            $multiChoiceQuestObj->setQuestionMark(array('auto' => 0));
                        } elseif ($question->getOptions()['ChoiceType'] == 3) {
                            $multiChoiceQuestObj->setQuestionMark(array('auto' => 5));
                        }
                        $multiChoiceQuestObj->setQuestionnaireUser($questionnaire_user);
                        $multiChoiceQuestObj->setQuestion($question);
                        $multiChoiceQuestObj->setQuestionnaireQuestionChoice($questionMultiChoice);
                        $em->persist($multiChoiceQuestObj);
                        $em->flush();
                        if ($question->getOptions()['ChoiceType'] == 2) {
                            $auto+=$questionMultiChoice->getRanking();
                            $responseArr[] = $multiChoiceQuestObj->getId();
                        }
                    }
                    if ($question->getOptions()['ChoiceType'] == 2 && $auto && count($responseArr)) {
                        if (!$question->getOptions()['total_ranking'])
                            $total_rankin = 5;
                        else
                            $total_rankin = $question->getOptions()['total_ranking'];
                        foreach ($responseArr as $respond) {
                            $varr = number_format(($auto * 5) / $total_rankin, 2);
                            if ($varr > 5)
                                $varr = 5;
                            $respondObj = $questionMultiChoice = $this->get('questionnaire_question_response_repository')->getElements(array('by_id' => $respond, 'action' => 'one'));
                            $respondObj->setQuestionMark(array('auto' => $varr));
                        }
                        $userScore += $varr * $question->getRankin();
                    }
                    if ($question->getOptions()['ChoiceType'] != 1 && $question->getOptions()['ChoiceType'] != 2) {
                        if ($invalid != 1) {
                            $userScore = $userScore + 5 * $question->getRankin();
                        }
                    }
                    $questionnaire_user->setScore(array_merge($questionnaire_user->getScore(), array('auto' => $userScore)));
                    $em->persist($questionnaire_user);
                    $em->flush();
                } else {
                    $userDividend = (is_array($questionnaire_user->getScore()) && array_key_exists('dividend', $questionnaire_user->getScore())) ? $questionnaire_user->getScore()['dividend'] + ($question->getRankin() * 5) : $question->getRankin() * 5;

                    if ($questionnaire_user->getScore()) {
                        $questionnaire_user->setScore(array_merge($questionnaire_user->getScore(), array('dividend' => $userDividend)));
                    } else {
                        $questionnaire_user->setScore(array('dividend' => $userDividend));
                    }

                    $em->persist($questionnaire_user);
                    $em->flush();

                    $multiChoiceQuestObj = new QuestionnaireQuestionResponse();
                    $multiChoiceQuestObj->setQuestionnaireUser($questionnaire_user);
                    $multiChoiceQuestObj->setQuestion($question);
                    $em->persist($multiChoiceQuestObj);
                    $em->flush();
                }
            }
        }

        //If  dateime type question response given
        if ($params['datetime'] && !empty($params['datetime'])) {
            foreach ($params['datetime'] as $key => $dt) {
                $question = $this->get('question_repository')->getElements(array('by_id' => $key, 'action' => 'one'));
                $datetimeType = $question->getOptions()['DatetimeType'];

                $userDividend = (is_array($questionnaire_user->getScore()) && array_key_exists('dividend', $questionnaire_user->getScore())) ? $questionnaire_user->getScore()['dividend'] + ($question->getRankin() * 5) : $question->getRankin() * 5;

                if ($questionnaire_user->getScore()) {
                    $questionnaire_user->setScore(array_merge($questionnaire_user->getScore(), array('dividend' => $userDividend)));
                } else {
                    $questionnaire_user->setScore(array('dividend' => $userDividend));
                }

                $em->persist($questionnaire_user);
                $em->flush();


                //adding 0 before month and day if less then 2 digit
                if (isset($dt['start_date']['month'])) {
                    $smonth = sprintf('%1$02d', $dt['start_date']['month']);
                }
                if (isset($dt['start_date']['day'])) {
                    $sday = sprintf('%1$02d', $dt['start_date']['day']);
                }
                if (isset($dt['start_date']['hour']) && ($dt['start_date']['hour'] != 'hr')) {
                    $shour = sprintf('%1$02d', $dt['start_date']['hour']);
                }
                if (isset($dt['start_date']['minute']) && ($dt['start_date']['minute'] != 'min')) {
                    $sminute = sprintf('%1$02d', $dt['start_date']['hour']);
                }

                if (isset($dt['start_date']['year'])) {
                    $syear = sprintf('%1$04d', $dt['start_date']['year']);
                }

                //If range date time type question
                if ($datetimeType[0] == 2) {
                    if (isset($dt['end_date']['month'])) {
                        $emonth = sprintf('%1$02d', $dt['end_date']['month']);
                    }
                    if (isset($dt['end_date']['day'])) {
                        $eday = sprintf('%1$02d', $dt['end_date']['day']);
                    }
                    if (isset($dt['end_date']['hour']) && ($dt['end_date']['hour'] != 'hr')) {
                        $ehour = sprintf('%1$02d', $dt['end_date']['hour']);
                    }
                    if (isset($dt['end_date']['minute']) && ($dt['end_date']['minute'] != 'min')) {
                        $eminute = sprintf('%1$02d', $dt['end_date']['minute']);
                    }
                    if (isset($dt['end_date']['year'])) {
                        $eyear = sprintf('%1$04d', $dt['end_date']['year']);
                    }
                }

                //If format is defined for range or simple datetime                  
                if (isset($question->getOptions()['DatetimeType']) and isset($datetimeType['format'])) {
                    // If Format is d-m-y
                    if ($datetimeType['format'] == 'd-m-y') {
                        if (!$dt['start_date']['day'] && !$dt['start_date']['month'] && !$dt['start_date']['year'])
                            $startdatetime = NULL;
                        else
                            $startdatetime = $sday . '-' . $smonth . '-' . $syear;
                        //If range date time type question
                        if ($datetimeType[0] == 2) {
                            if (!$dt['end_date']['day'] && !$dt['end_date']['month'] && !$dt['end_date']['year'])
                                $enddatetime = NULL;
                            else
                                $enddatetime = $eday . '-' . $emonth . '-' . $eyear;
                        }
                    }

                    // If Format is m-y
                    if ($datetimeType['format'] == 'm-y') {
                        if (!$dt['start_date']['month'] && !$dt['start_date']['year'])
                            $startdatetime = NULL;
                        else
                            $startdatetime = $smonth . '-' . $syear;

                        //If range date time type question
                        if ($datetimeType[0] == 2) {
                            if (!$dt['start_date']['month'] && !$dt['start_date']['year'])
                                $enddatetime = NULL;
                            else
                                $enddatetime = $emonth . '-' . $eyear;
                        }
                    }

                    // If Format is y
                    if ($datetimeType['format'] == 'y') {
                        if (!$dt['start_date']['year'])
                            $startdatetime = NULL;
                        else
                            $startdatetime = $syear;

                        //If range date time type question
                        if ($datetimeType[0] == 2) {
                            if (!$dt['end_date']['year'])
                                $enddatetime = NULL;
                            else
                                $enddatetime = $syear;;
                        }
                    }

                    // If Format is m-y
                    if ($datetimeType['format'] == 'H:i') {

                        if ($dt['start_date']['hour'] == 'hr' || $dt['start_date']['minute'] == 'min' || !$dt['start_date']['hour'] || !$dt['start_date']['minute'])
                            $startdatetime = NULL;
                        else
                            $startdatetime = $shour . ':' . $sminute;
                        //If range date time type question
                        if ($datetimeType[0] == 2) {
                            if ($dt['end_date']['hour'] == 'hr' || $dt['end_date']['minute'] == 'min' || !$dt['end_date']['hour'] || !$dt['end_date']['minute'])
                                $enddatetime = NULL;
                            else
                                $enddatetime = $ehour . ':' . $ehour;
                        }
                    }
                } else {

                    if (!$dt['start_date']['day'] && !$dt['start_date']['month'] && !$dt['start_date']['year'] && ( $dt['start_date']['hour'] == 'hr' || $dt['start_date']['minute'] == 'min' || !$dt['start_date']['hour'] || !$dt['start_date']['minute']))
                        $startdatetime = NULL;
                    else
                        $startdatetime = $syear . '-' . $smonth . '-' . $sday . ' ' . $shour . ':' . $sminute;

                    //If range date time type question
                    if ($datetimeType[0] == 2) {
                        if (!$dt['start_date']['day'] && !$dt['start_date']['month'] && !$dt['start_date']['year'] && ( $dt['start_date']['hour'] == 'hr' || $dt['start_date']['minute'] == 'min' || !$dt['end_date']['hour'] || !$dt['end_date']['minute']))
                            $enddatetime = NULL;
                        else
                            $enddatetime = $eyear . '-' . $emonth . '-' . $eday . ' ' . $ehour . ':' . $eminute;
                    }
                }

                $dtQuestObj = new QuestionnaireQuestionResponse();
                $dtQuestObj->setQuestionnaireUser($questionnaire_user);
                $dtQuestObj->setQuestion($question);
                $dtQuestObj->setStartDate($startdatetime);

                //If range date time type question
                if ($question->getOptions()['DatetimeType'][0] == 2) {
                    $dtQuestObj->setEndDate($enddatetime);
                }

                $em->persist($dtQuestObj);
                $em->flush();
            }
        }

        //If evaluation type question is given            
        if ($params['evaluation_question'] && !empty($params['evaluation_question'])) {
            foreach ($params['evaluation_question'] as $key => $eq) {

                $question = $this->get('question_repository')->getElements(array('by_id' => $key, 'action' => 'one'));
                $userDividend = (is_array($questionnaire_user->getScore()) && array_key_exists('dividend', $questionnaire_user->getScore())) ? $questionnaire_user->getScore()['dividend'] + ($question->getRankin() * 5) : $question->getRankin() * 5;

                if ($questionnaire_user->getScore()) {
                    $questionnaire_user->setScore(array_merge($questionnaire_user->getScore(), array('dividend' => $userDividend)));
                } else {
                    $questionnaire_user->setScore(array('dividend' => $userDividend));
                }

                $em->persist($questionnaire_user);
                $em->flush();
                $evaluationQuestObj = new QuestionnaireQuestionResponse();
                $evaluationQuestObj->setQuestionnaireUser($questionnaire_user);
                $evaluationQuestObj->setQuestion($question);
                $evaluationQuestObj->setResponse($eq);
                $em->persist($evaluationQuestObj);
                $em->flush();
            }
        }

        //If evaluation type question is given            
        if ($params['webcam'] && !empty($params['webcam'])) {
            foreach ($params['webcam'] as $key => $eq) {

                $question = $this->get('question_repository')->getElements(array('by_id' => $key, 'action' => 'one'));
                $userDividend = (is_array($questionnaire_user->getScore()) && array_key_exists('dividend', $questionnaire_user->getScore())) ? $questionnaire_user->getScore()['dividend'] + ($question->getRankin() * 5) : $question->getRankin() * 5;

                if ($questionnaire_user->getScore()) {
                    $questionnaire_user->setScore(array_merge($questionnaire_user->getScore(), array('dividend' => $userDividend)));
                } else {
                    $questionnaire_user->setScore(array('dividend' => $userDividend));
                }

                $em->persist($questionnaire_user);
                $em->flush();
                $webQuestObj = new QuestionnaireQuestionResponse();
                $webQuestObj->setQuestionnaireUser($questionnaire_user);
                $webQuestObj->setQuestion($question);
                $webQuestObj->setEnclosedFiles($eq);
                $em->persist($webQuestObj);
                $em->flush();
            }
        }
    }

    //Front office response section handling process
    public function foQuestionnaireProcessAction($slug_quest) {

        $request = $this->get('request');
        $session = $this->getRequest()->getSession();
        $slug_quest = $request->get('slug_quest');

        $em = $this->getDoctrine()->getManager();
        //Questionnaire information
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug' => $slug_quest, 'action' => 'one'));
        $administrator = 0;

        //checking for administrator user
        $access = $this->get('session')->get('access_admin');
        if (!empty($access) && array_key_exists('current', $access)) {
            $administrator = 1;
        }

        if ($administrator == 0) {
            //If not published and approved then redirect to home page 
            if ($questionnaire->getApprobation() != 1 || $questionnaire->getPublished() != 1) {
                return $this->redirect($this->generateUrl('fo_homepage'));
            }
        }

        $email = $session->get('email');

        if (!$email)
            return $this->redirect($this->generateUrl('fo_questionnaire_login_create', array('slug_quest' => $slug_quest)));

        $questionnaire_user = $this->get('questionnaire_user_repository')->getElements(array('by_questionnaire' => $questionnaire->getId(), 'by_email_user' => $email, 'action' => 'one'));

        if ($questionnaire_user) {
            $position = $questionnaire_user->getCurrentPosition();
        } else {
            $position = 1;
        }

        $elements = $this->get('questionnaire_element_repository')->getElements(array('by_questionnaire_id' => $questionnaire->getId(), 'by_level' => 0, 'by_position' => $position, 'order_by' => array('field' => 'position', 'sort' => 'asc')));

        $errors = NULL;
        $values = NULL;

        //if form is submitted  
        if ($request->getMethod() == 'POST') {

            $parameters = array();
            $elements = $this->get('questionnaire_element_repository')->getElements(array('by_questionnaire_id' => $questionnaire->getId(), 'by_level' => 0, 'by_position' => $position + 1, 'order_by' => array('field' => 'position', 'sort' => 'asc')));

            //getting all posted parameters
            $parameters['media_question'] = $request->files->get('media_question');
            $parameters['open_question'] = $request->get('open_question');
            $parameters['unique_choice_question'] = $request->get('unique_choice_question');
            $parameters['multi_choice_question'] = $request->get('multi_choice_question');
            $parameters['multi_choices'] = $request->get('multi_choices');
            $parameters['unique_choices'] = $request->get('unique_choices');
            $parameters['evaluation_question'] = $request->get('evaluation_question');
            $parameters['datetime'] = $request->get('date_time');
            $parameters['webcam'] = $request->get('webcam');

            $timeout = $request->get('timeout');

            /**
             * CHECKING SECTION
             */
            //If time not out then check errors else submit form automatically 
            if (!$timeout) {
                //getting errors if exists
                $error_list = $this->getQuestionnaireErrors($parameters);
                $errors = $error_list['errors'];
                $values = $error_list['values'];
            }


            /**
             * SAVING SECTION
             */
            if ($errors === NULL) {
                $parameters['questionnaire_user'] = $questionnaire_user;
                $parameters['questionnaire'] = $questionnaire;
                //function saving user response in question response
                $this->saveQuestionnaireAnswers($parameters);
            }


            if ($errors === NULL) {
                $questionnaire_user->setCurrentPosition($position + 1);
                $em->persist($questionnaire_user);
                $em->flush();
                return $this->redirect($this->generateUrl('fo_questionnaire_process', array('slug_quest' => $slug_quest)));
            }
        }

        if ($errors === NULL) {
            if ($position != '1' && count($elements) == 0) {
                $questionnaire_user->setAsFinished(1);
                $em->persist($questionnaire_user);
                $em->flush();
                return $this->redirect($this->generateUrl('fo_questionnaire_success', array('slug_quest' => $slug_quest)));
            }
        } else {

            $elements = $this->get('questionnaire_element_repository')->getElements(array('by_questionnaire_id' => $questionnaire->getId(), 'by_level' => 0, 'by_position' => $position, 'order_by' => array('field' => 'position', 'sort' => 'asc')));
        }



        $childArray = array();
        $subChildArray = array();

        //sorting array of child with position for 3 level
        foreach ($elements as $elem) {

            if (count($elem->getChildren()) > 0) {

                foreach ($elem->getChildren() as $child) {

                    $childArray[$elem->getId()][$child->getPosition()] = $child;

                    //array for sub child for level 2
                    if (count($child->getChildren()) > 0) {
                        foreach ($child->getChildren() as $subchild) {
                            $subChildArray[$child->getId()][$subchild->getPosition()] = $subchild;
                        }
                        ksort($subChildArray[$child->getId()]);
                    }
                }
                ksort($childArray[$elem->getId()]);
            }
        }


        $type_elements = $this->get('std_questionnaire_type_element_repository');
        $type_question = $this->get('std_question_type_repository');

        $type_elements = $type_elements->getElements(array('action' => 'array'));
        $type_elements[2]['type'] = $type_question->getElements(array('action' => 'array'));
        if ($administrator == 0) {
            $token = sha1($questionnaire_user->getEmail() . $position);
            if (!$session->get('token_' . $token)) {
                $first_time = true;
                $session->set('token_' . $token, $token);
            } else {
                $first_time = false;
            }
        } else {
            $first_time = true;
        }
        return $this->render('VMQuestionnaireBundle:Front:questionnaire_process.html.twig', array(
                    'questionnaire' => $questionnaire,
                    'elements' => $elements,
                    'childArray' => $childArray,
                    'subChildArray' => $subChildArray,
                    'type_elements' => $type_elements,
                    'errors' => $errors,
                    'values' => $values,
                    'pos' => $position,
                    'questionnare_user' => $questionnaire_user,
                    'first_time' => $first_time
        ));
    }

    //function validates uploaded file format    
    public function validateUploadedDocumentForResponse($file, $format) {
        $valid_doc = 0;
        $file_info = pathinfo($file->getClientOriginalName());
        $ext = $file_info['extension'];

        foreach ($format as $doc) {
            //Validation for valid file format
            if ($doc == 'txt') {
                if ($ext == 'txt') {
                    $valid_doc = 1;
                }
            }
            //For pdf format
            if ($doc == 'pdf') {
                if ($ext == 'pdf') {
                    $valid_doc = 1;
                }
            }
            //For document file
            if ($doc == 'doc') {
                if ($ext == 'doc' || $ext == 'docx') {
                    $valid_doc = 1;
                }
            }

            //For images
            if ($doc == 'img') {
                if ($ext == 'png' || $ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif') {
                    $valid_doc = 1;
                }
            }
        }

        return $valid_doc;
    }

    //function validates uploaded file format    
    public function validateDateTime($date, $question) {
        $flag = 0;
        $datetimeType = $question->getOptions()['DatetimeType'];

        //For range type question of datetime 
        if ($datetimeType[0] == 2) {
            if (isset($question->getOptions()['DatetimeType']) and isset($datetimeType['format'])) {

                if ($datetimeType['format'] == 'd-m-y') {
                    if (!$date['start_date']['month'] || !$date['start_date']['day'] || !$date['start_date']['year'] || !$date['end_date']['month'] || !$date['end_date']['day'] || !$date['end_date']['year']) {
                        $flag = 1;
                    } else {
                        $startdatetime = mktime(0, 0, 0, $date['start_date']['month'], $date['start_date']['day'], $date['start_date']['year']) . "<br/>";
                        $enddatetime = mktime(0, 0, 0, $date['end_date']['month'], $date['end_date']['day'], $date['end_date']['year']);
                    }
                }

                // If Format is m-y
                if ($datetimeType['format'] == 'm-y') {
                    if (!$date['start_date']['month'] || !$date['start_date']['year'] || !$date['end_date']['month'] || !$date['end_date']['year']) {
                        $flag = 1;
                    } else {
                        $startdatetime = mktime(0, 0, 0, $date['start_date']['month'], 0, $date['start_date']['year']) . "<br/>";
                        $enddatetime = mktime(0, 0, 0, $date['end_date']['month'], 0, $date['end_date']['year']);
                    }
                }

                // If Format is y
                if ($datetimeType['format'] == 'y') {
                    if (!$date['start_date']['year'] || !$date['end_date']['year']) {
                        $flag = 1;
                    } else {
                        $startdatetime = mktime(0, 0, 0, 0, 0, $date['start_date']['year']) . "<br/>";
                        $enddatetime = mktime(0, 0, 0, 0, 0, $date['end_date']['year']);
                    }
                }

                // If Format is m-y
                if ($datetimeType['format'] == 'H:i') {
                    if (($date['start_date']['hour'] == 'hr' || $date['end_date']['hour'] == 'hr' || $date['start_date']['minute'] == 'min' || $date['end_date']['minute'] == 'min' || ($date['start_date']['hour'] == '0' && $date['start_date']['minute'] == '0') || ($date['end_date']['hour'] == '0' && $date['end_date']['minute'] == '0'))) {
                        $flag = 1;
                    } else {
                        $startdatetime = mktime($date['start_date']['hour'], $date['start_date']['minute'], 0, 0, 0, 0) . "<br/>";
                        $enddatetime = mktime($date['end_date']['hour'], $date['end_date']['minute'], 0, 0, 0, 0);
                    }
                }
            } else {
                if ($date['start_date']['hour'] == 'hr' || $date['end_date']['hour'] == 'hr' || $date['start_date']['minute'] == 'min' || $date['end_date']['minute'] == 'min' || ($date['start_date']['hour'] == '0' && $date['start_date']['minute'] == '0') || ($date['end_date']['hour'] == '0' && $date['end_date']['minute'] == '0') || !$date['start_date']['month'] || !$date['start_date']['day'] || !$date['start_date']['year'] || !$date['end_date']['month'] || !$date['end_date']['day'] || !$date['end_date']['year']) {
                    $flag = 1;
                } else {
                    $startdatetime = mktime($date['start_date']['hour'], $date['start_date']['minute'], 0, $date['start_date']['month'], $date['start_date']['day'], $date['start_date']['year']) . "<br/>";
                    $enddatetime = mktime($date['end_date']['hour'], $date['end_date']['minute'], 0, $date['end_date']['month'], $date['end_date']['day'], $date['end_date']['year']);
                }
            }

            //if any field is emapty
            if ($flag == 1) {
                $errors[$question->getId()] = 'All fields are required';
            } else {
                //If first is greater than second then throw error
                if ($startdatetime > $enddatetime) {
                    $errors[$question->getId()] = 'First should be smaller than second';
                }
            }
        } else {
            if (isset($question->getOptions()['DatetimeType']) and isset($datetimeType['format'])) {

                if ($datetimeType['format'] == 'd-m-y') {
                    if (!$date['start_date']['month'] || !$date['start_date']['day'] || !$date['start_date']['year']) {
                        $flag = 1;
                    } else {
                        $startdatetime = mktime(0, 0, 0, $date['start_date']['month'], $date['start_date']['day'], $date['start_date']['year']) . "<br/>";
                    }
                }

                // If Format is m-y
                if ($datetimeType['format'] == 'm-y') {
                    if (!$date['start_date']['month'] || !$date['start_date']['year']) {
                        $flag = 1;
                    } else {
                        $startdatetime = mktime(0, 0, 0, $date['start_date']['month'], 0, $date['start_date']['year']) . "<br/>";
                    }
                }

                // If Format is y
                if ($datetimeType['format'] == 'y') {
                    if (!$date['start_date']['year']) {
                        $flag = 1;
                    } else {
                        $startdatetime = mktime(0, 0, 0, 0, 0, $date['start_date']['year']) . "<br/>";
                    }
                }

                // If Format is m-y
                if ($datetimeType['format'] == 'H:i') {
                    if ($date['start_date']['hour'] == 'hr' || $date['start_date']['minute'] == 'min' || ($date['start_date']['hour'] == '0' && $date['start_date']['minute'] == '0')) {
                        $flag = 1;
                    } else {
                        $startdatetime = mktime($date['start_date']['hour'], $date['start_date']['minute'], 0, 0, 0, 0) . "<br/>";
                    }
                }
            } else {
                if ($date['start_date']['hour'] == 'hr' || $date['start_date']['minute'] == 'min' || ($date['start_date']['hour'] == '0' && $date['start_date']['minute'] == '0') || !$date['start_date']['month'] || !$date['start_date']['day'] || !$date['start_date']['year']) {
                    $flag = 1;
                } else {
                    $startdatetime = mktime($date['start_date']['hour'], $date['start_date']['minute'], 0, $date['start_date']['month'], $date['start_date']['day'], $date['start_date']['year']) . "<br/>";
                }
            }
        }
    }

    //Function uploads files for media type question
    public function uploadMediaDocument($file_name, $obj, $user) {
        $root_path = $this->getRequest()->server->get('DOCUMENT_ROOT');

        //For user which are registered
        if ($user->getUser()) {
            $file_path = $root_path . '/uploads/users/' . $user->getUser()->getId() . '/responses';
        } else {
            //For user which are not registered
            $file_path = $root_path . '/uploads/users/guest/' . $user->getId() . '/responses';
        }


        if (!is_dir($root_path . '/uploads')) {
            mkdir($root_path . '/uploads', 0777);
        }

        if (!is_dir($root_path . '/uploads/users')) {
            mkdir($root_path . '/uploads/users', 0777);
        }

        //For user which are registered
        if ($user->getUser()) {
            if (!is_dir($root_path . '/uploads/users/' . $user->getUser()->getId())) {
                mkdir($root_path . '/uploads/users/' . $user->getUser()->getId(), 0777);
            }
        } else {
            //For user which are not registered
            if (!is_dir($root_path . '/uploads/users/guest')) {
                mkdir($root_path . '/uploads/users/guest', 0777);
            }

            if (!is_dir($root_path . '/uploads/users/guest/' . $user->getId())) {
                mkdir($root_path . '/uploads/users/guest/' . $user->getId(), 0777);
            }
        }

        if (!is_dir($file_path)) {
            mkdir($file_path, 0777);
        }

        //If not exists file then add
        if (!file_exists($file_path . '/' . $file_name)) {
            $obj->move($file_path, $file_name);
        } else {
            unlink($file_path . '/' . $file_name);
            $obj->move($file_path, $file_name);
        }
    }

    //Middle office Questionnaire
    public function moQuestionnaireProcessAction() {

        $request = $this->get('request');
        $slug_quest = $request->get('slug_quest');
        $slug_ent = $request->get('slug_ent');

        $session = $this->getRequest()->getSession();
        //$position= 1;
        //If next question link is clicked
        if ($request->getMethod() == 'POST') {
            $session->set('position', $session->get('position') + 1);
        } else {
            $session->set('position', 1);
        }

        $position = $session->get('position');

        //Questionnaire information
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug' => $slug_quest, 'action' => 'one'));
        //elements list
        $elements = $this->get('questionnaire_element_repository')->getElements(array('by_questionnaire_id' => $questionnaire->getId(), 'by_level' => 0, 'by_position' => $position, 'order_by' => array('field' => 'position', 'sort' => 'asc')));

        $childArray = array();
        $subChildArray = array();

        //sorting array of child with position for 3 level
        foreach ($elements as $elem) {
            if (count($elem->getChildren()) > 0) {
                foreach ($elem->getChildren() as $child) {

                    $childArray[$elem->getId()][$child->getPosition()] = $child;

                    //array for sub child for level 2
                    if (count($child->getChildren()) > 0) {
                        foreach ($child->getChildren() as $subchild) {
                            $subChildArray[$child->getId()][$subchild->getPosition()] = $subchild;
                        }
                        ksort($subChildArray[$child->getId()]);
                    }
                }
                ksort($childArray[$elem->getId()]);
            }
        }

        $type_elements = $this->get('std_questionnaire_type_element_repository');
        $type_question = $this->get('std_question_type_repository');

        $type_elements = $type_elements->getElements(array('action' => 'array'));
        $type_elements[2]['type'] = $type_question->getElements(array('action' => 'array'));


        if ($position != '1' && count($elements) == 0) {
            $session->set('position', ($position - 1));
            return $this->render('VMQuestionnaireBundle:Middle:questionnaire_success.html.twig', array('questionnaire' => $questionnaire, 'enterprise' => $questionnaire->getEnterprise()));
        }

        // BREADCRUMBS
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem($questionnaire->getEnterprise()->getName(), $this->get("router")->generate("mo_enterprise_show", array('slug_ent' => $request->get('slug_ent'))));

        $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate("mo_questionnaires", array('slug_ent' => $request->get('slug_ent'))));

        $breadcrumbs->addItem($questionnaire->getName(), $this->get("router")->generate("mo_questionnaire_show", array('slug_ent' => $request->get('slug_ent'), 'slug_quest' => $request->get('slug_quest'))));
        $breadcrumbs->addItem('preview', $this->get("router")->generate("mo_questionnaire_preview_show", array('slug_ent' => $request->get('slug_ent'), 'slug_quest' => $request->get('slug_quest'))));
        $breadcrumbs->addItem('responses');

        return $this->render('VMQuestionnaireBundle:Middle:questionnaire_process.html.twig', array(
                    'questionnaire' => $questionnaire,
                    'elements' => $elements,
                    'childArray' => $childArray,
                    'subChildArray' => $subChildArray,
                    'type_elements' => $type_elements,
                    'position' => $position
        ));
    }

    public function moQuestionnaireSuccessAction($slug_quest) {
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug' => $slug_quest, 'action' => 'one'));
        if ($questionnaire) {
            return $this->render('VMQuestionnaireBundle:Front:questionnaire_success.html.twig', array('questionnaire' => $questionnaire, 'enterprise' => $questionnaire->getEnterprise()));
        }
    }

    public function moQuestionnairePreviewShowAction() {
        $request = $this->get('request');

        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug' => $request->get('slug_quest'), 'action' => 'one'));

        // BREADCRUMBS
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem($questionnaire->getEnterprise()->getName(), $this->get("router")->generate("mo_enterprise_show", array('slug_ent' => $request->get('slug_ent'))));

        $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate("mo_questionnaires", array('slug_ent' => $request->get('slug_ent'))));

        $breadcrumbs->addItem($questionnaire->getName(), $this->get("router")->generate("mo_questionnaire_show", array('slug_ent' => $request->get('slug_ent'), 'slug_quest' => $request->get('slug_quest'))));
        $breadcrumbs->addItem('preview');

        return $this->render('VMQuestionnaireBundle:Middle:preview_show.html.twig', array('questionnaire' => $questionnaire));
    }

    //Middle office Questionnaire
    public function boQuestionnaireProcessAction() {

        $request = $this->get('request');
        $slug_quest = $request->get('slug_quest');
        $session = $this->getRequest()->getSession();
        //$position= 1;
        //If next question link is clicked
        if ($request->getMethod() == 'POST') {
            $session->set('position', $session->get('position') + 1);
        } else {
            $session->set('position', 1);
        }

        $position = $session->get('position');

        //Questionnaire information
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug' => $slug_quest, 'action' => 'one'));
        //elements list
        $elements = $this->get('questionnaire_element_repository')->getElements(array('by_questionnaire_id' => $questionnaire->getId(), 'by_level' => 0, 'by_position' => $position, 'order_by' => array('field' => 'position', 'sort' => 'asc')));

        $childArray = array();
        $subChildArray = array();

        //sorting array of child with position for 3 level
        foreach ($elements as $elem) {
            if (count($elem->getChildren()) > 0) {
                foreach ($elem->getChildren() as $child) {

                    $childArray[$elem->getId()][$child->getPosition()] = $child;

                    //array for sub child for level 2
                    if (count($child->getChildren()) > 0) {
                        foreach ($child->getChildren() as $subchild) {
                            $subChildArray[$child->getId()][$subchild->getPosition()] = $subchild;
                        }
                        ksort($subChildArray[$child->getId()]);
                    }
                }
                ksort($childArray[$elem->getId()]);
            }
        }

        $type_elements = $this->get('std_questionnaire_type_element_repository');
        $type_question = $this->get('std_question_type_repository');

        $type_elements = $type_elements->getElements(array('action' => 'array'));
        $type_elements[2]['type'] = $type_question->getElements(array('action' => 'array'));


        if ($position != '1' && count($elements) == 0) {
            $session->set('position', ($position - 1));
            return $this->render('VMQuestionnaireBundle:Front:questionnaire_success.html.twig', array('questionnaire' => $questionnaire, 'enterprise' => $questionnaire->getEnterprise()));
        }

        // BREADCRUMBS
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate("bo_questionnaires"));

        $breadcrumbs->addItem($questionnaire->getName(), $this->get("router")->generate("bo_questionnaire_show", array('slug_quest' => $request->get('slug_quest'))));
        $breadcrumbs->addItem('preview', $this->get("router")->generate("bo_questionnaire_preview_show", array('slug_quest' => $request->get('slug_quest'))));
        $breadcrumbs->addItem('responses');

        return $this->render('VMQuestionnaireBundle:Back:questionnaire_process.html.twig', array(
                    'questionnaire' => $questionnaire,
                    'elements' => $elements,
                    'childArray' => $childArray,
                    'subChildArray' => $subChildArray,
                    'type_elements' => $type_elements,
                    'position' => $position
        ));
    }

    public function boQuestionnaireSuccessAction($slug_quest) {
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug' => $slug_quest, 'action' => 'one'));
        if ($questionnaire) {
            return $this->render('VMQuestionnaireBundle:Front:questionnaire_success.html.twig', array('questionnaire' => $questionnaire, 'enterprise' => $questionnaire->getEnterprise()));
        }
    }

    public function boQuestionnairePreviewShowAction() {
        $request = $this->get('request');

        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug' => $request->get('slug_quest'), 'action' => 'one'));

        // BREADCRUMBS
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate("bo_questionnaires"));
        $breadcrumbs->addItem($questionnaire->getName(), $this->get("router")->generate("bo_questionnaire_show", array('slug_quest' => $request->get('slug_quest'))));
        $breadcrumbs->addItem('preview');

        return $this->render('VMQuestionnaireBundle:Back:preview_show.html.twig', array('questionnaire' => $questionnaire));
    }

    //To download media type files
    public function moQuestionnaireResponseDownloadAction() {

        $request = $this->get('request');
        $response_id = $request->get('response_id');
        $response = $this->get('questionnaire_question_response_repository')->getElements(array('by_id' => $response_id, 'action' => 'one'));

        $file_path = $this->getRequest()->server->get('DOCUMENT_ROOT') . $response->getEnclosedfiles();

        if (file_exists($file_path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Content-Type: application/force-download");
            header('Content-Disposition: attachment; filename=' . urlencode(basename($file_path)));
            // header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            ob_clean();
            flush();
            readfile($file_path);
            exit;
        }

        exit;
    }

    private function getNextElement() {
        //return next element by param
    }

}
