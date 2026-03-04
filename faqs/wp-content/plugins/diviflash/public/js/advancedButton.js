(($)=>{
    'use strict';
    $(window).on('load', function() {
        const list_advanced_button = $('.difl_advanced_button');
        list_advanced_button.each(function(index, advanced_button) {
            process_tooltip(index, advanced_button);
            const list_position_aware_bg = $(advanced_button).find(".dfab_position_aware_bg");
            $(advanced_button).on("mouseenter mouseleave", ".dfab_ripple_position_aware", (function (ripple_position_aware) {
                var ripple_position_aware_container = $(this).offset();
                var left = ripple_position_aware.pageX - ripple_position_aware_container.left;
                var top = ripple_position_aware.pageY - ripple_position_aware_container.top;
                list_position_aware_bg.css({
                    top: top,
                    left: left
                });
            }));
        });

        function process_tooltip(index, advanced_button) {
            const settings = $(advanced_button).find('.difl_advanced_button_container').data('settings');
            const tooltip_container = $(advanced_button).find('.difl_advanced_button_container');
            const tooltipStatus = settings.tooltip_enable;
	        const disableOnMobile = settings.disable_on_mobile && window.innerWidth < 768;
	        const ele_class = advanced_button.classList.value.split(" ").filter(function(class_name){
		        return class_name.indexOf('difl_advanced_button_') !== -1;
	        });

            if(tooltipStatus && !disableOnMobile){
                const options = {
                    arrow: settings.arrow,
                    animation: settings.animation,
                    placement: settings.placement,
                    trigger: settings.trigger,
                    allowHTML: true,
                    followCursor: false,
                    interactive: settings.interactive,
                    interactiveBorder: parseInt(settings.interactiveBorder),
                    maxWidth: parseInt(settings.maxWidth),
                    offset:[parseInt(settings.offsetSkidding) , parseInt(settings.offsetDistance)],
                    theme :`.${ele_class[0]}` , // for each module initiat , make different theme
                    //duration: 1000,
                    delay: [parseInt(settings.delay), parseInt(settings.interactiveDebounce)],
                    // moveTransition: 'transform 2s ease-out',
                    //showOnCreate: true
                };
                const tooltipContent = $(advanced_button).find('noscript').text();
                if(tooltipContent === ''){
                    tippy(tooltip_container[0], options).disable();
                }else{
                    options['content'] =  tooltipContent;
                    //tippy.disableAnimations();
                    tippy(tooltip_container[0], options);
                }
            }
        }
    });
})(jQuery)