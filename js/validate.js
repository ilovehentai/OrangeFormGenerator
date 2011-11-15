(function( $ ){
 
    $.fn.validate = function(options) {
  
        var _this = this;
        var opts = $.extend({}, $.fn.validate.defaults, options);
        
        var masks = 
        {
            "not_empty" : /(.){1,}/,
            "money" : /^[\d]+((\.|,)[\d]{2}){0,1}$/,
            "phone" : /^([(][0-9-+ ]+[)]|[0-9-+ ]*)?[0-9 ]{9,}([(][a-zA-Z0-9. ]+[)]|[a-zA-Z0-9. ]+)?$/,
            "email" : /^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.([a-zA-Z]{2,4}\.)?[a-zA-Z]{2,4}$/,
            "selected" : /^[1-9]{1}([0-9])*$/,
            "regExpression" : function(regex){
                return regex;
            },
            "not_null_value" : /[^null]/,
            "nif" : /[0-9]{9}/,
            "zip_code" : /([1-9]{1}[0-9]{3}-[0-9]{3}|[1-9]{1}[0-9]{3})/,
            "min_size" : function(size){
                return "(.){" + size + ",}";
            },
            "match" : function(val){
                return $("#" + val).val();
            }
            //checked
        };
        
        return this.each(function() {
            
            var o = $.meta ? $.extend({}, opts, _this.data()) : opts;
            if(jQuery().textcount)
            {
                $.each(o.fields, function(key, val) {
                    if(val.textcount)
                    {
                        $("#" + key).textcount(val.textcount);
                    }
                });
            }
            
            $(_this).submit(function(){
            
                var string_msg = "";
                $.each(o.fields, function(key, val) {

                    key = $("#" + key);
                    $.fn.validate.resetMark(key);
                    
                    var value = key.val();

                    var check = function(){

                        var valid = true;
                        switch(val.validator)
                        {
                            case 'checked':
                                if(!key.is(":checked"))
                                {
                                    valid = false;
                                }
                                break;
                            default:
                                regex = null;
                                if(val.validator.indexOf(":") > -1)
                                {
                                    var valreg = (val.validator).substr((val.validator).indexOf(":") + 1, (val.validator).length);
                                    var validator = (val.validator).substr(0, (val.validator).indexOf(":"));
                                    
                                    regex = masks[validator](valreg);
                                }
                                else
                                {
                                    regex = masks[val.validator];
                                }
                                
                                if(!value.match(regex))
                                {
                                    valid = false;
                                }
                        }
                        
                        if(!valid)
                        {
                            $.fn.validate.mark(key);
                            string_msg += val.msg + "<br/>";
                        }
                    }
                    
                    if(value!=undefined)
                    {
                        check();
                    }
                });
                
                if(string_msg != "")
                {
                    $.fn.validate.showErrors(string_msg);
                    return false;
                }
                
                return true;
           });
        });
    };
    
    $.fn.validate.resetMark = function(obj)
    {
        if($.fn.validate.listOfBorders[obj.attr("id")] != null)
        {
            obj.css("border-color", $.fn.validate.listOfBorders[obj.attr("id")]);
        }
        
    }
    
    $.fn.validate.mark = function(obj)
    {
        $.fn.validate.listOfBorders[obj.attr("id")] = obj.css("border-color");
        obj.css("border-color", $.fn.validate.defaults.border_color);
    }
       
    $.fn.validate.showErrors = function(string_msg)
    {
        var error_box = $("<div>");
        error_box.addClass($.fn.validate.defaults.error_css);
        error_box
        .append("<h2>" + $.fn.validate.defaults.title_msg + "</h2>")
        .append("<p>" + $.fn.validate.defaults.error_msg + "</p>")
        .append("<div>" + string_msg + "</div>")
        .dialog({
            modal: true,
            width: 500,
            title: $.fn.validate.defaults.title_msg,
            resizable: false,
            buttons: {
                Ok: function() {
                    $(this).dialog("close");
                }
            }
        });
    }
    
    $.fn.validate.listOfBorders = new Array();
    
    $.fn.validate.defaults = {
          fields: new Array(),
          title_msg: "Input errors",
          error_msg: "Unable to submit data, because there are errors in filling out the following form:",
          border_color: "red",
          error_css: "error-log"
    };

})( jQuery );
