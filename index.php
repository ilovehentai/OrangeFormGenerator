<?php
require_once(__DIR__ . DIRECTORY_SEPARATOR . "example" . DIRECTORY_SEPARATOR . "init.php");
use FormGenerator\FormGenerator;

/** Initialize variables **/
$nome_pessoal = "";
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Orange FormGenerator</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/base/jquery-ui.css" type="text/css" media="all" />
        <link rel="stylesheet" href="/css/form.css" type="text/css" media="all" />
        <link rel="stylesheet" href="/css/example.css" type="text/css" media="all" />
        
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js" type="text/javascript"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/i18n/jquery-ui-i18n.min.js" type="text/javascript"></script>
        <script src="/js/validate.js" type="text/javascript"></script>
        <script src="/js/textcount.js" type="text/javascript"></script>
    </head>
    <body>
        <img alt="logo" src="/images/logo.png" />
        <?php
        
        if(isset($_POST['submit'])){
            
            if(FormGenerator::isValid('simple_form')){
                echo "submetido com sucesso";
            }else{
                echo FormGenerator::getFormErrors('simple_form');
            }            
            
        }else{
            try {
                $form = new FormGenerator("simple_form", 
                                            array(
                                                 "configDir" => __DIR__ . "/example/configs/",
                                                 "cacheDir" => __DIR__ . "/example/cache/",
                                                 "configFile" => "example.yml",
                                                 "templateDir" => __DIR__ . "/example/configs/",
                                                 "elements_default_values" => array("nome_pessoal" => $nome_pessoal)
                                                ));
                echo $form->render();
                $form->set_mDebug(true);
                echo $form->renderDebug();
                
            } catch (FormGeneratorException $e) {
                print_r($e->getTraceAsString());
            } catch (Exception $e) {
                print_r($e->getTraceAsString());
            }
        }
        ?>
    </body>
</html>
