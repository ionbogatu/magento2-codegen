require([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/lib/spinner',
    'mage/translate'
], function ($, modal, loader, $t) {

    let $modal = $('#helper_modal');
    let $loader = loader.get('codegen.helper.form');

    $(document).ready(function(){

        if($modal.length === 1) {
            modal({
                'type': 'popup',
                'responsive': true,
                'innerScroll': false,
                'trigger': '.helper_target',
                'buttons': [
                    {
                        text: $t('Create Helper'),
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
        $('body').on('click', 'li[data-ui-id="menu-ibg-codegen-helper"]', function(){
            $modal.modal('openModal');
        });
    }

    function callAjax(modal){

    }
});