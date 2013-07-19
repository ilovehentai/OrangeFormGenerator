<?php
namespace FormGenerator\FormElements;

class FormElement extends BaseElement{
    
    private $_mAction;
    private $_mMethod;
    private $_mEnctype;
    private $_stream;
    private $_formData;
    
    protected $_mId;

    protected $_mSkeleton;
    protected $_mAttributes;
    
    protected $_csrfToken;
        
    public function __construct(array $config, CsrfTokenElement $csrf_token = null) {
        
        $this->_mSkeleton = "<form%s>\nstream\n{{csrf_token}}\n</form>\n";
        $this->_mAttributes = $config['attributes'];
        $this->_formData = $config;
        if(!is_null($csrf_token)) {
            $this->_csrfToken = $csrf_token;
        }
    }
    
    private function processFormAttributes(array $data)
    {
        $this->set_mAction($data['action']);
        $this->set_mEnctype($data['enctype']);
        $this->set_mId($data['id']);
        $this->set_mMethod($data['method']);
    }
    
    public function build(){
        
        $this->processFormAttributes($this->_formData);
        $attr = array(
                        "enctype" => $this->get_mEnctype(),
                        "action" => $this->get_mAction(),
                        "method" => $this->get_mMethod(),
                        "id" => $this->get_mId()
                    );
        
        if(is_array($this->_mAttributes))
        {
            $this->_mAttributes = array_merge($this->_mAttributes, $attr);
        }
        else
        {
            $this->_mAttributes = $attr;
        }
        $this->buildAndaddCsrfToken();
        $html_data = parent::build();
        return str_replace("stream", $this->_stream, $html_data);
    }
    
    private function buildAndaddCsrfToken() {
        if(!is_null($this->_csrfToken)) {
            $token = $this->_csrfToken->build();
            $this->_mSkeleton = str_replace("{{csrf_token}}", $token, $this->_mSkeleton);
        }
    }
    
    public function get_mAction() {
        return $this->_mAction;
    }

    public function set_mAction($_mAction) {
        if(is_string($_mAction) && $_mAction != "self")
        {
            $this->_mAction = $_mAction;
        }
        else
        {
            $this->_mAction = "";
        }
    }

    public function get_mMethod() {
        return $this->_mMethod;
    }

    public function set_mMethod($_mMethod) {
        if(strtolower($_mMethod) == "get")
        {
            $this->_mMethod = $_mMethod;
        }
        else
        {
            $this->_mMethod = "post";
        }
    }

    public function get_mEnctype() {
        return $this->_mEnctype;
    }

    public function set_mEnctype($_mEnctype) {
        $valid_enctypes = array(
                                    "application/x-www-form-urlencoded",
                                    "multipart/form-data",
                                    "text/plain"
                                );
        if(in_array($_mEnctype, $valid_enctypes))
        {
            $this->_mEnctype = $_mEnctype;
        }
        else
        {
            $this->_mEnctype = $valid_enctypes[1];
        }
    }

    public function get_mId() {
        return $this->_mId;
    }

    public function set_mId($_mId) {
        $this->_mId = $_mId;
    }
    
    public function getStream() {
        return $this->_stream;
    }

    public function setStream($stream) {
        $this->_stream = $stream;
    }
    
    public function get_csrfToken() {
        return $this->_csrfToken;
    }

    public function set_csrfToken($_csrfToken) {
        $this->_csrfToken = $_csrfToken;
        return $this;
    }
}
