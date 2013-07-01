<?php

namespace FormGenerator\FormGenerator;
use FormGenerator\FormElements\BaseElement;

interface FormGeneratorObserver{
    public function update(BaseElement $sender, $args);
}