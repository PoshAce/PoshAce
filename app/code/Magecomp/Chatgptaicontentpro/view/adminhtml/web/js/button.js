define([
    'Magento_Ui/js/form/components/button',
    'uiRegistry',
    'mage/url','mage/loader'
], function (Button, registry, urlBuilder,loader) {
    'use strict';
   

    return Button.extend({
        defaults: {
            title: 'Generate AI Content',
            error: '',
            displayArea: '',
            template: 'Magecomp_Chatgptaicontentpro/button',
            elementTmpl: 'Magecomp_Chatgptaicontentpro/button',
            modalName: null,
            actions: [{
                targetName: '${ $.name }',
                actionName: 'action'
            }],

        },

          /**
         * @abstract
         */
        onRender: function () {
        },
        /**
         * @abstract
         */
        hasAddons: function () {
            return false;
        },
        /**
         * @abstract
         */
        hasService: function () {
            return false;
        },
        action: function () {
            var data = new FormData();
            var promp1 = jQuery("input[name='product[name]']").val();

            var test = jQuery(".admin__action-multiselect").text();
            var test2 = jQuery.trim(test.replace( /[\s\n\r]+/g, ' ' ));
            var array = test2.split(' ');
            var promp2 = array[2];
                       
            var payload ={
                  'form_key': FORM_KEY,
                   'prompt': promp1 +','+promp2,
                  'type': this.settings.type,
                   'storeid': jQuery('#openai_storeid').val()
                };
           var result = true;

             jQuery.ajax({ showLoader: true,url: jQuery('#openai_url').val(),data: payload,type: 'POST',}).done(
                    function (response) {
                    if(response.type == 'meta_keywords'){
                      jQuery("textarea[name='product[meta_keyword]']").val(response.result).trigger('change');
                    }else if(response.type == 'meta_description'){
                      jQuery("textarea[name='product[meta_description]']").val(response.result).trigger('change');
                    }else if(response.type == 'meta_title'){
                       jQuery("input[name='product[meta_title]']").val(response.result).trigger('change');
                    }else if(response.type == 'short_description'){
                        tinymce.activeEditor.setContent(response.result); 
                        tinymce.activeEditor.save(); // This will save the content internally
                        jQuery(".mce-content-body").trigger('change');
                    }else if(response.type == 'description'){
                        tinymce.activeEditor.setContent(response.result); 
                        tinymce.activeEditor.save(); // This will save the content internally
                        jQuery(".mce-content-body").trigger('change');
                    }
                }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                    }
                );          
        }

    });
});
