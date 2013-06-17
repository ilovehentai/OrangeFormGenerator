<?php
namespace FormGenerator\Patterns;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author josesantos
 */
interface IFormTemplateAdapter {
    function placeFormElements($elements, $fieldset);
    function setTemplatePath($path);
}
