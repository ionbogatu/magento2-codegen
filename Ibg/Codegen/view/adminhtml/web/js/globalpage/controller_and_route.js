require([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/lib/spinner',
    'mage/translate'
], function ($, modal, loader, $t) {

    let $modal = $('#controller_and_route_modal');
    let $loader = loader.get('codegen.controller_and_route.form');

    $(document).ready(function(){

        if($modal.length === 1) {
            modal({
                'type': 'popup',
                'responsive': true,
                'innerScroll': false,
                'trigger': '.controller_and_route_target',
                'buttons': [
                    {
                        text: $t('Create Controller'),
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
        }
    });

    function bindEventOpenModal(){
        $('body').on('click', 'li[data-ui-id="menu-ibg-codegen-controller-and-route"]', function(){
            $modal.modal('openModal');
        });
    }

    function callAjax(modal){

    }
});