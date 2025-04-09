define([
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/modal/modal',
    'uiRegistry',
    'jquery/jquery.cookie'
], function ($, ui, modal,registry) {
    'use strict';

    $.widget('mirasvit.feedPreview', {
        options: {
            url: null,
        },

        _create: function () {
            this.element
                .off('click.button')
                .on('click.button', $.proxy(this.preview, this));

            this._super();
        },

        preview: function () {
            var self = this;

            var modal = $('<div/>').modal({
                type: 'slide',
                title: $.mage.__('Feed Preview <sup>10 products</sup>'),
                modalClass: 'preview-aside',
                closeOnEscape: true,
                opened: function () {
                    $('body').trigger('processStart');

                    $(this).html(self.getIframe());

                    self.getForm().submit();

                    $('iframe').on('load', function() {
                        $('body').trigger('processStop');
                    });
                },
                closed: function () {
                    $('.preview-aside').remove();
                },

                buttons: [
                    {
                        text: $.mage.__('Open in new window'),
                        click: function (e) {
                            var win = window.open(self.options.url, '_blank');
                            win.focus();
                            modal.modal('closeModal');
                        }
                    },
                    {
                        text: $.mage.__('Reload'),
                        click: function (e) {
                            self.getForm().submit();
                        }
                    }
                ]
            });

            modal.modal('openModal');
        },

        getIframe: function () {
            return $('<iframe>')
                .attr('name', 'preview_iframe');
        },

        getQueryString: function(){
            const regex = /(\[([^\[\]]*?)\])/g;
            var data = registry.get('mst_feed_feed_form.mst_feed_feed_form_data_source').data;
            filterLegacyProperties(data);
                if (data.xml && $('[name="xml[schema]"]').val()) {
                    data.xml.schema = $('[name="xml[schema]"]').val();
                } else if (data.csv && $('.mst_feed_csv_data')) {
                    $('.mst_feed_csv_data:not(.arg-count, .delete-flag)').each(function ( index, element ) {
                        var name = $(this).attr("name");
                        name =  name.replaceAll(regex, (match, group1, group2) => {
                            return isNaN(group2) ? `.${group2}` : group1;
                        });
                        eval('data.'+name+'=$(this).val()')
                    })
                    if (data.csv.enclosure === undefined){
                        data.csv.enclosure = '';
                    }
                } else {
                    return '';
                }

            function filterLegacyProperties(obj) {
                $.each(obj, function(key, value) {
                    if ($.isArray(value)) {
                        obj[key] = $.grep(value, function(item) {
                            return item !== 'legacy-build.min.js';
                        });
                    } else if ($.isPlainObject(value)) {
                        filterLegacyProperties(value);
                    }
                });
            }

            function getPathToObj(obj, path = []) {
                let result = [];

                for (let key in obj) {
                    if (!obj.hasOwnProperty(key)) return;

                    let newPath = path.slice();
                    newPath.push(key);

                    let everyPath = [];
                    if (typeof obj[key] === "object") {
                        everyPath = getPathToObj(obj[key], newPath);
                    } else {
                        everyPath.push({
                            path: newPath,
                            val: obj[key]
                        });
                    }

                    if (everyPath) {
                        everyPath.map((item) => result.push(item))
                    }
                }

                return result;
            }

            function composeQueryString(paths) {
                let result = "";
                paths.map((item) => {
                    let pathString = "";
                    if (item.path.length > 1) {
                        pathString = item.path.reduce((a, b, index) => {
                            return a + '['+ b +']';
                        })
                    } else {
                        pathString = item.path[0];
                    }

                    if (result) {
                        pathString = "&" + pathString + '=' + item.val;
                    } else {
                        pathString = pathString + '=' + item.val;
                    }

                    result += pathString;
                });

                return result;
            }

            const str = composeQueryString(getPathToObj(data));
            return encodeURI(str);
        },


        getForm: function () {
            $("[target=preview_iframe]").remove();
            var previewIds = $.cookie("feed_preview_ids");

            var $form = $('<form/>')
                .attr('action', this.options.url)
                .attr('method', 'post')
                .attr('target', 'preview_iframe')
                .css('display', 'none');

            $form.append($('<textarea>')
                .attr('name', 'data')
                .text(this.getQueryString()));

            $form.append($('<input>')
                .attr('name', 'preview_ids')
                .val(previewIds));

            $('body').append($form);

            return $form;
        }
    });

    return $.mirasvit.feedPreview;
});
