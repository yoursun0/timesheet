/* 

jquery.updateWithJSON.js
by rebecca murphey
rmurphey gmail com

update form elements or other items
with content in a json string. for example:

$.getJSON({
  'someScriptThatReturnsJSON.php',
  someData,
  $.updateFromJSON(data)
});

* if you have multiple checkboxes with 
the same name attribute, or a select
that allows multiple selections, you must
pass the value(s) for the selected items
in an array:

var data = { field: ['value1','value2','value3'] }

*/

(function($){
    $.updateWithJSON = function(data){
    
        $.each(data, function(fieldName, fieldValue){
        
            // use id's where you can;
            // the second method of finding
            // the relevant element
            // will be much slower
            
            var $field = $('#' + fieldName);
            
            
            if ($field.length < 1) {
                $field = $('input,select,textarea').filter('[name="' + fieldName + '"]');
            }
            
            if ($field.eq(0).is('input')) {
            
                var type = $field.attr('type');
                
                switch (type) {
                
                    case 'checkbox':
                        if ($field.length > 1) {
                        
                            // more than one field matches
                            // that name, so expect the values
                            // to be passed in an array
                            
                            $field.each(function(){
                                var value = $(this).val();
                                if ($.inArray(value, fieldValue) != -1) {
                                    $(this).attr('checked', 'true');
                                }
                                else {
                                    $(this).attr('checked', '');
                                }
                            });
                            
                        }
                        else {
                        
                            if ($field.val() == fieldValue) {
                                $field.attr('checked', 'true');
                            }
                            else {
                                $field.attr('checked', '');
                            }
                            
                        }
                        
                        break;
                        
                    case 'radio':
                        $field.each(function(){
                            var value = $(this).val();
                            if (value == fieldValue) {
                                $(this).attr('checked', 'true');
                            }
                            else {
                                $(this).attr('checked', '');
                            }
                        });
                        break;
                        
                    default:
                        $field.val(fieldValue);
                        break;
                        
                }
                
            }
            else 
                if ($field.is('select')) {
                
                    var $options = $('option', $field);
                    var multiple = $field.attr('multiple');
                    
                    $options.each(function(){
                    
                        var value = $(this).val() || $(this).html();
                        
                        switch (multiple) {
                        
                            case true:
                                // multiple selections are allowed,
                                // so expect them to be passed in an array
                                
                                if ($.inArray(value, fieldValue) != -1) {
                                    $(this).attr('selected', 'true');
                                }
                                else {
                                    $(this).attr('selected', '');
                                }
                                break;
                                
                            default:
                                if (value == fieldValue) {
                                    $(this).attr('selected', 'true');
                                }
                                else {
                                    $(this).attr('selected', '');
                                }
                                break;
                        }
                        
                    });
                    
                }
                else {
                
                    $field.val(fieldValue);
                    
                }
            
        });
    }
    
})(jQuery);