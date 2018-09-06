define([
    'jquery',
    'uiComponent',
    'ko'
], function($, Component, ko){
    "use strict";

    return Component.extend({
        defaults: {
            currentModule: ko.observable(''),
            currentAction: ko.observable('select')/*,
            isDecisive: ko.observable(false)*/
        },

        initialize: function() {
            this._super();

            this.currentModule = ko.observable(this.currentModule);
            /*this.isDecisive = ko.observable(this.isDecisive);*/
        },

        changeSlide: function(element, event){
            let $element = $(event.target);
            let $modal = $element.closest('.codegen_modal');
            let step = $element.data('for');

            // switch header
            $modal.find('.slider-header .slider-step.active').removeClass('active');
            $modal.find('.slider-header .slider-step[data-for="' + step + '"]').addClass('active');

            // switch content
            $modal.find('.slider-content.active').removeClass('active');
            $modal.find('.slider-content.' + step).addClass('active');

            // switch controller
            $element.siblings('.active').removeClass('active');
            $element.addClass('active');
        }
    });
});
