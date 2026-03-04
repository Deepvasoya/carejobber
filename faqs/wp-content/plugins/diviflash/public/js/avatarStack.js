(($)=>{
    "use strict";
    $(window).on('load', function() {
        const list_avatar_stack = $('.difl_avatar_stack');

        list_avatar_stack.each(function(index, avatar_stack) {
            process_text_tag_level(index, avatar_stack);
            avatar_stack_tooltip(index, avatar_stack);
        });

        function avatar_stack_tooltip(index, avatar_stack) {
            const settings = $(avatar_stack).find('.difl_avatar_stack_container').data('settings');
            const avatar_stack_items = $(avatar_stack).find('.difl_avatar_stack_item');
            const tooltipStatus = settings.tooltip_enable;

            const ele_class = avatar_stack.classList.value.split(" ").filter(function(class_name){
                return class_name.indexOf('difl_avatar_stack_') !== -1;
            });

            if(avatar_stack_items.length > 0 && tooltipStatus){
                const options = {
                    arrow: settings.arrow,
                    animation: settings.animation,
                    placement: settings.placement,
                    trigger: settings.trigger,
                    allowHTML: true,
                    followCursor: 'mouseenter focus' === settings.trigger ? settings.followCursor: false,
                    interactive: settings.interactive,
                    interactiveBorder: parseInt(settings.interactiveBorder),
                    interactiveDebounce: parseInt(settings.interactiveDebounce),
                    maxWidth: parseInt(settings.maxWidth),
                    offset:[parseInt(settings.offsetSkidding) , parseInt(settings.offsetDistance)],
                    theme :`.${ele_class[0]}` , // for each module initiat , make different theme
                    //duration: 1000,
                    delay: parseInt(settings.delay),
                    // moveTransition: 'transform 2s ease-out',
                    //showOnCreate: true
                };
                avatar_stack_items.each(function(item_index, avatar_stack_item) {
                    const child_ele_class = avatar_stack_item.classList.value.split(" ").filter(function(class_name){
                        return class_name.indexOf('difl_avatar_stack_item_') !== -1;
                    });
                    const tooltipContent = df_avatar_stack[ele_class]['tooltip_content'][child_ele_class[0]];
                    if(tooltipContent === ''){
                        tippy(avatar_stack_item, options).disable();
                    }else{
                        options['content'] =  tooltipContent;
                        //tippy.disableAnimations();
                        tippy(avatar_stack_item, options);
                    }
                });
            }
        }

        function process_text_tag_level(index, avatar_stack) {
            const tag_attr = $(avatar_stack).find('.difl_avatar_stack_container').data('tag_attr');
            const text_info = $(avatar_stack).find('.difl_avatar_stack_item .difl_avatar_stack_text_container');
            if (text_info.length > 0) {
                text_info.each((index, text_container) => {
                    const title_field = $(text_container).find('.difl_avatar_stack_text_title').get(0);
                    if (title_field) {
                        const title_value = $(title_field).text();
                        const new_title_field = $('<' + tag_attr.title_tag + '>', {
                            class: 'difl_avatar_stack_text_title',
                            text: title_value
                        });
                        $(title_field).remove();
                        $(text_container).append(new_title_field);
                    }

                    const subtitle_field = $(text_container).find('.difl_avatar_stack_text_subtitle').get(0);
                    if (subtitle_field) {
                        const subtitle_value = $(subtitle_field).text();
                        const new_subtitle_field = $('<' + tag_attr.subtitle_tag + '>', {
                            class: 'difl_avatar_stack_text_subtitle',
                            text: subtitle_value
                        });
                        $(subtitle_field).remove();
                        $(text_container).append(new_subtitle_field);
                    }
                });
            }
        }
    });

})(jQuery)
