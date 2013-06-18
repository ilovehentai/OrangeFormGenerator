<?php
namespace FormGenerator\Patterns;
use FormGenerator\Collection;
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
    function setTemplatePath($path);
    function setFormElements(FormElement $formElement, Collection $elementsCollection, Collection $fieldsetCollection);
    function render();
    function addJavaScript($jscript);
}
