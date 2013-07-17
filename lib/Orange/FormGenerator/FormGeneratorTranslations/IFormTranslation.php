<?php

namespace FormGenerator\FormGeneratorTranslations;

/**
 *
 * @author josesantos
 */
interface IFormTranslation {
    public function __construct($locale = "");
    public function setLocale($locale);
    public function getTranslation($text, array $parameters = array());
    public static function getInstance($locale = "");
}
