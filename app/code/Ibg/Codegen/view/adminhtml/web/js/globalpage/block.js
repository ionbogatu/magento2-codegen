require([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/lib/spinner',
    'mage/translate'
], function ($, modal, loader, $t) {

    let $modal = $('#block_modal');
    let $loader = loader.get('codegen.block.form');

    $(document).ready(function(){

        if($modal.length === 1) {
            modal({
                'type': 'popup',
                'responsive': true,
                'innerScroll': false,
                'trigger': '.block_target',
                'buttons': [
                    {
                        text: $t('Create Block'),
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

            bindEventToggleArea();
        }
    });

    function bindEventOpenModal(){
        $('body').on('click', 'li[data-ui-id="menu-ibg-codegen-block"]', function(){
            $modal.modal('openModal');
        });
    }

    function bindEventToggleArea(){

        $modal.on('change', '#block_area', function(){

            let $self = $(this);
            let $area = $('.absolute_path .area');

            let relativeAreaPath = ($self.val() !== 'frontend') ? $self.val() : '';
            $area.text(relativeAreaPath);
        });
    }

    function callAjax(modal){

    }
});