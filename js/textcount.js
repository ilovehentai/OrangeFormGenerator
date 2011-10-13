(function( $ ){
 
    $.fn.textcount = function(options) {
  
        var _this = this;
        var opts = $.extend({}, $.fn.textcount.defaults, options);
        
        return this.each(function() {
            
            var o = $.meta ? $.extend({}, opts, _this.data()) : opts;
            var elem = $("<span>");
            
            var count_chars = function(){
                text_count = ($(_this).val()).length;
                text_count = o.maxchars - text_count;
                
                if(text_count > 0)
                {
                    elem.html(text_count + " " + o.label);
                }
                else
                {
                    $(_this).val(($(_this).val()).substr(0, o.maxchars));
                    elem.html(o.negation);
                }
            }
            
            var append_tag = function()
            {
                elem.html(o.maxchars + " " + o.label);
                $(_this).after(elem);
            }
            
            append_tag();
            count_chars();
            
            $(this).keypress(function(){
                count_chars();
            });
            
            $(this).keyup(function(){
                count_chars();
            });
       });
        
    };
    
    $.fn.textcount.defaults = {
          maxchars: 500,
          label: "caracteres left",
          negation: "no caracteres left"
    };

})( jQuery );
