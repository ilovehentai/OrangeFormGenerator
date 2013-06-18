<?php
namespace FormGenerator;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IteratorClass
 *
 * @author josesantos
 */
class CollectionIterator implements \Iterator{
    
    private $_list;
    
    public function __construct($array = array()) {
        $this->_list = $array;
    }
    
    public function current() {
        return current($this->_list);
    }

    public function key() {
        return key($this->_list);
    }

    public function next() {
        return next($this->_list);
    }

    public function rewind() {
        reset($this->_list);
    }

    public function valid() {
        $key = $this->key();
        return ($key !== NULL && $key !== FALSE);
    }
}
