<?php

namespace FormGenerator\Validation;

class NumberValidation extends BaseValidation {

    private $_mRulename;

    public function __construct($config) {
        parent::__construct($config);
        $size = 0;
        list($this->_mRulename, $number) = explode(":", $config['rule']);
        $this->_mExpression = $this->generateExpression($size);
    }

    private function generateExpression($size) {
        $length = "";
        if ($size !== null && $size !== "" && $size !== "~") {
            $minus = preg_match("/-[0-9]+/", $size);
            $plus = preg_match("/\+[0-9]+/", $size);

            if ($minus === 1) {
                $minus = str_replace("-", "", $minus);
            } else if ($plus !== 1) {
                $minus = $size;
            }

            if ($plus === 1) {
                $plus = str_replace("+", "", $plus) . (($minus === 1) ? "," : "");
            } else {
                $plus = "";
            }

            $length = "{" . $plus . $minus . "}";
        }
        return "[0-9]" + $length;
    }

}
