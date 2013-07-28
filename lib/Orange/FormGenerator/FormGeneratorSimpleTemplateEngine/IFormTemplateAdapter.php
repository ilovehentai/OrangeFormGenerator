<?php
namespace FormGenerator\FormGeneratorSimpleTemplateEngine;
use FormGenerator\FormCollection\Collection;
use FormGenerator\FormElements\FormElement;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author josesantos
 */
interface IFormTemplateAdapter {
    function setTemplatePath($path=null);
    function setFormElements(FormElement $formElement, Collection $elementsCollection, Collection $fieldsetCollection);
    function render();
    function addJavaScript($jscript);
}
