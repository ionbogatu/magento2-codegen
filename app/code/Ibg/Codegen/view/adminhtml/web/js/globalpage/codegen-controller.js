define([
    'jquery',
    'uiComponent',
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

            let self = this;

            this._super();

            this.showSuccessMessage = ko.observable(this.data.showSuccessMessage);

            $(document).ready(function(){

                if($modal.length === 1) {
                    modal({
                        'type': 'popup',
                        'responsive': true,
                        'innerScroll': false,
                        'trigger': '#nav li[data-ui-id="menu-ibg-codegen-controller"]',
                        'buttons': [
                            {
                                text: 'Next Step',
                                class: 'action primary action-main-popup next-step',
                                click: function(){

                                }
                            },
                            {
                                text: 'Create Controller',
                                class: 'action primary action-main-popup last-step display-none hidden',
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
                    bindClickNextStep();
                    bindCurrentModuleChange();
                }
            });

            function callAjax(modal){
                $.ajax({
                    url: self.url,
                    method: 'post',
                    dataType: 'json',
                    data: {
                        form_key: window.FORM_KEY,
                        destination: self.currentAction(),
                        module_name: self.currentModule()
                    },
                    beforeSend: function(){
                        $loader.show();
                    },
                    success: function(data){
                        let messageTemplate = '';
                        let $moduleSelect = $modal.find('#codegen_module_name_select');
                        let $messageSuccess = $('.currently_selected_module_wrapper.success');
                        let $messageWarning = $('.currently_selected_module_wrapper.warning');

                        if(data.success){
                            $messageSuccess.show();
                            $messageWarning.hide();
                            if(self.currentAction() === 'create'){
                                $moduleSelect.append('<option value="' + self.currentModule() + '">' + self.currentModule() + '</option>');
                            }
                            modal.closeModal();
                            self.showSuccessMessage(true);
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
                $('body').on('click', '.controller_target', function(){
                    $modal.data('modal').openModal();
                });
            }

            function bindClickNextStep(){
                $modal.closest('.modal-inner-wrap').find('.next-step').on('click', '', function(){
                    let $activeSliderContent = $modal.find('.slider-content.active');
                    let $next = $activeSliderContent.next('.slider-content');

                    if($next.length === 1){
                        $next.addClass('active');
                        $activeSliderContent.removeClass('active');

                        $next = $next.next('.slider-content');
                    }

                    if($next.length === 0){
                        $modal.closest('.modal-inner-wrap').find('.last-step').toggleClass('display-none hidden');
                        $modal.closest('.modal-inner-wrap').find('.next-step').toggleClass('display-none hidden');
                    }else{

                    }
                });
            }

            function bindCurrentModuleChange(){
                let parent = self.getParent();
                parent.currentModule.subscribe(function(newValue){
                    if(newValue.length){
                        $modal.closest('.modal-inner-wrap').find('.last-step').toggleClass('display-none hidden');
                        $modal.closest('.modal-inner-wrap').find('.next-step').toggleClass('display-none hidden');
                    }
                });
            }
        },
        
        getParent: function(){
            return uiRegistry.get(this.parentName);
        }
    });
});