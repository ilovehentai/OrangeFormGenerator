<?php

namespace FormGenerator\Patterns;
use FormGenerator\FormElements\BaseElement;

interface FormGeneratorObserver{
    public function update(BaseElement $sender, $args);
}