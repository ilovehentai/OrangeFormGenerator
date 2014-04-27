<?php
require_once(__DIR__ . DIRECTORY_SEPARATOR . "example" . DIRECTORY_SEPARATOR . "init.php");
use FormGenerator\FormGenerator;

/** For the select lang form **/
use FormGenerator\FormElements\FormElement;
use FormGenerator\FormElements\SelectElement;
use FormGenerator\FormElements\OptionElement;
use FormGenerator\FormElements\LabelElement;
use FormGenerator\FormElements\FieldsetElement;
use FormGenerator\FormElements\LegendElement;

/** Initialize variables **/
$nome_pessoal = "My Name (Default Value)";
$locale = filter_input(INPUT_GET, "locale", FILTER_SANITIZE_STRING);
$locale = (!empty($locale)) ? $locale : "pt_PT";
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
        <script type="text/javascript">
            $(function(){
               $("#locale").change(function(){
                  window.location.href = "?locale=" + $(this).val(); 
               });
            });
        </script>
    </head>
    <body>
        <img alt="logo" src="/images/logo.png" />
        <div id="selectlang">
            <?php
                $select_lang_form = new FormGenerator("selectlang_form", 
                                                            array(
                                                                    "rootDir" => "%DIR%/../../../..", 
                                                                    "cacheDir" => "%ROOT%/example/cache/",
                                                                    "templateDir" => "%ROOT%/example/configs/")
                                                     );
                
                $config_for_formelement = array(
                                                "id" => "locale_form",
                                                "attributes" => array("name" => "locale_form", "class" => "sp_css")
                                                );
                $select_lang_form->setOutputOnly(true);
                $select_lang_form->setFormElement(new FormElement($config_for_formelement));
                $options_for_select = array("pt_PT" => "Português", "fr_CH" => "Français", "gb_UK" => "English");
                $config_for_select = array("attributes" => array("id" => "locale", "name" => "locale"), "options" => $options_for_select);
                $select_element = new SelectElement($config_for_select);
                $select_element->addOption(new OptionElement(array("text" => "Default", 
                                                                   "attributes" => array("value" => "~")
                                                                )));
                $select_element->setTranslator($select_lang_form->getFormTranslator());
                $fieldset_element = new FieldsetElement(array("attributes" => array("id" => "group1")));
                $select_lang_form->addFieldset($fieldset_element, new LegendElement(
                                                                                array("text" => "Select Language", 
                                                                                      "attributes" => array("class" => "legend_xpto"))
                                                                                ));
                $select_lang_form->addElement($select_element, new LabelElement(
                                                                                array("text" => "Locale", 
                                                                                      "attributes" => array("for" => "locale"))
                                                                                ));
                $select_lang_form->setLocale("en_GB");
                $select_lang_form->setDebug(true);
                
                echo $select_lang_form->render("locale.html");
            ?>
        </div>
        <?php
        $clear_saved_data = true;
        if(isset($_POST['submit'])){
            $clear_saved_data = false;
            if(FormGenerator::isValid('simple_form') === true){
                echo "submetido com sucesso";
            }else{
                echo FormGenerator::getFormErrors('simple_form');
                echo "<a href=\"/\">Back to form</a>";
            }
            
        }
        try {
            $form = new FormGenerator("simple_form", 
                                        array(
                                             "configDir" => __DIR__ . "/example/configs/",
                                             "cacheDir" => __DIR__ . "/example/mycache/",
                                             "configFile" => "example.yml",
                                             "templateDir" => __DIR__ . "/example/configs/",
                                             "elements_default_values" => array("nome_pessoal" => $nome_pessoal),
                                             "readonly" => true,
                                             "locale" => $locale
                                            ));
            $form->setDebug(true);
            if($clear_saved_data) {
                $form->clearStoredItemsValues();
            }
            echo $form->render();
            echo $form->renderDebug();

        } catch (Exception $e) {
            echo  $e->getMessage();
            echo "<br />";
            print_r($e->getTraceAsString());
        }
        ?>
    </body>
</html>
