<?php

namespace FP\GeneralBundle\Twig;

class FPExtension extends \Twig_Extension {
    
    public function getFilters() {
        return array(
            'duree' => new \Twig_Filter_Method($this, 'dureeFilter'),
            'explodeTwig' => new \Twig_Filter_Method($this, 'explodeStringFilter'),
            'slugifyTwig' => new \Twig_Filter_Method($this, 'slugify')
        );
        
    }
    
    public function getFunctions()
    {
        return array(
            'evalTwig' => new \Twig_Function_Method($this, 'evaluateString', array(
                'needs_context' => true,
                'needs_environment' => true,
                
            )),
        );
    }
     
   public function slugify($text)
    { 
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text))
        {
          return 'n-a';
        }

        return $text;
    } 
    
    public function dureeFilter($number) {
        $annees = '';
        $mois = '';
        $jours = '';
        $heures = '';
        $retour = '';

        while ($number >= 525949) {
            $annees += 1;
            $number = $number - 525949;
        }

        while ($number < 525949 && $number >= 43829) {
            $mois += 1;
            $number = $number - 43829;
            if ($mois == 12) {
                $mois = $mois - 12;
                $annees +=1;
            }
        }
        while ($number < 43829 && $number >= 1440) {
            $jours += 1;
            $number = $number - 1440;
        }
        while ($number < 1440 && $number >= 60) {
            $heures += 1;
            $number = $number - 60;
        }

        if ($annees != 0){
            $retour .= $annees;
            $retour .= ($annees > 1) ? ' années ' : ' année ';
        }if ($mois != 0)
            $retour .= $mois . ' mois ';
        if ($jours != 0){
            $retour .= $jours;
            $retour .= ($jours > 1) ? ' jours ' : ' jour ';
        }if ($heures != 0){
            $retour .= $heures;
            $retour .= ($annees > 1) ? ' heures ' : ' heure ';
        }if ($number != 0){
            $retour .= $number;
            $retour .= ($annees > 1) ? ' minutes ' : ' minute ';
        }
        return $retour;
    }

   
    
     /**
    * Loads a string template and returns the evaluated result
    *
    * @param Twig_Environment $currEnv Current environment
    * @param array $context
    * @param string $string The string template to evaluate
    * @return string
    */
    
    public function evaluateString(\Twig_Environment $currEnv, $context, $string)
    {
       
        $env = $this->setUpEnvironment($currEnv);
         return $env->loadTemplate($string)->render($context);
    }

    
  
    /**
    * Makes a new environment and adds the extensions from the current environment
    *
    * @param Twig_Environment $currEnv Current environment
    * @return Twig_Environment
    */
    
    private function setUpEnvironment(\Twig_Environment $currEnv)
    {
        $env = new \Twig_Environment(new \Twig_Loader_String());
        $env->setExtensions($currEnv->getExtensions());
        return $env;
    }
    
    public function explodeStringFilter($string, $delim=' '){
        return explode($delim,$string);
    }
    
    public function getName() {
        return 'forma_search_extension';
    }
       
    //image resize function
    function image_resize($src, $dst, $width, $crop = 0) {

        if (!list($w, $h) = getimagesize($src))
            return "Unsupported picture type!";

        $type = strtolower(substr(strrchr($src, "."), 1));
        if ($type == 'jpeg')
            $type = 'jpg';
        switch ($type) {
            case 'bmp': $img = imagecreatefromwbmp($src);
                break;
            case 'gif': $img = imagecreatefromgif($src);
                break;
            case 'jpg': $img = imagecreatefromjpeg($src);
                break;
            case 'png': $img = imagecreatefrompng($src);
                break;
            default : return "Unsupported picture type!";
        }

        // resize
        $height = (($h * $width) / $w);

        $new = imagecreatetruecolor($width, $height);

        // preserve transparency
        if ($type == "gif" or $type == "png") {
            imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
            imagealphablending($new, false);
            imagesavealpha($new, true);
        }
        
        imagecopyresampled($new, $img, 0, 0, 0, 0, $width, $height, $w, $h);
       
        switch ($type) {
            case 'bmp': imagewbmp($new, $dst);
                break;
            case 'gif': imagegif($new, $dst);
                break;
            case 'jpg': imagejpeg($new, $dst);
                break;
            case 'png': imagepng($new, $dst);
                break;
        }
        return true;
    } 
}
?>