define([
    'uiComponent',
    'uiRegistry',
    'Ibg_Codegen/js/components/slider',
    'jquery'
], function(Component, uiRegistry, slider, $){

    return Component.extend({

        initialize: function(){

            this._super();

            slider(this);
        },

        removeModuleFromSlider: function(){
            $('.slider').each(function(){
                let $slider = $(this);
                let $element = $slider.find('.slider-content[data-class="module"]');

                if($element.length) {
                    let step = $element.attr('class').match(/(step-\d)/);

                    if (step[1] !== undefined) {
                        let $controller = $slider.siblings('.controller-wrapper').find('li[data-for="' + step[0] + '"]');
                        if ($controller.hasClass('active')) {
                            $controller.next().addClass('active');
                        }
                        $controller.remove();

                        let $controllerWrapper = $slider.siblings('.controller-wrapper');
                        if($controllerWrapper.find('li').length < 2){
                            $controllerWrapper.remove();
                        }
                    }

                    $element.next('.slider-content').addClass('active');
                    $element.remove();
                }
            });
        }
    });
});
