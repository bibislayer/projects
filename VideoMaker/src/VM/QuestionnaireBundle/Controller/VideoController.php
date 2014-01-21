<?php

namespace VM\QuestionnaireBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class VideoController extends Controller {

    public function foSaveAction() {
        $request = $this->get('request');
        $root_path = $this->getRequest()->server->get('DOCUMENT_ROOT');
        $file_path = $root_path . '/uploads/videos';
        if (!is_dir($root_path . '/uploads'))
            mkdir($root_path . '/uploads', 0777);
        if (!is_dir($file_path))
            mkdir($file_path, 0777);
        foreach (array('video', 'audio') as $type) {
            if (isset($_FILES["${type}-blob"])) {
                $fileName = $_POST["${type}-filename"];
                $file_path = $file_path.'/'.$fileName;
                if (!move_uploaded_file($_FILES["${type}-blob"]["tmp_name"], $file_path)) {
                    echo(" problem moving uploaded file");
                }

                echo($file_path);
            }
        }
        exit;
    }
    
    public function foGetQuestionNameAction($id){
        $element = $this->get('questionnaire_element_repository')->getElements(array('by_id' => $id, 'action' => 'one'));
        echo $element->getName();
        exit;
    }

}
