require([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/lib/spinner',
    'mage/translate'
], function($, modal, loader, $t){

    let $modal = $('#currently_selected_module_modal');
    let $loader = loader.get('codegen.module_create_or_select.form');
    let $modalMessageContainer = $modal.children('.messages');

    $(document).ready(function(){

        modal({
            'type': 'popup',
            'responsive': true,
            'innerScroll': false,
            'trigger': '.show-modal',
            'buttons': [
                {
                    text: $t('Create Module'),
                    class: 'action primary action-main-popup',

                    /** @inheritdoc */
                    click: function () {

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
    });

    function bindEventOpenModal(){
        $('body').on('click', 'li[data-ui-id="menu-ibg-codegen-module"], .currently_selected_module_target', function(){
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
                    $destinationInput.val('create');
                }else if($destination.data('destination-for') === 'select'){
                    $actionButtonText.text('Select Module');
                    $destinationInput.val('select');
                }
            }
        );
    }

    function callAjax(modal){

        let url = $modal.data('submit-url');
        let $destination = $modal.find('input[name="destination"]');

        let $moduleName = '';
        if($destination.val() === 'create'){
            $moduleName = $('input[name=module_name_create]');
        }else if($destination.val() === 'select'){
            $moduleName = $('input[name=module_name_select]');
        }
        
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: {
                destination: $destination.val(),
                module_name: $moduleName.val(),
                form_key: window.FORM_KEY
            },
            beforeSend: function(){
                $loader.show();
            },
            success: function(data, textStatus, jqXHR){
                let messageTemplate = '<div class="message message-error"><div>' + error + '</div></div>';
                $modalMessageContainer.append(messageTemplate);
            },
            error: function(jqXHR, textStatus, error){
                let messageTemplate = '<div class="message message-error"><div>' + error + '</div></div>';
                $modalMessageContainer.append(messageTemplate);
            },
            complete: function(){
                $loader.hide();
                setTimeout(function(){
                    modal.closeModal();
                }, 3000);
            }
        });
    }
});