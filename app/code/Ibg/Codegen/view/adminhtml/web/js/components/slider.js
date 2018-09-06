define([
    'jquery',
    'ko'
], function($){

    return function(){

        $('.slider').not('.slider-active').each(function(){
            let $slider = $(this);
            let i = 1;
            /*let $header = $('<div class="slider-header"></div>');*/
            let $controllers = $('<hr/><ul class="controller-wrapper"></ul>');

            if($slider.children('div').length > 1) {
                $slider.children('div').each(function () {
                    let $child = $(this);
                    /*let $step = '';*/
                    let $controller = '';

                    if (i === 1) {
                        $child.addClass('slider-content step-' + i + ' active');
                        if($child.data('class')){
                            $child.addClass($child.data('class'));
                        }
                        /*$step = $('<div class="slider-step active" data-for="step-' + i + '">Step ' + i + ': ' + $child.data('title') + '</div>');*/
                        $controller = $('<li class="controller active" data-bind="click: changeSlide" data-for="step-' + i + '"></li>');
                    } else {
                        $child.addClass('slider-content step-' + i);
                        if($child.data('class')){
                            $child.addClass($child.data('class'));
                        }
                        /*$step = $('<div class="slider-step" data-for="step-' + i + '">Step ' + i + ': ' + $child.data('title') + '</div>');*/
                        $controller = $('<li class="controller" data-bind="click: changeSlide" data-for="step-' + i + '"></li>');
                    }

                    i++;

                    /*$header.append($step);*/
                    $controllers.append($controller);
                });

                /*$slider.prepend($header);*/
            }

            $slider.addClass('slider-active');
            $controllers.insertAfter($slider);
        });
    };
});
