(function( $ ){
 
    $.fn.validate = function(options) {
  
        var _this = this;
        var opts = $.extend({}, $.fn.validate.defaults, options);
        
        var element;
        
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
            "different" : function(word){
                return "[^" + word + "]";
            },
            "number" : function(size){
                var length = "";
                if(size !== null && size !== "" && size !== "~")
                {
                    var minus = size.match(/-[0-9]+/);
                    var plus = size.match(/\+[0-9]+/);
                    
                    if(minus !== null && minus.length > 0)
                    {
                        minus = minus[0].replace("-", "");
                    }
                    else if (plus === null)
                    {
                        minus = size;
                    }
                    
                    if(plus !== null && plus.length > 0)
                    {
                        plus =  plus[0].replace("+", "") + ((minus !== null) ? "," : "");
                    }
                    else
                    {
                        plus = "";
                    }
                    
                    length = "{" + plus + minus + "}";
                }
                return "[0-9]" + length;
            },
            "not_null" : /[^null]/,
            "nif" : /[0-9]{9}/,
            "zip_code" : /([1-9]{1}[0-9]{3}-[0-9]{3}|[1-9]{1}[0-9]{3})/,
            "min_size" : function(size){
                return "(.){" + size + ",}";
            },
            "match" : function(val){
                return ($("#" + val).val() !== "") ? $("#" + val).val() : "^$";
            },
            "date" : function(){
                var data = new Date(element.val());
                return (data === "Invalid Date") ? false : "(.*)";
            },
            "one_or_other" : function(val){
                
                var data = val.split(":");
                if($("#" + data[0]).val() !== "")
                {
                    return "(.*)";
                }
                else
                {
                    return eval("this." + data[1]);
                }
            },
            "not_empty_if" : function(val)
            {
                var data = val.split(":");
                if(element.val() === "" || ($("#" + data[0]).val()).match(eval("this." + data[1])))
                {
                    return "(.*)";
                }
                else
                {
                    return false;
                }
            },
            "empty_if_not" : function(val)
            {
                var data = val.split(":");
                if(element.val() === "")
                {
                    return "^$";
                }
                else if((element.val()).match(eval("this." + data + "()")))
                {
                    return "(.*)";
                }
                else
                {
                    return false;
                }
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
                $.each(o.fields, function(key, validators) {

                    if(validators.incase !== undefined)
                    {
                        if($("#" + val.incase).is(":checked"))
                        {
                            return true;
                        }
                    }
                        
                    key = $("#" + key);
                    element = key;
                    $.fn.validate.resetMark(key);
                    
                    var value = key.val();

                    var check = function(){

                        var valid = true;
                        if(validators.length > 0) {
                            
                            for(var i=0; i < validators.length; i++) {
                                var val = validators[i];
                                switch(val.validator)
                                {
                                    case 'checked':
                                        if(!key.is(":checked"))
                                        {
                                            valid = false;
                                        }
                                        break;
                                    default:
                                        var regex = null;
                                        if(val.validator.indexOf(":") > -1)
                                        {
                                            var valreg = (val.validator).substr((val.validator).indexOf(":") + 1, (val.validator).length);
                                            var validator = (val.validator).substr(0, (val.validator).indexOf(":"));

                                            regex = masks[validator](valreg);
                                        }
                                        else
                                        {
                                            if (typeof(masks[val.validator]) === "function") {
                                                regex = masks[val.validator]();
                                            }
                                            else
                                            {
                                                regex = masks[val.validator];
                                            }
                                        }

                                        if(regex === false || !value.match(regex))
                                        {
                                            valid = false;
                                        }
                                }

                                if(!valid)
                                {
                                    $.fn.validate.mark(key);
                                    string_msg += " - " + val.msg + "<br/>";
                                }

                            }
                        }
                    };
                    
                    if(value!==undefined)
                    {
                        check();
                    }
                });
                
                if(string_msg !== "")
                {
                    
                    $.fn.validate.showErrors(string_msg);
                    return false;
					
                } else if (typeof o.afterValidate === "function") {
				
					return o.afterValidate();
					
				}
                
                return true;
           });
        });
    };
    
    $.fn.validate.resetMark = function(obj)
    {
        if($.fn.validate.listOfBorders[obj.attr("id")] !== null)
        {
            obj.css("border-color", $.fn.validate.listOfBorders[obj.attr("id")]);
            obj.parent().removeClass("erro");
        }
        
    };
    
    $.fn.validate.mark = function(obj)
    {
        $.fn.validate.listOfBorders[obj.attr("id")] = obj.css("border-color");
        obj.css("border-color", $.fn.validate.defaults.border_color);
        obj.parent().addClass("erro");
    };
       
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
    };
    
    $.fn.validate.listOfBorders = new Array();
    
    $.fn.validate.defaults = {
          fields: new Array(),
          title_msg: "Input errors",
          error_msg: "Unable to submit data, because there are errors in filling out the following form:",
          border_color: "red",
          error_css: "error-log"
    };

})( jQuery );
