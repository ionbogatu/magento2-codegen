define([
    'jquery',
    'Ibg_Codegen/js/components/uiComponent',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/lib/spinner',
    'Ibg_Codegen/js/globalpage/codegen-bindings',
    'ko',
    'uiRegistry'
], function($, Component, modal, loader, codegenBindings, ko, uiRegistry){

    let $modal = $('#controller_modal');
    let $loader = loader.get('codegen.controller.form');
    let $modalMessageContainer = $modal.find('.messages');

    return Component.extend({

        defaults: {
            url: $modal.data('submit-url')
        },

        initialize: function(){

            this._super();

            let self = this;

            this.showSuccessMessage = ko.observable(this.showSuccessMessage);

            $(document).ready(function(){

                if($modal.length === 1) {
                    modal({
                        'type': 'popup',
                        'responsive': true,
                        'innerScroll': false,
                        'trigger': '#nav li[data-ui-id="menu-ibg-codegen-controller"]',
                        'buttons': [
                            {
                                text: 'Create Controller',
                                class: 'action primary action-main-popup',
                                click: function(){
                                    $modalMessageContainer.html('');
                                    callAjax(this);
                                }
                            },
                            {
                                text: 'Close',
                                class: 'action secondary action-hide-popup',
                                click: function () {
                                    this.closeModal();
                                }
                            }
                        ]
                    }, $modal);
                    $modal.removeClass('hidden');

                    $loader.hide();

                    bindOpenModal();
                }
            });

            function callAjax(modal){
                let $area = $modal.find('#codegen_area');
                let $frontName = $modal.find('#codegen_front_name');
                let $controllerName = $modal.find('#codegen_controller_name');
                let $actionName = $modal.find('#codegen_action_name');

                let postData = {
                    form_key: window.FORM_KEY,
                    area: $area.val(),
                    front_name: $frontName.val(),
                    controller_name: $controllerName.val(),
                    action_name: $actionName.val(),
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
                        let $messageSuccess = $('.currently_selected_module_wrapper.success');
                        let $messageWarning = $('.currently_selected_module_wrapper.warning');

                        if(data !== undefined) {
                            if (data.success) {
                                $messageSuccess.show();
                                $messageWarning.hide();
                                if(postData.destination === 'create'){
                                    uiRegistry.get('codegen.module').moduleList.push(postData.module_name);
                                }
                                self.removeModuleFromSlider();
                                modal.closeModal();
                                self.showSuccessMessage(true);
                                uiRegistry.get('codegen.module').showSuccessMessage(true);
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

            function bindOpenModal(){
                $('body').on('click', '.controller_target', function(){
                    $modal.data('modal').openModal();
                });
            }
        },

        getParent: function(){
            return uiRegistry.get(this.parentName);
        }
    });
});
