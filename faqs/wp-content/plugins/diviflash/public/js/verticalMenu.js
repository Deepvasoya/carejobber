(function($) {
    const dfVerticalMenuScripts = {
        isTouchDevice: "ontouchstart" in document.documentElement,
        init: function() {
            $(".df_vertical_menu_main_container .df-vertical-sub-menu").hide();
            this.subMenuReveal();
            this.appendExtraDivForEnableHoverEffect();
            this.verticalCustomMegaMenuColumn();
            this.clickInnerSubmenu();
            this.hamburgerRevealOnClick();
            this.isTouchDevice || this.hamburgerRevealOnHover();
            this.isTouchDevice && this.hamburgerRevealOnTouch();
            this.isTouchDevice || this.hoverOuterSubmenu();
            this.isTouchDevice && this.touchOuterSubmenu();
        },
        appendExtraDivForEnableHoverEffect: function() {
            $(
                ".df_vertical_menu_main_container.df-vertical-has-item-animation a"
            ).each(function(i, ele) {
                let newSpan = $(
                    '<span class="df_vertical_border_hover_effect"></span>'
                );
                $(this).append(newSpan);
            });
        },
        setMegaMenuDynamicPosition: function(ele) {
            $subMenu = ele.children(
                ".df-vertical-sub-menu.df-vertical-mega-menu-item"
            );

            if ($subMenu.length) {
                $subMenu.css("display", "flex");
                $subMenu.css("opacity", "0");
                const wrapperTop = ele.closest(".df-vertical-menu-nav").offset()
                    .top;

                const activeElementBottom =
                    ele.offset().top + ele.outerHeight();
                const subMenuHeight = $subMenu[0].getBoundingClientRect()
                    .height;
                const distance = activeElementBottom - wrapperTop;

                if (distance > subMenuHeight) {
                    ele.css("position", "relative");
                    $subMenu.css("bottom", "0");
                    $subMenu.css("top", "auto");
                }

                $subMenu.css("display", "none");
                $subMenu.css("opacity", "1");
            }
        },
        verticalMegaMenu: function($obj) {
            if ($obj.hasClass("df-vertical-mega-menu")) {
                let containerOffsetLeft = $obj.offset()?.left;

                const _dataSet = $obj[0].dataset;

                const elementWidth = $obj.outerWidth();
                const occupaidWidth = elementWidth + containerOffsetLeft;

                let _width = 0;

                if (_dataSet.width === "custom_width") {
                    _width = _dataSet.widthValue
                        ? parseInt(_dataSet.widthValue)
                        : "270";
                } else if (_dataSet.width == "full_width") {
                    const fullScreenSize = $(window).width();

                    _width = fullScreenSize - occupaidWidth - 20;
                    if (
                        $obj.closest(".df-vertical-sub-menu-reveal-left")
                            .length > 0
                    ) {
                        _width = containerOffsetLeft;
                    }
                } else {
                    const parentContainerWidth = $obj
                        .closest(".et_pb_row")
                        .outerWidth();
                    _width = parentContainerWidth - elementWidth;
                }

                $obj.find(">.df-vertical-sub-menu").css("width", _width);
            }
        },
        subMenuReveal: function() {
            $(".df-vertical-mega-menu > .df-vertical-sub-menu").addClass(
                "df-vertical-mega-menu-item"
            );
            $(
                ".df-vertical-mega-menu > .df-vertical-sub-menu .df-vertical-sub-menu"
            ).addClass("df-vertical-inside-mega-menu");
        },
        verticalCustomMegaMenuColumn: function() {
            $(".df_vertical_menu_main_container .df-vertical-mega-menu").each(
                function(i, ele) {
                    const _col_number = Number(ele.dataset.column);
                    let _c = 1;

                    $(this)
                        .find(".df-vertical-mega-menu-item>li")
                        .each(function(index, element) {
                            if (!$(this).attr("data-column")) {
                                $(this).attr("data-column", _c);
                                if (_c === _col_number) {
                                    _c = 1;
                                } else {
                                    _c++;
                                }
                            }
                        });
                    if (
                        !$(this)
                            .find(">ul")
                            .hasClass("df-vertical-col-added")
                    ) {
                        $(this)
                            .find('[data-column="1"]')
                            .wrapAll('<div class="col col-1"></div>');
                        $(this)
                            .find('[data-column="2"]')
                            .wrapAll('<div class="col col-2"></div>');
                        $(this)
                            .find('[data-column="3"]')
                            .wrapAll('<div class="col col-3"></div>');
                        $(this)
                            .find('[data-column="4"]')
                            .wrapAll('<div class="col col-4"></div>');
                        $(this)
                            .find('[data-column="5"]')
                            .wrapAll('<div class="col col-5"></div>');
                        $(this)
                            .find('[data-column="6"]')
                            .wrapAll('<div class="col col-6"></div>');
                        $(this)
                            .find('[data-column="7"]')
                            .wrapAll('<div class="col col-7"></div>');
                        $(this)
                            .find(">ul")
                            .addClass("df-vertical-col-added");
                    }
                }
            );
        },
        clickInnerSubmenu: function() {
            $(".df-vertical-sub-menu-reveal-stack li .dropdown-arrow").addClass(
                "rotate-arrow-down"
            );

            $(
                ".df-vertical-sub-menu-reveal-stack .df-vertical-menu-nav-level-0 > li, .df-vertical-sub-menu-reveal-stack .df-vertical-sub-menu li"
            ).on("click", function(event) {
                //Prevent Default
                if (
                    (event.target.parentElement.parentElement.classList.contains(
                        "df-vertical-menu-item-has-children"
                    ) ||
                        event.target.parentElement.classList.contains(
                            "df-vertical-menu-item-has-children"
                        )) &&
                    !$(this)
                        .parent()
                        .hasClass("col")
                ) {
                    event.preventDefault();
                }
                if (
                    event.target.closest(
                        ".df-vertical-sub-menu.df-vertical-custom-submenu.df-vertical-mega-menu-item"
                    ) ||
                    event.target.closest(".df-vertical-sub-menu li")
                ) {
                    event.stopPropagation();
                }

                const $submenu = $(this)
                    .children(".df-vertical-sub-menu")
                    .first();
                const $arrow = $(this)
                    .find(".dropdown-arrow")
                    .first();

                $(this)
                    .find("a")
                    .first()
                    .toggleClass("active");

                if ($submenu.length) {
                    const isVisible = $submenu.is(":visible");

                    if (
                        isVisible &&
                        !event.target.closest(
                            ".df-vertical-sub-menu.df-vertical-custom-submenu.df-vertical-mega-menu-item"
                        )
                    ) {
                        $submenu.removeClass("overflow-visible");
                        $arrow.toggleClass("rotate-arrow-up");
                        $submenu.slideUp("slow");
                    } else if (!isVisible) {
                        $arrow.toggleClass("rotate-arrow-up");
                        $submenu.slideDown("slow", function() {
                            $submenu.addClass("overflow-visible");
                        });
                    }
                }
            });
        },
        hoverOuterSubmenu: function() {
            const _this = this;

            $(
                ".df-vertical-sub-menu-reveal-flyout li .dropdown-arrow"
            ).addClass("rotate-arrow-left");

            $(
                '.df-vertical-sub-menu-reveal-flyout ul[class*="df-vertical-menu-nav-level-"]:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu) > li'
            )
                .on("mouseenter", function() {
                    let $parent = $(this);
                    _this.setMegaMenuDynamicPosition($parent);
                    _this.verticalMegaMenu($parent);
                    $parent
                        .closest(".df-vertical-menu-nav")
                        .closest(".et_pb_column")
                        .css("z-index", "9");
                    let $arrow = $parent
                        .children("a")
                        .children(".dropdown-arrow");
                    if (
                        $arrow.closest(".df-vertical-sub-menu-reveal-right")
                            .length > 0
                    ) {
                        $arrow
                            .addClass("rotate-arrow-right")
                            .removeClass("rotate-arrow-left");
                    }
                    $parent.children("a").addClass("active");
                    // $parent.find('.df-vertical-sub-menu-reveal-flyout').children('.df-vertical-sub-menu').css('display', 'none');
                    $parent
                        .children(".df-vertical-sub-menu")
                        .addClass("overflow-visible");
                    $parent
                        .children(".df-vertical-sub-menu")
                        .fadeIn("fast", function() {});
                })
                .on("mouseleave", function() {
                    let $parent = $(this);
                    let $arrow = $parent
                        .children("a")
                        .children(".dropdown-arrow");
                    $parent
                        .closest(".df-vertical-menu-nav")
                        .closest(".et_pb_column")
                        .css("z-index", "unset");
                    $parent.children("a").removeClass("active");
                    $parent
                        .children(".df-vertical-sub-menu")
                        .removeClass("overflow-visible")
                        .fadeOut("fast");
                    $arrow
                        .addClass("rotate-arrow-left")
                        .removeClass("rotate-arrow-right");
                });
        },
        touchOuterSubmenu: function() {
            $(
                ".df-vertical-sub-menu-reveal-flyout li .dropdown-arrow"
            ).addClass("rotate-arrow-down");

            $(".df-vertical-sub-menu-reveal-flyout li").each(function() {
                const $parent = $(this);

                // Open submenu on touch
                $parent.children("a").on("touchstart", function(event) {
                    if (
                        (event.target.parentElement.parentElement.classList.contains(
                            "df-vertical-menu-item-has-children"
                        ) ||
                            event.target.parentElement.classList.contains(
                                "df-vertical-menu-item-has-children"
                            )) &&
                        !$(this)
                            .parent()
                            .hasClass("col")
                    ) {
                        event.preventDefault();
                    }
                    if (event.target.closest(".df-vertical-sub-menu li")) {
                        event.stopPropagation();
                    }

                    const $arrow = $parent
                        .children("a")
                        .children(".dropdown-arrow");
                    const $submenu = $parent.children(".df-vertical-sub-menu");

                    // Toggle submenu visibility and arrow rotation
                    if (!$submenu.is(":visible")) {
                        $arrow
                            .addClass("rotate-arrow-up")
                            .removeClass("rotate-arrow-down");
                        $submenu.slideDown("down", function() {
                            $(this).addClass("overflow-visible");
                            $parent.children("a").addClass("active");
                        });
                    } else {
                        $arrow
                            .addClass("rotate-arrow-down")
                            .removeClass("rotate-arrow-up");
                        $submenu
                            .removeClass("overflow-visible")
                            .slideUp("slow");
                        $parent.children("a").removeClass("active");
                    }
                });

                // Close submenu on clicking outside
                $(document).on("touchstart", function(event) {
                    if (
                        !$(event.target).closest(
                            ".df-vertical-sub-menu-reveal-flyout li"
                        ).length
                    ) {
                        $parent
                            .children(".df-vertical-sub-menu")
                            .removeClass("overflow-visible")
                            .slideUp("slow");
                        $parent
                            .children("a")
                            .children(".dropdown-arrow")
                            .addClass("rotate-arrow-down")
                            .removeClass("rotate-arrow-up");
                        $parent.children("a").removeClass("active");
                    }
                });
            });
        },
        hamburgerRevealOnTouch: function() {
            const $hamburger = $(
                ".df_vertical_menu_main_container.df_enabled_hamburger.df_hamburger_reveal_on_hover .df-vertical-humberger-container"
            );
            const $nav = $(
                ".df_vertical_menu_main_container.df_enabled_hamburger.df_hamburger_reveal_on_hover nav.df-vertical-menu-nav-wrap"
            );

            $nav.hide();

            $hamburger.on("touchstart", function() {
                $nav_parent_wrapper = $(this).closest(".et_pb_module_inner");

                $nav_parent_wrapper.css(
                    "height",
                    `${$nav_parent_wrapper.css("height")}`
                );

                const $hamburgerIcon = $(this).find(
                    ".df-vertical-menu-hamburger-icon .hamburger"
                );
                const $nav = $(this).siblings(".df-vertical-menu-nav-wrap");

                $hamburgerIcon.toggleClass("is-active");
                $nav.toggleClass("df_reveal_active");

                if ($nav.is(":visible")) {
                    console.log("Touch end");

                    $nav.addClass("overflow_hidden");
                    $nav.slideUp(() => {
                        $nav.removeClass("overflow_hidden");
                        //fixing zindex issue with sibling contents
                        $(this)
                            .closest(".et_pb_module")
                            .css("z-index", "");
                        $(this)
                            .closest(".et_pb_column")
                            .css("z-index", "");
                        $(this)
                            .closest(".et_pb_row")
                            .css("z-index", "");
                    });
                } else {
                    console.log("Touch start");

                    //fixing zindex issue with sibling contents
                    $(this)
                        .closest(".et_pb_module")
                        .css("z-index", "99999999");
                    $(this)
                        .closest(".et_pb_column")
                        .css("z-index", "999999");
                    $(this)
                        .closest(".et_pb_row")
                        .css("z-index", "9999");

                    $nav.addClass("overflow_hidden");
                    $nav.slideDown(() => {
                        $nav.removeClass("overflow_hidden");
                    });
                }
            });
        },
        hamburgerRevealOnHover: function() {
            const $hamburger = $(
                ".df_vertical_menu_main_container.df_enabled_hamburger.df_hamburger_reveal_on_hover "
            );
            const $nav = $(
                ".df_vertical_menu_main_container.df_enabled_hamburger.df_hamburger_reveal_on_hover nav.df-vertical-menu-nav-wrap"
            );
            $nav.hide();
            $hamburger.on("mouseenter", function(event) {
                console.log("hover start");
                //fixing zindex issue with sibling contents
                console.log(this);

                $(this)
                    .closest(".et_pb_module")
                    .css("z-index", "99999999");
                $(this)
                    .closest(".et_pb_column")
                    .css("z-index", "999999");
                $(this)
                    .closest(".et_pb_row")
                    .css("z-index", "9999");

                $nav_parent_wrapper = $(this).closest(".et_pb_module_inner");
                $nav_parent_wrapper.css(
                    "height",
                    `${$nav_parent_wrapper.css("height")}`
                );
                const $hamburgerIcon = $(this).find(
                    ".df-vertical-menu-hamburger-icon .hamburger"
                );
                const $nav = $(this).children(".df-vertical-menu-nav-wrap");
                $hamburgerIcon.addClass("is-active");
                $nav.addClass("df_reveal_active");
                $nav.addClass("overflow_hidden");
                $nav.slideDown(() => {
                    $nav.removeClass("overflow_hidden");
                });
            });
            $hamburger.on("mouseleave", function(event) {
                console.log("Touched end");

                $nav_parent_wrapper = $(this).closest(".et_pb_module_inner");
                $nav_parent_wrapper.css(
                    "height",
                    `${$nav_parent_wrapper.css("height")}`
                );
                const $hamburgerIcon = $(this).find(
                    ".df-vertical-menu-hamburger-icon .hamburger"
                );
                const $nav = $(this).children(".df-vertical-menu-nav-wrap");
                $hamburgerIcon.removeClass("is-active");
                $nav.removeClass("df_reveal_active");
                $nav.addClass("overflow_hidden");
                $nav.hide();
                $nav.removeClass("overflow_hidden");

                //fixing zindex issue with sibling contents
                $(this)
                    .closest(".et_pb_module")
                    .css("z-index", "");
                $(this)
                    .closest(".et_pb_column")
                    .css("z-index", "");
                $(this)
                    .closest(".et_pb_row")
                    .css("z-index", "");
            });
        },
        hamburgerRevealOnClick: function() {
            const $hamburger = $(
                ".df_vertical_menu_main_container.df_enabled_hamburger.df_hamburger_reveal_on_click .df-vertical-humberger-container"
            );
            const $nav = $(
                ".df_vertical_menu_main_container.df_enabled_hamburger.df_hamburger_reveal_on_click nav.df-vertical-menu-nav-wrap"
            );

            $nav.hide();

            $hamburger.on("click", function() {
                $nav_parent_wrapper = $(this).closest(".et_pb_module_inner");

                $nav_parent_wrapper.css(
                    "height",
                    `${$nav_parent_wrapper.css("height")}`
                );

                const $hamburgerIcon = $(this).find(
                    ".df-vertical-menu-hamburger-icon .hamburger"
                );
                const $nav = $(this).siblings(".df-vertical-menu-nav-wrap");

                $hamburgerIcon.toggleClass("is-active");
                $nav.toggleClass("df_reveal_active");

                if ($nav.is(":visible")) {
                    console.log("Click start");

                    $nav.addClass("overflow_hidden");
                    $nav.slideUp(() => {
                        $nav.removeClass("overflow_hidden");
                        
                        //fixing zindex issue with sibling contents
                        $(this)
                            .closest(".et_pb_module")
                            .css("z-index", "");
                        $(this)
                            .closest(".et_pb_column")
                            .css("z-index", "");
                        $(this)
                            .closest(".et_pb_row")
                            .css("z-index", "");
                    });
                } else {
                    console.log("Click end");

                    //fixing zindex issue with sibling contents
                    $(this)
                        .closest(".et_pb_module")
                        .css("z-index", "99999999");
                    $(this)
                        .closest(".et_pb_column")
                        .css("z-index", "999999");
                    $(this)
                        .closest(".et_pb_row")
                        .css("z-index", "9999");

                    $nav.addClass("overflow_hidden");
                    $nav.slideDown(() => {
                        $nav.removeClass("overflow_hidden");
                    });
                }
            });
        }
    };
    dfVerticalMenuScripts.init();
})(jQuery);
