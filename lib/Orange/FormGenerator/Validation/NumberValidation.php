<?php

namespace FormGenerator\Validation;

class NumberValidation extends BaseValidation {

    private $_mRulename;

    public function __construct($config) {
        parent::__construct($config);
        $size = 0;
        list($this->_mRulename, $size) = explode(":", $config['rule']);
        $this->_mExpression = $this->generateExpression($size);
    }

    private function generateExpression($size) {
        $length = ""; 
        $minus = "";
        $plus = "";
        if ($size !== null && $size !== "" && $size !== "~") {
            $match_minus = preg_match("/-[0-9]+/", $size, $minus);
            $match_plus = preg_match("/\+[0-9]+/", $size, $plus);

            if ($match_minus === 1) {
                $minus = str_replace("-", "", $minus[0]);
            } else if ($match_plus !== 1) {
                $minus = $size;
            }

            if ($match_plus === 1) {
                $plus = str_replace("+", "", $plus[0]) . (($match_minus === 1) ? "," : "");
            } else {
                $plus = "";
            }

            $length = "{" . $plus . $minus . "}";
        }
        return "[0-9]" . $length;
    }

}
