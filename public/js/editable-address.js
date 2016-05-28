/**
Address editable input.
Internally value stored as {city: "Moscow", street: "Lenina", building: "15"}
@class address
@extends abstractinput
@final
@example
<a href="#" id="address" data-type="address" data-pk="1">awesome</a>
<script>
$(function(){
    $('#address').editable({
        url: '/post',
        title: 'Enter city, street and building #',
        value: {
            city: "Moscow", 
            street: "Lenina", 
            building: "15"
        }
    });
});
</script>
**/
(function ($) {
    "use strict";
    
    var Address = function (options) {
        this.init('address', options, Address.defaults);
    };

    //inherit from Abstract input
    $.fn.editableutils.inherit(Address, $.fn.editabletypes.abstractinput);

    $.extend(Address.prototype, {
        /**
        Renders input from tpl
        @method render() 
        **/        
        render: function() {
           this.$input = this.$tpl.find('input');
        },
        
        /**
        Default method to show value in element. Can be overwritten by display option.
        
        @method value2html(value, element) 
        **/
        value2html: function(value, element) {
            if(!value) {
                $(element).empty();
                return; 
            }
			
			var line1 = "";
			if (value.address != "") line1 += value.address + "\n";
			var line2 = "";
			if (value.city != "") line2 += value.city + ", ";
			if (value.province != "") line2 += value.province + ", ";
			if (value.country != "") line2 += value.province;
			if (line2 != "") line2 += "\n";
			var line3 = "";
			if (value.postal_code) line3 += value.postal_code + "\n";
			
            $(element).text(line1+line2+line3); 
        },
        
        /**
        Gets value from element's html
        
        @method html2value(html) 
        **/        
        html2value: function(html) {        
          /*
            you may write parsing method to get value by element's html
            e.g. "Moscow, st. Lenina, bld. 15" => {city: "Moscow", street: "Lenina", building: "15"}
            but for complex structures it's not recommended.
            Better set value directly via javascript, e.g. 
            editable({
                value: {
                    city: "Moscow", 
                    street: "Lenina", 
                    building: "15"
                }
            });
          */ 
          return null;  
        },
      
       /**
        Converts value to string. 
        It is used in internal comparing (not for sending to server).
        
        @method value2str(value)  
       **/
       value2str: function(value) {
           var str = '';
           if(value) {
               for(var k in value) {
                   str = str + k + ':' + value[k] + ';';  
               }
           }
           return str;
       }, 
       
       /*
        Converts string to value. Used for reading value from 'data-value' attribute.
        
        @method str2value(str)  
       */
       str2value: function(str) {
           /*
           this is mainly for parsing value defined in data-value attribute. 
           If you will always set value by javascript, no need to overwrite it
           */
           return str;
       },                
       
       /**
        Sets value of input.
        
        @method value2input(value) 
        @param {mixed} value
       **/         
       value2input: function(value) {
           if(!value) {
             return;
           }
           this.$input.filter('[name="address"]').val(value.address);
           this.$input.filter('[name="city"]').val(value.city);
           this.$input.filter('[name="province"]').val(value.province);
		   this.$input.filter('[name="country"]').val(value.country);
           this.$input.filter('[name="postal_code"]').val(value.postal_code);
       },       
       
       /**
        Returns value of input.
        
        @method input2value() 
       **/          
       input2value: function() { 
           return {
              address: this.$input.filter('[name="address"]').val(), 
              city: this.$input.filter('[name="city"]').val(), 
              province: this.$input.filter('[name="province"]').val(),
			  country: this.$input.filter('[name="country"]').val(), 
              postal_code: this.$input.filter('[name="postal_code"]').val()
           };
       },        
       
        /**
        Activates input: sets focus on the first field.
        
        @method activate() 
       **/        
       activate: function() {
            this.$input.filter('[name="address"]').focus();
       },  
       
       /**
        Attaches handler to submit form in case of 'showbuttons=false' mode
        
        @method autosubmit() 
       **/       
       autosubmit: function() {
           this.$input.keydown(function (e) {
                if (e.which === 13) {
                    $(this).closest('form').submit();
                }
           });
       }       
    });

    Address.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
        tpl: '<div class="editable-address"><label><span>Address: </span><input type="text" name="address" class="input-small"></label></div>'+
             '<div class="editable-address"><label><span>City: </span><input type="text" name="city" class="input-small"></label></div>'+
             '<div class="editable-address"><label><span>Province: </span><input type="text" name="province" class="input-small"></label></div>'+
			 '<div class="editable-address"><label><span>Country: </span><input type="text" name="country" class="input-small"></label></div>'+
             '<div class="editable-address"><label><span>Postal Code: </span><input type="text" name="postal_code" class="input-small"></label></div>',
             
        inputclass: ''
    });

    $.fn.editabletypes.address = Address;

}(window.jQuery));