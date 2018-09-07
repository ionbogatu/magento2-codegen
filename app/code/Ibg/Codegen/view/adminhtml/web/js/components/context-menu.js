define([
    'jquery',
    'uiComponent',
    'ko'
], function($, Component, ko){

    return Component.extend({

        defaults: {
            items: ko.observableArray([
                {
                    title: 'Add Block',
                    callback: 'addBlock'
                },
                {
                    title: 'Add Block Here',
                    callback: 'addBlockHere'
                },
                {
                    title: 'Edit this block',
                    callback: 'editThisBlock'
                }
            ])
        },

        initialize: function() {

            this._super();

            $(document).ready(function(){

                let $body = $('body');
                let $contextMenuWrapper = $('#context-menu-wrapper');

                $body.on('contextmenu', function(e){

                    let position = {top: e.pageY - 10, left: e.pageX - $contextMenuWrapper.width() - 11};

                    $contextMenuWrapper.offset(position);
                });

                $body.on('mouseleave', '#context-menu-wrapper', function(){
                    $contextMenuWrapper.offset({top: -10000, left: -10000});
                });

                $contextMenuWrapper.on('click', 'li', function(fnString){
                    if(fnString === 'addBlock'){
                        addBlock();
                    } else if(fnString === 'addBlockHere') {
                        addBlockHere();
                    } else if(fnString === 'editThisBlock') {
                        editThisBlock();
                    }
                });
            });
        }
    });
});
