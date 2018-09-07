define([
    'jquery',
    'Ibg_Codegen/js/components/uiComponent',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/lib/spinner',
    'mage/translate',
    'uiRegistry'
], function ($, Component, modal, loader, $t, uiRegistry) {

    let $modal = $('#block_modal');
    let $loader = loader.get('codegen.block.form');
    let $modalMessageContainer = $modal.find('.messages');

    return Component.extend({

        defaults: {
            url: $modal.data('submit-url')
        },

        initialize: function () {

            this._super();

            let self = this;

            $(document).ready(function () {

                if ($modal.length === 1) {
                    modal({
                        'type': 'popup',
                        'responsive': true,
                        'innerScroll': false,
                        'trigger': '#nav li[data-ui-id="menu-ibg-codegen-block"]',
                        'buttons': [
                            {
                                text: $t('Create Block'),
                                class: 'action primary action-main-popup',
                                click: function () {
                                    $modalMessageContainer.html('');
                                    callAjax(this);
                                }
                            },
                            {
                                text: $t('Close'),
                                class: 'action secondary action-hide-popup',

                                /** @inheritdoc */
                                click: function () {
                                    this.closeModal();
                                }
                            }
                        ]
                    }, $modal);
                    $modal.removeClass('hidden');
                    $loader.hide();
                }

                function callAjax(modal) {
                    let $area = $modal.find('#codegen_area');
                    let $blockName = $modal.find('#codegen_block_name');

                    let postData = {
                        form_key: window.FORM_KEY,
                        area: $area.val(),
                        block_name: $blockName.val(),
                    };

                    let $destination = $modal.find('#destination');
                    if($destination.length === 1){
                        postData.destination = $destination.val();

                        if($destination.val() === 'create'){
                            postData.module_name = $modal.find('#codegen_module_name_create').val();
                        }else if($destination.val() === 'select'){
                            postData.module_name = $modal.find('#codegen_module_name_select').val();
                        }
                    }

                    $.ajax({
                        url: self.url,
                        method: 'post',
                        dataType: 'json',
                        data: postData,
                        beforeSend: function(){
                            $loader.show();
                        },
                        success: function(data){
                            if(data !== undefined) {
                                if (data.success) {
                                    if(postData.destination === 'create'){
                                        uiRegistry.get('codegen.module').moduleList.push(postData.module_name);
                                    }
                                    self.removeModuleFromSlider();
                                    modal.closeModal();
                                    uiRegistry.get('codegen.module').showSuccessMessage(true);
                                    uiRegistry.get('codegen.controller').showSuccessMessage(true);
                                } else {
                                    let messageTemplate = '<div class="message message-error"><div>' + data.message + '</div></div>';
                                    $modalMessageContainer.append(messageTemplate);
                                }
                            }
                        },
                        error: function(jqXHR, textStatus, error){
                            let messageTemplate = '<div class="message message-error"><div>' + error + '</div></div>';
                            $modalMessageContainer.append(messageTemplate);
                        },
                        complete: function(){
                            $loader.hide();
                        }
                    });
                }
            });
        },

        getParent: function(){
            return uiRegistry.get(this.parentName);
        },

        getBlockPath: function(){
            return '\\' + this.getParent().currentModule().replace('_', '\\') + '\\Block\\';
        }
    });
});
