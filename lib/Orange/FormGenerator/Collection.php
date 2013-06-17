<?php
namespace FormGenerator;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CollectionClass
 *
 * @author josesantos
 */
class Collection implements IteratorAggregate
{
    private $_items = array();
    private $_count = 0;
    
    public function getIterator() {
        return new CollectionIterator($this->_items);
    } //put your code here
    
    public function add($value) {
        $this->items[$this->_count++] = $value;
    }
}
