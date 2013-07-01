<?php

namespace FormGenerator\FormElements;
use FormGenerator\FormGenerator;

interface ElementObservable
{
    public function addObserver(FormGenerator $observer);
    public function notify(array $data = array());
}
