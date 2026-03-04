(($) => {
  "use strict";
  let lastScrollTop = 0;
  let scrollDirectionGlobal;
  function detectScrollDirection() {


    const currentScrollTop = $(window).scrollTop();

    if (currentScrollTop > lastScrollTop) {
      lastScrollTop = currentScrollTop <= 0 ? 0 : currentScrollTop;
      scrollDirectionGlobal = "down"
      return "down";
    } else if (currentScrollTop < lastScrollTop) {
      lastScrollTop = currentScrollTop <= 0 ? 0 : currentScrollTop;
      scrollDirectionGlobal = "up"
      return "up";
    }
  }

  function getScrollDistance(ele) {
    let lastScrollPos = parseInt(ele.dataset.lastScrollPos);
    const currentScrollPos = window.scrollY || document.documentElement.scrollTop;
    const scrollDistance = currentScrollPos - lastScrollPos;

    
    lastScrollPos = currentScrollPos;
    ele.dataset.lastScrollPos = lastScrollPos;

    return scrollDistance;
  }

  function initTextReveal(ele) {

    const container = ele.querySelector('.df_text_reveal_main_container');

    if (container) {
      const settings = JSON.parse(container.getAttribute('data-settings'));

      if (!ele.dataset.targetedChunk) {
        ele.dataset.targetedChunk = 0
      }
      if (!ele.dataset.lastScrollPos) {
        ele.dataset.lastScrollPos = 0
      }
      initializeSpans(ele, settings);

      const classesWithScroll = [
        '.with-scroll.df_text_reveal_by__dual_color_animation',
        '.with-scroll.df_text_reveal_by_opacity_animationr'
      ];

      const classesAuto = [
        '.auto.df_text_reveal_by_opacity_animationr',
        '.auto.df_text_reveal_by__dual_color_animation'
      ];

      const classesOnViewport = [
        '.on-viewport.df_text_reveal_by_opacity_animationr',
        '.on-viewport.df_text_reveal_by__dual_color_animation'
      ];

      if (classesWithScroll.some(selector => ele.querySelector(selector))) {
        window.addEventListener('scroll', () => handleAnimationOnScroll(ele, settings));
      } else if (classesAuto.some(selector => ele.querySelector(selector))) {
        handleBasicAnimation(ele, settings);
      } else if (classesOnViewport.some(selector => ele.querySelector(selector))) {
        window.addEventListener('scroll', () => handleAnimationOnViewPort(ele, settings));
      }

    }
  }

  function initializeSpans(ele, settings) {

    const container = ele.querySelector('.df_text_reveal_main_container');

    if (container) {
      function wrapTextNodes(node) {
        if (node.nodeType === Node.TEXT_NODE) {
          const text = node.nodeValue;
          const parent = node.parentNode;
          let initialOpacity = parseFloat(settings.settings__reveal_initial_opacity)
          let chunk
          if (text.trim()) {

            // if text split by word by word 
            if (ele.querySelector('.df_text_reveal_word_by_word')) {
              chunk = text.split(' ');

            }
            // if text split by letter by letter
            else if (ele.querySelector('.df_text_reveal_letter_by_letter')) {
              chunk = text.split('');
            }


            // chunk = chunk.map(e => `<span class="df_inner" style="filter:blur(${(10-initialOpacity*10)/2}px); opacity:${initialOpacity}">${e}</span>`)
            chunk = chunk.map(e => `<span class="df_inner" style=" opacity:${initialOpacity}">${e}</span>`)

            const tempDiv = document.createElement('div');
            if (ele.querySelector('.df_text_reveal_word_by_word')) {
              tempDiv.innerHTML = chunk.join(' ');
            } else if (ele.querySelector('.df_text_reveal_letter_by_letter')) {
              tempDiv.innerHTML = chunk.join('');
            }

            while (tempDiv.firstChild) {
              parent.insertBefore(tempDiv.firstChild, node);
            }

            parent.removeChild(node);
          }
        } else if (node.nodeType === Node.ELEMENT_NODE) {
          Array.from(node.childNodes).forEach(childNode => {
            wrapTextNodes(childNode);
          });
        }
      }

      wrapTextNodes(container);
    }
  }

  function processOffsetValue(offsetValue) {
    let offset;
    if (typeof offsetValue === 'string' && offsetValue.match('%')) {
      offset = ($(window).height() * (parseFloat(offsetValue) / 100));
    }
    else if (typeof offsetValue === 'string' && offsetValue.match('px')) {
      offset = parseFloat(offsetValue);
    } else {
      offset = ($(window).height() * (parseFloat(offsetValue) / 100));
    }
    return offset
  }

  function isInViewport(ele, settings) {

    const rect = ele.getBoundingClientRect();
    const offsetValueTop = settings.settings__reveal_viewport_offset_value_top == '' ? 0 : settings.settings__reveal_viewport_offset_value_top;
    const offsetValueBottom = settings.settings__reveal_viewport_offset_value_bottom == '' ? 0 : settings.settings__reveal_viewport_offset_value_bottom;

    const isVerticallyInView = rect.top < ($(window).height() - processOffsetValue(offsetValueBottom)) && rect.bottom > processOffsetValue(offsetValueTop);
    const isHorizontallyInView = rect.left < $(window).width() && rect.right > 0;

    return isVerticallyInView && isHorizontallyInView;
  }
  function isInFullyViewport(ele, settings) {
    const rect = ele.getBoundingClientRect();
    const offsetValueTop = settings.settings__reveal_viewport_offset_value_top || 0;
    const offsetValueBottom = settings.settings__reveal_viewport_offset_value_bottom || 0;

    const isVerticallyFullyInView =
      rect.top >= processOffsetValue(offsetValueTop) &&
      rect.bottom <= ($(window).height() - processOffsetValue(offsetValueBottom));

    const isHorizontallyFullyInView =
      rect.left >= 0 &&
      rect.right <= $(window).width();

    return isVerticallyFullyInView && isHorizontallyFullyInView;
  }

  function chunkGeneration(ele, selector) {
    return ele.querySelectorAll(`${selector} span.df_inner`)
  }
  function handleScrollSpeed(chunks, elementTotalWidth, settings) {
    const offsetValueTop = settings.settings__reveal_viewport_offset_value_top == '' ? 0 : settings.settings__reveal_viewport_offset_value_top;
    const offsetValueBottom = settings.settings__reveal_viewport_offset_value_bottom == '' ? 0 : settings.settings__reveal_viewport_offset_value_bottom;

    let viewport = ($(window).height() - (processOffsetValue(offsetValueBottom) + processOffsetValue(offsetValueTop)))
    let initialOpacity = parseFloat(settings.settings__reveal_initial_opacity)
    let totalReqOpacity = (chunks.length * (1 - initialOpacity));

    let speed = ((totalReqOpacity / viewport));

    return speed;
  }

  function handleBasicAnimation(ele, settings) {

    let delay = parseInt(settings.settings__reveal_delay);
    let duration = parseInt(settings.settings__reveal_duration)

    const elements = ele.querySelectorAll('span.df_inner');
    let targetedChunk = parseInt(ele.dataset.targetedChunk) || 0;
    let chunk_duration = duration / elements.length;
    setTimeout(() => {
      let intervalId;
      try {
        intervalId = setInterval(() => {
          if (targetedChunk < elements.length) {
            const element = elements[targetedChunk];
            element.style.transition = `all ${chunk_duration}ms`;
            element.style.opacity = '1';

            if (ele.querySelector('.df_text_reveal_by__dual_color_animation')) {
              let secondaryColor = `var( --secondary-reveal-color )`;
              element.style.color = secondaryColor
            }

            targetedChunk += 1;
          } else {
            clearInterval(intervalId);
          }
        }, chunk_duration);
      } catch (error) {
        clearInterval(intervalId);
        console.error(error);
      }
    }, delay);
  }
  function handleAnimationOnViewPort(ele, settings) {
    let delay = parseInt(settings.settings__reveal_delay);
    let duration = parseInt(settings.settings__reveal_duration)
    let targetedChunk = parseInt(ele.dataset.targetedChunk) || 0;
    let smallChunkList = chunkGeneration(ele, '.df_text_reveal_main_container')

    let chunk_duration = duration / smallChunkList.length;

    let initialOpacity = parseFloat(settings.settings__reveal_initial_opacity)
    let intervalId;


    //check and add class if this element is visible
    if (isInViewport(ele, settings)) {
      if (!ele.classList.contains('--df-scrolling-text-reveal-element-visible')) {
        ele.classList.add('--df-scrolling-text-reveal-element-visible')
      }
    }
    else {
      if (ele.classList.contains('--df-scrolling-text-reveal-element-visible')) {
        if (ele.classList.contains('--df-scrolling-text-reveal-element-visible')) {
          ele.classList.remove('--df-scrolling-text-reveal-element-visible')
        }
      }
    }

    if (ele.classList.contains('--df-scrolling-text-reveal-element-visible')) {

      setTimeout(() => {

        try {

          intervalId = setInterval(() => {
            if (targetedChunk < smallChunkList.length && isInViewport(ele, settings)) {
              const chunk = smallChunkList[targetedChunk];

              if (!isInViewport(chunk, settings) && chunk.style.opacity < 1) {
                chunk.style.opacity = '1';
                if (ele.querySelector('.df_text_reveal_by__dual_color_animation')) {
                  let secondaryColor = `var( --secondary-reveal-color )`;
                  chunk.style.color = secondaryColor
                }
                targetedChunk += 1;
              }

              chunk.style.transition = `all ${chunk_duration}ms`;
              chunk.style.opacity = '1';
              if (ele.querySelector('.df_text_reveal_by__dual_color_animation')) {
                let secondaryColor = `var( --secondary-reveal-color )`;
                chunk.style.color = secondaryColor
              }
              targetedChunk += 1;
            } else {
              ele.dataset.targetedChunk = targetedChunk
              clearInterval(intervalId);
            }
          }, chunk_duration);

        } catch (error) {
          console.error(error);
        }
      }, delay);
    } else {
      clearInterval(intervalId)
      
      if (smallChunkList[0].style.opacity > initialOpacity) {

        smallChunkList.forEach(e => {
          e.style.opacity = initialOpacity
          e.style.color = 'unset'
        })
      }
      ele.dataset.targetedChunk = 0

    }

  }
  function handleAnimationOnScroll(ele, settings) {
    let initialOpacity = parseFloat(settings.settings__reveal_initial_opacity)
    let scrollAmount = getScrollDistance(ele);


    //get Targeted Value
    let targetedChunk = parseInt(ele.dataset.targetedChunk)
    let smallChunkList = chunkGeneration(ele, '.df_text_reveal_main_container')

    detectScrollDirection()

    //handle fastest go down event 
    if (($(ele).offset().top + $(ele).height()) < $(window).scrollTop()) {
      smallChunkList.forEach(e => e.style.opacity = '1')
      targetedChunk = smallChunkList.length - 1;
      ele.dataset.targetedChunk = targetedChunk
    }


    //handle fastest go up even
    if (($(window).height() + $(window).scrollTop()) < $(ele).offset().top) {
      smallChunkList.forEach(e => e.style.opacity = initialOpacity)
      targetedChunk = 0;
      ele.dataset.targetedChunk = targetedChunk
    }

    if (isInViewport(ele, settings)) {

      let incrementRate = handleScrollSpeed(smallChunkList, $(ele).width(), settings)


      if (scrollDirectionGlobal === "down") {

        while (scrollAmount > 0 && smallChunkList[targetedChunk] && isInFullyViewport(smallChunkList[targetedChunk], settings)) {

          let currentSpan = smallChunkList[targetedChunk]
          let currentOpacity = parseFloat(currentSpan.style.opacity);


          if (currentOpacity < 1 && isInViewport(currentSpan, settings)) {
            let carry_opacity = incrementRate-(1-currentOpacity)

            // increase the opacity 
            let newOpacity = Math.min((currentOpacity + incrementRate), 1)
            currentSpan.style.opacity = newOpacity

            console.table({carry_opacity,currentSpan })

            while (carry_opacity > 0 && smallChunkList[targetedChunk+1] ) {
              let nextSpan = smallChunkList[targetedChunk + 1]
              let nextSpanCurrentOpacity = parseFloat(nextSpan.style.opacity)

              let newOpacity = Math.min((nextSpanCurrentOpacity + carry_opacity), 1)
              nextSpan.style.opacity = newOpacity

              targetedChunk++;

              // update the targetedChunk value 
              ele.dataset.targetedChunk = targetedChunk
              
              //update the carry value
              carry_opacity = Math.max(carry_opacity - (1 - nextSpanCurrentOpacity),0)
            }



            //check is it a dual color animation or not 
            if (ele.querySelector('.df_text_reveal_by__dual_color_animation')) {
              let secondaryColor = `var( --secondary-reveal-color )`;
              currentSpan.style.color = secondaryColor
            }

            scrollAmount = Math.max(scrollAmount - 1, 0);
          }
          else if (1 == currentOpacity) {
            scrollAmount = scrollAmount - 1
            if (targetedChunk < smallChunkList.length - 1) {
              targetedChunk = targetedChunk + 1
              ele.dataset.targetedChunk = targetedChunk
            }
          }
          else {
            scrollAmount = Math.max(scrollAmount - 1, 0);
          }

        }
      }
      else if (scrollDirectionGlobal == "up") {

        scrollAmount = scrollAmount * -1

        while (scrollAmount > 0 && smallChunkList[targetedChunk]) {

          let currentSpan = smallChunkList[targetedChunk]
          let currentOpacity = parseFloat(currentSpan.style.opacity);

          if (initialOpacity < currentOpacity && currentOpacity <= 1) {
            let newOpacity = Math.max((currentOpacity - incrementRate), initialOpacity)
            let carry_opacity = incrementRate-(currentOpacity-initialOpacity);
            currentSpan.style.opacity = newOpacity
            currentSpan.style.color = 'unset'

            while (carry_opacity > 0 && smallChunkList[targetedChunk-1] ) {
              let prvSpan = smallChunkList[targetedChunk - 1]
              let prvSpanCurrentOpacity = parseFloat(prvSpan.style.opacity)

              let newOpacity = Math.max((currentOpacity - carry_opacity), initialOpacity)
              prvSpan.style.opacity = newOpacity

              targetedChunk--;

              // update the targetedChunk value 
              ele.dataset.targetedChunk = targetedChunk
              
              //update the carry value
              carry_opacity = Math.max(carry_opacity - ( prvSpanCurrentOpacity-initialOpacity),0)
            }


            scrollAmount = Math.max(scrollAmount - 1, 0);

          }
          else if (initialOpacity == currentOpacity) {
            scrollAmount = Math.max(scrollAmount - 1, 0);
            targetedChunk = Math.max(targetedChunk - 1, 0)
            ele.dataset.targetedChunk = targetedChunk
          }
        }
      }
    }

  }

  // $(window).on('load', function () {
    $('.difl_text_reveal').each(function (index, ele) {

      initTextReveal(ele)

    });
  // });
})(jQuery);
