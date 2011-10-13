<?php

namespace FormGenerator\Patterns;
use FormGenerator\FormGenerator;

interface ElementObservable
{
    public function addObserver(FormGenerator $observer);
    public function notify(array $data = array());
}
