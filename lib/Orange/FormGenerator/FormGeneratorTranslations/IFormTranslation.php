<?php

namespace FormGenerator\FormGeneratorTranslations;

/**
 *
 * @author josesantos
 */
interface IFormTranslation {
    public function __construct($locale = "", $translations_path = "");
    public function setLocale($locale);
    public function setTranslationsPath($locale_path);
    public function getTranslation($text, array $parameters = array());
    public static function getInstance($locale = "");
}
