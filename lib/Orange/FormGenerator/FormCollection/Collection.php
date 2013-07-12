<?php
namespace FormGenerator\FormCollection;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CollectionClass
 *
 * @author josesantos
 */
class Collection implements \IteratorAggregate
{
    private $_items = array();
    private $_count = 0;
    
    public function getIterator() {
        return new CollectionIterator($this->_items);
    }
    
    public function add($value) {
        $this->_items[$this->_count++] = $value;
    }
    
    public function isEmpty() {
        return $this->_count == 0 ? true : false;
    }
    
    public function get($index) {
        return $this->_items[$index];
    }
}
