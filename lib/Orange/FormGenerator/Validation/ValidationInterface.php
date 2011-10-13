<?php

namespace FormGenerator\Validation;

/**
 *
 * @author seara
 */
interface ValidationInterface {
    public function isValid($value);
    public function getExpression();
}

?>
