var fbTemplate;
var formBuilder;
jQuery(document).ready(function($) {
    
    jQuery("#post").attr('novalidate','novalidate');

     var buildWrap = document.querySelector('.build-wrap'),
        renderWrap = document.querySelector('.render-wrap'),
        editing = true,
        fbOptions = {
            controlPosition: 'left',
            disableFields: ['autocomplete', 'button', 'access'],
            editOnAdd: true,
            dataType: 'xml',
            formData: tmpformData,
            disableHTMLLabels: true,
        };
    var isJson = false;
    try {
        var json = jQuery.parseJSON(tmpformData);
        isJson = true;
    } catch(err) {
        isJson = false;
    }
      if (isJson) {
        fbOptions.formData = tmpformData;
        fbOptions.dataType = 'json';
      }
    formBuilder = jQuery(buildWrap).formBuilder(fbOptions);

});