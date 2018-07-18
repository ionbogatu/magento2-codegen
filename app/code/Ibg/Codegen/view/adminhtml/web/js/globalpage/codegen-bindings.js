define([
    'jquery',
    'uiComponent',
    'ko'
], function($, Component, ko){
    "use strict";

    return Component.extend({
        defaults: {
            currentModule: ko.observable(''),
            currentAction: ko.observable('select'),
            isDecisive: ko.observable(false)
        },

        initialize: function() {
            this._super();

            this.currentModule = ko.observable(this.currentModule);
            this.isDecisive = ko.observable(this.isDecisive);
        }
    });
});