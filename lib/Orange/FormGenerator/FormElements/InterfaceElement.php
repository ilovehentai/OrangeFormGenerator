<?php
namespace FormGenerator\FormElements;

interface InterfaceElement{
    public function __construct(array $config);
    public function build();
    public function set_mId($id);
    public function get_mId();
}
