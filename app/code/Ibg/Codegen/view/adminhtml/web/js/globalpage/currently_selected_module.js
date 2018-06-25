require([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/lib/spinner',
    'mage/translate'
], function($, modal, loader, $t){

    let $modal = $('#currently_selected_module_modal');
    let $loader = loader.get('codegen.create_or_select_module.form');
    let $modalMessageContainer = $modal.children('.messages');
    let $selectedModuleSuccess = $('.currently_selected_module_wrapper.success');
    let $selectedModuleWarning = $('.currently_selected_module_wrapper.warning');
    let $controllerAndRouteSuccess = $('.controller_and_route_wrapper.success');
    let actionButtonClicks = 0;

    $(document).ready(function(){

        if($modal.length === 1) {
            modal({
                'type': 'popup',
                'responsive': true,
                'innerScroll': false,
                'trigger': '.currently_selected_module_target',
                'buttons': [
                    {
                        text: $t('Create Module'),
                        class: 'action primary action-main-popup',

                        /** @inheritdoc */
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

            bindEventOpenModal();

            bindEventSwitchDestination();
        }
    });

    function bindEventOpenModal(){
        $('body').on('click', 'li[data-ui-id="menu-ibg-codegen-module"]', function(){
            $modalMessageContainer.html('');
            $modal.modal('openModal');
        });
    }

    function bindEventSwitchDestination(){

        let $actionButton = $modal.closest('.modal-inner-wrap').find('.action-main-popup');
        let $actionButtonText = $actionButton.find('span');

        $modal.find('.admin__fieldset-wrapper-content')
            .on('focus', '.admin__field-control > input, .admin__field-control > select', function(){

                let selector = '.admin__fieldset-wrapper-content';
                let $destination = $(this).closest(selector);
                let $destinationInput = $modal.find('input[name="destination"]');
                let $activeDestination = $(selector + '.active');

                if(!$destination.is($activeDestination)){
                    $activeDestination.removeClass('active');
                    $destination.addClass('active');
                }

                if($destination.data('destination-for') === 'create'){
                    $actionButtonText.text('Create Module');
                    $actionButton.removeClass('secondary').addClass('primary');
                    $destinationInput.val('create');
                }else if($destination.data('destination-for') === 'select'){
                    $actionButtonText.text('Select Module');
                    $actionButton.removeClass('primary').addClass('secondary');
                    $destinationInput.val('select');
                }
            }
        );
    }

    function callAjax(modal){
        let url = $modal.data('submit-url');
        let $destination = $modal.find('input[name="destination"]');
        let $moduleSelect = $('#codegen_module_name_select');

        let $moduleName = '';
        if($destination.val() === 'create'){
            $moduleName = $('#codegen_module_name_create');
        }else if($destination.val() === 'select'){
            $moduleName = $moduleSelect;
        }
        
        $.ajax({
            url: url,
            method: 'post',
            dataType: 'json',
            data: {
                destination: $destination.val(),
                module_name: $moduleName.val(),
                form_key: window.FORM_KEY
            },
            beforeSend: function(){
                $loader.show();
                actionButtonClicks++;
            },
            success: function(data, textStatus, jqXHR){
                let messageTemplate = '';
                if(data.success){
                    debugger;
                    $selectedModuleSuccess.removeClass('hidden');
                    $controllerAndRouteSuccess.removeClass('hidden');
                    $selectedModuleSuccess.find('.currently_selected_module_text').text($moduleName.val());
                    $selectedModuleWarning.addClass('hidden');

                    if($destination.val() === 'create'){
                        $moduleSelect.append('<option value="' + $moduleName.val() + '">' + $moduleName.val() + '</option>');
                    }

                    // add menu item
                    let $moduleMenuItem = $('li[data-ui-id="menu-ibg-codegen-module"]');
                    if(
                        typeof(data.menu) !== "undefined" &&
                        typeof(data.menu.controllerAndActionHtml) !== "undefined"
                    ){
                        $(data.menu.controllerAndActionHtml).insertAfter($moduleMenuItem);
                    }

                    // i don't need this message inside the modal anymore because the modal is closed instantly
                    // messageTemplate = '<div class="message message-success"><div>' + data.message + '</div></div>';
                    modal.closeModal();
                }else{
                    /*$selectedModuleSuccess.addClass('hidden');
                    $selectedModuleWarning.removeClass('hidden');*/
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
                $selectedModuleSuccess.addClass('hidden');
                $selectedModuleWarning.removeClass('hidden');
                let messageTemplate = '<div class="message message-error"><div>' + error + '</div></div>';
                $modalMessageContainer.append(messageTemplate);
            },
            complete: function(){
                $loader.hide();
                setTimeout(function(){
                    actionButtonClicks--;
                    if(!actionButtonClicks){
                        modal.closeModal();
                    }
                }, 3000);
            }
        });
    }
});