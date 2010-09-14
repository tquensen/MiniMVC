(function(jQuery) {
    var minimvcValidator = function() {

      var forms = minimvc.forms || [];

      jQuery.each(forms, function(formName, formData) {
          jQuery('#'+formName).bind('submit', function() {
              if (jQuery(this).find('.invalid').length > 0) {
                return false;
              }
          });
          jQuery.each(formData.elements, function(elemName, elem) {
                if (elem.validate !== true) {
                    return;
                }
                
                var elemId = '';
                if (elem.multiple === true) {
                    elemId= '[id^="' + formName + '__' + elemName + '"]';
                } else {
                    elemId = '#' + formName + '__' + elemName;
                }
                var wrapperId = elemId + '__wrapper';
                jQuery(elemId).bind('blur', function() {
                    var currentElem = $(this);
                    var elemValue = [];
                    if (elem.multiple === true) {
                        var currentElements = jQuery('[id^="' + formName + '__' + elemName + '"]');
                        if (elem.type == 'radio' || elem.type == 'checkboxGroup') {
                            currentElements = currentElements.filter(':checked');
                        }
                        currentElements.each(function(i, e) {
                           elemValue.push(e.val() || 'On');
                        });
                        if (elem.type == 'radio') {
                            elemValue = elemValue.shift() || null;
                        }
                    } else {
                        if (elem.type == 'checkbox') {
                            elemValue = (currentElem.filter(':checked').length > 0) ? currentElem.val() || 'On' : null;
                        } else {
                            elemValue = currentElem.val();
                        }
                    }
                    
                    $.ajax({
                        'cache': false,
                        'data': { '_validateForm': formName, '_validateField': elemName, '_validateValue': elemValue},
                        'dataType': 'json',
                        'type': 'POST',
                        'url': formData.url,
                        'success': function(data) {
                            if (typeof(data.status) == "undefined" || data.status == false) {
                                data.message = data.message || {};
                                var wrapper = jQuery('#'+wrapperId);
                                if (wrapper.length == 0) {
                                    return false;
                                }
                                wrapper.removeClass('valid').addClass('invalid');
                                var errorElement = wrapper.find('.formError');
                                if (errorElement.length > 0) {
                                    errorElement.html(data.message || '');
                                } else {
                                    wrapper.append('<span class="formError">'+data.message+'</span>');
                                }
                            } else {
                                var wrapper = jQuery('#'+wrapperId);
                                wrapper.removeClass('invalid').addClass('valid');
                                wrapper.find('.formError').remove();
                            }
                        }
                    });
                });
          });
      });
    }
})(jQuery);