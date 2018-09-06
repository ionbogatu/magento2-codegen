define([
    'jquery',
    'Ibg_Codegen/js/components/uiComponent',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/lib/spinner',
    'Ibg_Codegen/js/globalpage/codegen-bindings',
    'ko',
    'uiRegistry'
], function($, Component, modal, loader, codegenBindings, ko, uiRegistry){

    let $modal = $('#currently_selected_module_modal');
    let $loader = loader.get('codegen.create_or_select_module.form');
    let $modalMessageContainer = $modal.find('.messages');

    return Component.extend({

        defaults: {
            url: $modal.data('submit-url'),
            currentActionText: ko.observable('Select Module')
        },

        initialize: function(){

            this._super();

            let self = this;

            this.showSuccessMessage = ko.observable(this.showSuccessMessage);
            this.moduleList = ko.observableArray(this.moduleList);

            this.moduleList.subscribe(function(newValue){
                newValue.sort();
            });

            $(document).ready(function(){

                if($modal.length === 1) {
                    modal({
                        'type': 'popup',
                        'responsive': true,
                        'innerScroll': false,
                        'trigger': '#nav li[data-ui-id="menu-ibg-codegen-module"]',
                        'buttons': [
                            {
                                text: 'Select Module',
                                class: 'action secondary action-main-popup',
                                click: function () {
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
                let parent = self.getParent();

                $.ajax({
                    url: self.url,
                    method: 'post',
                    dataType: 'json',
                    data: {
                        form_key: window.FORM_KEY,
                        destination: parent.currentAction(),
                        module_name: parent.currentModule()
                    },
                    beforeSend: function(){
                        $loader.show();
                    },
                    success: function(data){
                        let messageTemplate = '';
                        /*let $moduleSelect = $modal.find('#codegen_module_name_select');*/
                        let $messageSuccess = $('.currently_selected_module_wrapper.success');
                        let $messageWarning = $('.currently_selected_module_wrapper.warning');

                        if(data.success){
                            $messageSuccess.show();
                            $messageWarning.hide();
                            if(parent.currentAction() === 'create'){
                                // $moduleSelect.append('<option value="' + parent.currentModule() + '">' + parent.currentModule() + '</option>');
                                self.moduleList.push(parent.currentModule());
                            }
                            self.removeModuleFromSlider();
                            modal.closeModal();
                            self.showSuccessMessage(true);
                            /*parent.isDecisive(true);*/
                            uiRegistry.get('codegen.controller').showSuccessMessage(true);
                        }else{
                            if(Array.isArray(data.message)){
                                for(let i = 0; i < data.message.length; i++){
                                    messageTemplate += '<div class="message message-error"><div>' + data.message[i] + '</div></div>';
                                }
                            }else if(typeof(data.message) === "string"){
                                messageTemplate += '<div class="message message-error"><div>' + data.message + '</div></div>';
                            }
                        }

                        if(messageTemplate !== ''){
                            $modalMessageContainer.append(messageTemplate);
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
                $('body').on('click', '.currently_selected_module_target', function(){
                    $modal.data('modal').openModal();
                });
            }
        },

        changeModuleAction: function(action){
            let $modalMainAction = $modal.closest('.modal-popup').find('.action-main-popup');
            let parent = this.getParent();
            
            if(parent.currentAction() !== action){
                parent.currentAction(action);
                $modalMainAction.toggleClass('primary secondary');
                if(action === 'create'){
                    $modalMainAction.find('span').text('Create Module');
                }else if(action === 'select'){
                    $modalMainAction.find('span').text('Select Module');
                }
            }

            parent.currentModule($(event.currentTarget).val());
        },

        getParent: function () {
            return uiRegistry.get(this.parentName);
        }
    });
});
