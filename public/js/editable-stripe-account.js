/**
Stripe account editable input.
@class stripe_account
@extends abstractinput
@final
@example
<a href="#" id="stripe_account" data-type="stripe_account" data-pk="1">Empty</a>
<script>
$(function(){
    $('#stripe_account').editable({
        url: '/post',
        title: 'All the fields are mandatory #',
        value: {
            country: 'CA',

            first_name: '',
            last_name: '',
            email: '@',

            year: 1981,
            month: 7,
            day: 11,

            type: 'individual',  //individual / company

            address_country: 'CA',
            state: 'ON',
            city: '',
            line_1: '',
            line_2: '',
            postal_code: '',

            pii: '', //Personal identification number / SIN

            bank_country: 'CA',
            currency: 'cad',
            routing_number: '',
            account_number: '',
            account_holder_name: '',
            account_holder_type: 'individual' //individual / company
        }
    });
});
</script>
**/
(function ($) {
    "use strict";
    
    var StripeAccount = function (options) {
        this.init('stripe_account', options, StripeAccount.defaults);
    };

    //inherit from Abstract input
    $.fn.editableutils.inherit(StripeAccount, $.fn.editabletypes.abstractinput);

    $.extend(StripeAccount.prototype, {
        /**
        Renders input from tpl
        @method render() 
        **/        
        render: function() {
           this.$input = this.$tpl.find('input,select');
        },
        
        /**
        Default method to show value in element. Can be overwritten by display option.
        
        @method value2html(value, element) 
        **/
        value2html: function(value, element) {
            if(!value || !value.has_account) {
                $(element).empty();
                return; 
            }

            // Form preview data
            var lines = [
            ];

            if (!value.charges_enabled
            || !value.transfers_enabled) {
                lines.push(["Invalid financial information"]);
            }
            else {
                lines.push(["Valid financial information"]);
            }


            // Convert form data to HTML
            var html = lines.map(function(line) {
                return line.filter(function(word) {
                    return !!word;
                }).join(", ");
            }).filter(function(line) {
                return !!line;
            }).join("<br />");

            // Show the HTML
            $(element).html(html);
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
           if (value) {
               for (var k in value) {
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
           this.$input.filter('[name="country"]').val(value.country);
           this.$input.filter('[name="first_name"]').val(value.first_name);
           this.$input.filter('[name="last_name"]').val(value.last_name);
		   this.$input.filter('[name="email"]').val(value.email);
           this.$input.filter('[name="year"]').val(value.year);
           this.$input.filter('[name="month"]').val(value.month);
           this.$input.filter('[name="day"]').val(value.day);
           this.$input.filter('[name="type"]').val(value.type);
           this.$input.filter('[name="state"]').val(value.state);
           this.$input.filter('[name="city"]').val(value.city);
           this.$input.filter('[name="line_1"]').val(value.line_1);
           this.$input.filter('[name="line_2"]').val(value.line_2);
           this.$input.filter('[name="postal_code"]').val(value.postal_code);

           this.$input.filter('[name="bank_country"]').val(value.bank_country);
           this.$input.filter('[name="currency"]').val(value.currency);
           this.$input.filter('[name="routing_number"]').val(value.routing_number);
           this.$input.filter('[name="account_holder_name"]').val(value.account_holder_name);
           this.$input.filter('[name="account_holder_type"]').val(value.account_holder_type);

           this.$tpl.find('#representer_verification').addClass('verification_' + value.representer_verification);
           this.$tpl.find('#bank_account_verification').addClass('verification_' + value.bank_account_verification);
       },
       
       /**
        Returns value of input.
        
        @method input2value() 
       **/          
       input2value: function() { 
           return {
                country: this.$input.filter('[name="country"]').val(),
                first_name: this.$input.filter('[name="first_name"]').val(),
                last_name: this.$input.filter('[name="last_name"]').val(),
                email: this.$input.filter('[name="email"]').val(),
                year: this.$input.filter('[name="year"]').val(),
                month: this.$input.filter('[name="month"]').val(),
                day: this.$input.filter('[name="day"]').val(),
                type: this.$input.filter('[name="type"]').val(),
                state: this.$input.filter('[name="state"]').val(),
                city: this.$input.filter('[name="city"]').val(),
                line_1: this.$input.filter('[name="line_1"]').val(),
                line_2: this.$input.filter('[name="line_2"]').val(),
                postal_code: this.$input.filter('[name="postal_code"]').val(),

                pii: this.$input.filter('[name="pii"]').val(),
                bank_country: this.$input.filter('[name="bank_country"]').val(),
                currency: this.$input.filter('[name="currency"]').val(),
                routing_number: this.$input.filter('[name="routing_number"]').val(),
                account_number: this.$input.filter('[name="account_number"]').val(),
                account_holder_name: this.$input.filter('[name="account_holder_name"]').val(),
                account_holder_type: this.$input.filter('[name="account_holder_type"]').val()
           };
       },        
       
        /**
        Activates input: sets focus on the first field.
        
        @method activate() 
       **/        
       activate: function() {
            this.$input.filter('[name="stripe_account"]').focus();
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

    StripeAccount.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
        tpl:
			'<h4>Basic financial information</h4>'+
            '<div class="editable-stripe-account"><span>Account type: </span><select name="type" class="input-small"><option value="individual">Individual</option><option value="company">Company</option></select></div>'+
            '<div class="editable-stripe-account"><span>Country: </span><select name="country" class="input-small"><option value="CA">Canada</option><option value="US">United States of America</option></select></div>'+
            '<div class="editable-stripe-account"><span>Province / State: </span><input type="text" name="state" class="input-small"></div>'+
            '<div class="editable-stripe-account"><span>City: </span><input type="text" name="city" class="input-small"></div>'+
            '<div class="editable-stripe-account"><span>Line 1: </span><input type="text" name="line_1" class="input-small"></div>'+
            '<div class="editable-stripe-account"><span>Line 2: </span><input type="text" name="line_2" class="input-small"></div>'+
            '<div class="editable-stripe-account"><span>Postal Code: </span><input type="text" name="postal_code" class="input-small"></div>'+

            '<h4>Representer information</h4>'+
            '<div class="editable-stripe-account"><span>First name: </span><input type="text" name="first_name" class="input-small"></div>'+
            '<div class="editable-stripe-account"><span>Last name: </span><input type="text" name="last_name" class="input-small"></div>'+
            '<div class="editable-stripe-account"><span>Email: </span><input type="email" name="email" class="input-small"></div>'+
            '<div class="editable-stripe-account"><span>Birthday: </span>'+
                '<input type="number" name="year" placeholder="YYYY" min="1900" max="2010" class="input-small">/'+
                '<input type="number" name="month" placeholder="MM" min="1" max="12" class="input-small">/'+
                '<input type="number" name="day" placeholder="DD" min="1" max="31" class="input-small">'+
            '</div>'+
            '<div class="editable-stripe-account"><span>SIN number: </span><input type="text" name="pii" class="input-small"></div>'+

            '<h4>Bank account information</h4>'+
            '<div class="editable-stripe-account"><span>Country: </span><select name="bank_country" class="input-small"><option value="CA">Canada</option><option value="US">United States of America</option></select></div>'+
            '<div class="editable-stripe-account"><span>Currency: </span><select name="currency" class="input-small"><option value="cad">Canadian Dollars</option><option value="usd">US Dollars</option></select></select></div>'+
            '<div class="editable-stripe-account"><span>Acc. holder type: </span><select name="account_holder_type" class="input-small"><option value="individual">Individual</option><option value="company">Company</option></select></div>'+
            '<div class="editable-stripe-account"><span>Routing number: </span><input type="text" name="routing_number" class="input-small"></div>'+
            '<div class="editable-stripe-account"><span>Acc. number: </span><input type="text" name="account_number" class="input-small"></div>'+
            '<div class="editable-stripe-account"><span>Acc. holder name: </span><input type="text" name="account_holder_name" class="input-small"></div>',

        inputclass: ''
    });

    $.fn.editabletypes.stripe_account = StripeAccount;

}(window.jQuery));