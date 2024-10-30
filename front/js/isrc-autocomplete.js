(function (factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else {
        // Browser globals
        factory(jQuery);
    }

}(function ($) {
    'use strict';
    let utils = (function () {
            return {
                escapeRegExChars: function (value) {
                    value = value.replace(/[<>{}()*+?.\\^$|]/g, "");
                    return '(' + value.replace(/[\-\[\]\/{}()*+?.\\^$|]/g, "\\$&") + ')(?![iclick]*(?:[>]))';
                },
                createNode: function (containerClass) {
                    let div = document.createElement('div');
                    div.className = containerClass;
                    div.style.display = 'none';
                    return div;
                }
            };
        }()),

        keys = {
            ESC: 27,
            TAB: 9,
            RETURN: 13,
            LEFT: 37,
            UP: 38,
            RIGHT: 39,
            DOWN: 40
        };

    function IsrcAutocomplete(el, options) {
        let noop = function () {
            },
            that = this,
            defaults = {
                appendTo: 'body',
                atc_btn: false,
                atc_label: 'add to cart',
                autoSelectFirst: false,
                containerClass: 'isrc_autocomplete-suggestions',
                currentRequest: null,
                customOrder: false,
                dataType: 'json',
                deferRequestBy: 100,
                delimiter: null,
                diduMeanEnabled: false,
                diduMeanLabel: 'Did you mean',
                enable_console: true,
                forceFixPosition: true,
                formatResult: IsrcAutocomplete.formatResult,
                haveSubmitBtn: false,
                isPreview: false,
                isTabbed: false,
                lookup: null,
                maxHeight: 450,
                minChars: 1,
                noCache: false,
                noResultsEnabled: false,
                noSuggestionNotice: 'No results',
                onSelect: null,
                continuousScroll: true,
                orientation: 'auto',
                paramName: 'query',
                params: {},
                popularLabel: 'No Results. Popular Searches',
                preventBadQueries: false,
                search_in: false,
                sendMeanings: false,
                sendPopularity: false,
                serviceUrl: null,
                showNoSuggestionNotice: '',
                suggestionClass: 'isrc_autocomplete-suggestion',
                template: 'clean',
                triggerSelectOnValidInput: true,
                type: 'POST',
                width: 'auto',
                zIndex: 999,
                onSearchComplete: noop,
                onSearchError: noop,
                lookupFilter: function (suggestion, originalQuery, queryLowerCase) {
                    return suggestion.value.toLowerCase().indexOf(queryLowerCase) !== -1;
                },
            };

        // Shared variables:
        that.element = el;
        that.el = $(el);
        that.wrapper = false;
        that.suggestions = [];
        that.currentTab = false;
        that.sugormean = 'suggestions';
        that.isMobile = false;
        that.limits = {};
        that.meanings = [];
        that.popularSearches = [];
        that.scrollPositions = {main: 0};
        that.badQueries = [];
        that.isAjaxingMore = false;
        that.ajaxingType = 'normal';
        that.selectedIndex = -1;
        that.selectedIndex2 = -1;
        that.selectedtypekey = false;
        that.currentValue = that.element.value;
        that.lastNotFoundValue = '';
        that.intervalId = 0;
        that.cachedResponse = {};
        that.lastIndexes = {};
        that.lastIndexes2 = {};
        that.onChangeInterval = null;
        that.onChange = null;
        that.pagination = 1;
        that.isLocal = false;
        that.overlayIsVisible = false;
        that.totalTabs = [],
            that.suggestionsContainer = null;
        that.options = $.extend({}, defaults, options);
        that.classes = {
            selected: 'isrc_autocomplete-selected',
            suggestion: 'isrc_autocomplete-suggestion',
            nosuggestion: 'autocomplete-meaning'
        };
        that.hint = null;
        that.hintValue = '';
        that.selection = null;

        // Initialize and set options:
        that.initialize();
        that.setOptions(options);
    }

    IsrcAutocomplete.utils = utils;

    $.IsrcAutocomplete = IsrcAutocomplete;

    IsrcAutocomplete.formatResult = function (suggestion, currentValue) {
        let pattern = utils.escapeRegExChars(currentValue);
        return suggestion.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
    };

    IsrcAutocomplete.prototype = {

        killerFn: null,

        initialize: function () {
            let that = this,
                suggestionSelector = '.' + that.classes.suggestion,
                selected = that.classes.selected,
                options = that.options,
                container,
                wrapper,
                isPreview = options.isPreview,
                placeholderOrg = options.placeholder,
                phAdvert = (options.phAdvert && options.phAdverts !== false),
                mouseOverTimer = (options.template === 'advanced') ? 150 : 0,
                phAdverts = options.phAdverts;


            // Remove autocomplete attribute to prevent native suggestions:
            that.element.setAttribute('autocomplete', 'off');
            that.el.attr('placeholder', placeholderOrg);

            that.suggestionsContainer = IsrcAutocomplete.utils.createNode(options.containerClass);

            container = $(that.suggestionsContainer);
            container.appendTo(options.appendTo);

            // Only set width if it was provided:
            if (options.width !== 'auto') {
                container.width(options.width);
            }

            // Listen for tabs click:
            if (options.isTabbed) {
                container.on('click.autocomplete', '.suggestion-tabs .tab-result', function () {
                    let tabid = $(this).data("tabid");
                    if (that.currentTab === tabid) {
                        return;
                    }
                    that.currentTab = tabid;
                    /* hide all first */
                    container.find('.suggestion-wrap').addClass('isrc_hidesuggestion');
                    /* show by tabid */
                    container.find('.suggestion-wrap[data-typekey="' + tabid + '"]').removeClass('isrc_hidesuggestion');

                    /* change class of other tabs */
                    container.find('.suggestion-tabs .tab-result').removeClass('selected');
                    $(this).addClass('selected');

                    that.rebuildScrollPosition();
                    that.restoreIndexPostition();
                    that.options.onTabClick(that);
                });
            }

            // Listen for mouse over event on suggestions list.
            container.on('mouseover.autocomplete', suggestionSelector, function () {
                let $this = $(this);

                if (!timeoutId) {
                    timeoutId = window.setTimeout(function () {
                        timeoutId = null; // EDIT: added this line
                        if (that.selectedIndex !== $this.data('index')) {
                            that.activate($this.data('index'));
                        }

                        that.options.themeFunctionsMouseOver(that, $this);
                    }, mouseOverTimer);
                }
            }).on('mouseleave.autocomplete', suggestionSelector, function () {
                if (timeoutId) {
                    window.clearTimeout(timeoutId);
                    timeoutId = null;
                }
            });


            // Deselect active element when mouse leaves wrapper container:
            wrapper = container.closest(that.options.wrapperClass);
            that.wrapper = wrapper;
            wrapper.on('mouseleave.autocomplete', function () {
                //that.selectedIndex = -1;
                that.options.themeFunctionsMouseLeave(that, $(this));
                container.find('.' + selected).removeClass(selected);
            });

            // Listen for click event on suggestions list:
            container.on('click.autocomplete', suggestionSelector, function (e) {
                if ($(e.target).closest('.isrc_extras.isrc_clickable').length > 0) {
                    return;
                }
                if ($(e.target).closest('.isrc_atc').length > 0) {
                    return;
                }
                that.selectedIndex = $(this).data('index');
                that.selectedIndex2 = $(this).data('index2');
                that.select(that.selectedIndex);
            });


            // Listen for click event on .slctr items:
            container.on('click.autocomplete', 'iclick', function () {
                let text = $(this).text();
                if (text) {
                    that.el.val(text).trigger('change');
                }
            });

            // Listen for iclick click event items:
            container.on('click.autocomplete', '.slctr', function () {
                that.select(that.selectedIndex);
            });

            // Listen for submit event:
            wrapper.on('submit', '.isrc-ajaxsearchform', function () {
                if (that.currentValue.length > 0) {
                    return true;
                }
                return false;
            });

            // Listen for click to show search event:
            wrapper.on('click.autocomplete', '.isrc-cl-op', function () {
                that.inputBodyAnim('toggle');
            });

            // Listen for click to show search event:
            // is theme form selected? */
            let $themeForm = wrapper.find('.isrc_theme_sw_full :input[name="s"]');
            if ($themeForm.length > 0) {
                $themeForm.attr('autocomplete', 'off');
                $themeForm.on('focus.autocomplete', function () {
                    that.inputBodyAnim('toggle', true);
                });

            }

            // Listen for click to fullscreen close button:
            wrapper.on('click.autocomplete', '.isrc-win-close', function () {
                that.inputBodyAnim('hide');
            });

            // Listen for trending tags click:
            wrapper.on('click.autocomplete', '.ttag', function () {
                wrapper.find('input.isrc-s').val($(this).text()).trigger('change');
            });

            // Listen for click event delete button:
            wrapper.on('click', '.isrc_delete_btn', function () {
                that.wrapper.find('.isrc-ajaxsearchform-container').removeClass('isrc-focused');
                wrapper.find('input.isrc-s').val('').trigger('change');
                /* reset Tabs*/
                that.currentTab = false;
            });

            // toggle preloader in preview
            if (isPreview) {
                $(window).on('sh_preloader', function () {
                    that.showHidePreloader('toggle');
                });
            }

            // Listen for click event on view more link:
            container.on('click.autocomplete', '.link-result.isrcviewall', function (e) {
                e.preventDefault();
                let $this = $(this),
                    typekey = $this.data('typekey');
                that.getContinuousSuggestions(typekey);
            });

            that.fixPosition();

            that.fixPositionCapture = function () {
                if (that.visible) {
                    that.fixPosition();
                }
            };

            $(window).on('resize.autocomplete', that.fixPositionCapture);

            that.el.on('keyup.autocomplete', function (e) {
                that.onKeyUp(e);
                that.scrollPositions = {};
            });

            that.el.on('focus.autocomplete', function () {
                that.wrapper.find('.isrc-ajaxsearchform-container').addClass('isrc-focused');
                if (phAdvert) {
                    that.el.removeClass('advert');
                    that.el.attr('placeholder', placeholderOrg);
                }
                that.onFocus();
                that.fixPosition();
            });

            that.el.on('blur.autocomplete', function () {
                if (phAdvert) {
                    let advert = phAdverts[Math.floor(Math.random() * phAdverts.length)];
                    that.el.addClass('advert');
                    that.el.attr('placeholder', advert.replace(/\\/g, ''));
                }
            });

            if (phAdvert) {
                let advert = phAdverts[Math.floor(Math.random() * phAdverts.length)];
                that.el.addClass('advert');
                that.el.attr('placeholder', advert.replace(/\\/g, ''));
            }


            that.el.on('change.autocomplete', function (e) {
                that.onKeyUp(e);
            });

            that.el.on('keydown.autocomplete', function (e) {
                that.onKeyPress(e);
            });

            that.adjustSlider();
            that.enableKillerFn();
        },
        transformResult: function (response) {
            response = typeof response === 'string' ? $.parseJSON(response) : response;
            return response;
        },
        adjustSlider: function () {
            let that = this,
                $rightSlider = that.wrapper.find('.isrc-slide.isrc-to-right'),
                $leftSlider = that.wrapper.find('.isrc-slide.isrc-to-left');

            if ($rightSlider.length > 0) {
                let $button = that.wrapper.find('.isrc-cl-op'),
                    buttonW = $button.outerWidth(),
                    buttonH = $button.outerHeight(),
                    offset = 5,
                    leftCss = buttonW + offset,
                    topCss = (buttonH / 2) + ($rightSlider.outerHeight() / 2) * -1,
                    maxW = 'calc(100% - ' + leftCss + 'px )';

                $rightSlider.css({'left': leftCss, 'top': topCss, 'max-width': maxW});
            }

            if ($leftSlider.length > 0) {
                let $button = that.wrapper.find('.isrc-cl-op'),
                    buttonW = $button.outerWidth(),
                    buttonH = $button.outerHeight(),
                    offset = 5,
                    rightCss = buttonW + offset,
                    topCss = (buttonH / 2) + ($leftSlider.outerHeight() / 2) * -1,
                    maxW = 'calc(100% - ' + rightCss + 'px )';
                $leftSlider.css({'right': rightCss, 'top': topCss, 'max-width': maxW});
            }
        },
        inputBodyAnim: function (type, focusTarget) {
            let that = this,
                focus = (!(typeof focusTarget === 'undefined' || focusTarget === false)),
                $boxH = that.wrapper.find('.isrc-boxH'),
                duration = 150,
                $slider = that.wrapper.find('.isrc-slide');

            if (type === 'toggle') {
                if (that.overlayIsVisible) {
                    type = 'hide';
                } else {
                    type = 'show';
                }
            }

            if (type === 'show') {
                that.overlayIsVisible = true;
                $('.isrc-boxH').fadeOut(duration);
                $boxH.fadeIn(duration);
                if (focus) {
                    that.el.focus();
                }
                /* slide in */
                $slider.addClass('slide-in').removeClass('isrc-slided-out slide-out');
                setTimeout(
                    function () {
                        $slider.addClass('isrc-slided-in');
                    }, 400);

            }

            if (type === 'hide') {
                that.wrapper.find('.isrc-ajaxsearchform-container').removeClass('isrc-focused');
                that.overlayIsVisible = false;
                $('.isrc-boxH').fadeOut(duration);
                /* slide out */
                $slider.removeClass('isrc-slided-in slide-in').addClass('slide-out');
                setTimeout(
                    function () {
                        $slider.addClass('isrc-slided-out');
                    }, 400);

            }

        },
        reorderResult: function (response) {

            let that = this,
                customOrder = that.options.customOrder,
                newresponse = {},
                totalTabs = [];

            if (customOrder && Object.keys(response.suggestions).length > 0) {
                newresponse.meanings = response.meanings;
                newresponse.popular_searches = response.popular_searches;
                newresponse.response_time = response.response_time;
                newresponse.results = response.results;
                newresponse.suggestions = {};
                newresponse.time = response.time;
                newresponse.isMobile = response.isMobile;
                newresponse.limits = response.limits;
                $.each(customOrder, function (key) {
                    $.each(response.suggestions, function (newkey, newvalue) {
                        if (key === newkey) {
                            newresponse.suggestions[key] = newvalue;
                            if (totalTabs.includes(key) !== -1) {
                                totalTabs.push(key);
                            }
                        }
                    });
                });
                that.totalTabs = totalTabs;
                return newresponse;
            }
            return response;
        },
        onFocus: function () {
            let that = this,
                minChars = that.options.minChars,
                valLength = that.el.val().length;

            if (minChars <= valLength) {
                that.show();
                that.visible = true;
            } else {
                that.hideFade();
                that.visible = false;
            }
        },
        checkAndSetTab: function () {
            let that = this,
                options = that.options,
                isTabbed = options.isTabbed,
                suggestions = that.suggestions,
                firstkey_in_suggestions = Object.keys(suggestions)[0],
                currentTab = that.currentTab;

            if (isTabbed && currentTab === false) {
                /* set shared var current tab. Its the first time */
                if (firstkey_in_suggestions) {
                    that.currentTab = firstkey_in_suggestions;
                }
            } else if (isTabbed && currentTab !== false) {
                /* check if the users selected tab exists in the new suggestions */
                if (typeof suggestions[currentTab] === 'undefined') {
                    /* it does not exists. set to first key */
                    that.currentTab = firstkey_in_suggestions;
                }
            }
        },
        setOptions: function (suppliedOptions) {
            let that = this,
                options = that.options;

            $.extend(options, suppliedOptions);

            that.isLocal = $.isArray(options.lookup);

            if (that.isLocal) {
                options.lookup = that.verifySuggestionsFormat(options.lookup);
            }

            options.orientation = that.validateOrientation(options.orientation, 'bottom');

            // Adjust height, width and z-index:
            $(that.suggestionsContainer).css({
                'max-height': options.maxHeight + 'px',
                'width': options.width + 'px',
                'z-index': options.zIndex
            });
        },
        clearCache: function () {
            this.cachedResponse = {};
            this.badQueries = [];
        },

        clear: function () {
            this.clearCache();
            this.currentValue = '';
            this.suggestions = {};
            this.meanings = [];
            this.popularSearches = [];
        },
        selectFirst: function () {
            if (this.options.autoSelectFirst === false || this.ajaxingType !== 'normal') {
                return false;
            }

            let that = this,
                container = $(that.suggestionsContainer),
                currentTab = that.currentTab,
                firstDiv = container.find(".isrc_autocomplete-suggestion[data-typekey='" + currentTab + "']").first(),
                firsVisibleIndex = firstDiv.data('index');
            that.activate(firsVisibleIndex);
            that.selectedIndex = firsVisibleIndex;

        },
        restoreIndexPostition: function () {
            if (this.options.autoSelectFirst === false) {
                return false;
            }

            let that = this,
                indexes = that.lastIndexes,
                currentTab = that.currentTab;

            if (typeof indexes[currentTab] === 'undefined') {
                // suggestions in tabs never hovered before. Select first visible
                that.selectFirst();
            } else {
                that.activate(indexes[currentTab]);
            }
        },
        disable: function () {
            let that = this;
            that.disabled = true;
            if (that.currentRequest) {
                that.currentRequest.abort();
            }
        },

        enable: function () {
            this.disabled = false;
        },
        getOrientation: function () {

            let that = this,
                $container = $(that.suggestionsContainer);

            // is preview mode?
            if (that.options.isPreview) {
                return 'bottom';
            }
            // Choose orientation
            let orientation = that.options.orientation,
                containerHeight = $container.outerHeight(),
                height = that.el.outerHeight(),
                offset = that.el.offset();

            if (orientation === 'auto') {
                let viewPortHeight = $(window).height(),
                    scrollTop = $(window).scrollTop(),
                    topOverflow = -scrollTop + offset.top - containerHeight,
                    bottomOverflow = scrollTop + viewPortHeight - (offset.top + height + containerHeight);
                orientation = (Math.max(topOverflow, bottomOverflow) === topOverflow) ? 'top' : 'bottom';
            }
            return orientation;

        },
        fixPosition: function () {
            // Use only when container has already its content

            let that = this,
                $container = $(that.suggestionsContainer),
                containerParent = $container.parent().get(0);
            // Fix position automatically when appended to body.
            // In other cases force parameter must be given.
            if (containerParent !== document.body && !that.options.forceFixPosition) {
                return;
            }

            // Choose orientation
            let orientation = that.getOrientation(),
                calcMaxHeight = that.calcHeight(orientation);
            // assign max-height for new calculation
            $container.css('max-height', calcMaxHeight);

            let containerHeight = $container.outerHeight(),
                options = that.options,
                suggestionsWidth = options.suggestionsWidth,
                widthreference = (suggestionsWidth === 'auto') ? options.appendTo : that.el,
                $deleteBtn = that.wrapper.find('.isrc_delete_btn '),
                appendTo = widthreference,
                customOffset = parseInt((options.offsetTop) ? options.offsetTop : 0),
                height = (suggestionsWidth === 'auto') ? appendTo.outerHeight() : appendTo.outerHeight() + 1,
                offset = appendTo.offset(),
                styles = {'top': offset.top, 'left': offset.left},
                maxHeight = that.options.maxHeight,
                fixedHeight = $.isNumeric(maxHeight);

            if (orientation === 'top') {
                styles.top += -containerHeight - customOffset;
            } else {
                styles.top += height + customOffset;
            }

            // If container is not positioned to body,
            // correct its position using offset parent offset
            if (containerParent !== document.body) {
                let opacity = $container.css('opacity'),
                    parentOffsetDiff;

                if (!that.visible) {
                    $container.addClass('isrc-opc-1').show();
                }

                parentOffsetDiff = $container.offsetParent().offset();
                styles.top -= parentOffsetDiff.top;
                styles.top += containerParent.scrollTop;
                styles.left -= parentOffsetDiff.left;

                if (!that.visible) {
                    $container.removeClass('isrc-opc-1').hide();
                }
            }

            if (that.options.width === 'auto') {
                if ($deleteBtn && suggestionsWidth === 'input') {
                    styles.width = (appendTo.outerWidth() + $deleteBtn.outerWidth()) + 'px';
                } else {
                    styles.width = appendTo.outerWidth() + 'px';
                }
            }

            if (orientation === 'bottom') {
                styles.top--;
            } else {
                styles.top++;
            }
            if (fixedHeight) {
                styles.maxHeight = calcMaxHeight + 'px';
            } else {
                styles.maxHeight = calcMaxHeight + 'px';
            }
            $container.css(styles);
        },
        calcHeight: function () {

            let optMaxHeight = this.options.maxHeight;


            if (optMaxHeight !== 'calculate') {
                return optMaxHeight;
            }

            let that = this,
                orientation = that.getOrientation(),
                input = that.options.appendTo,
                newMaxHeight = 0,
                isPreview = that.options.isPreview,
                element = that.el;

            if (isPreview) {
                return 500;
            }

            if (orientation === 'bottom') {
                let viewPortHeight = $(window).height(),
                    scrollTop = $(window).scrollTop(),
                    inputTop = element.offset().top,
                    inputHeight = element.outerHeight(),
                    offset = 25;

                newMaxHeight = viewPortHeight + scrollTop - inputTop - inputHeight - offset;

            } else if (orientation === 'top') {

                let srcfield = input.offset(),
                    distanceFromTop = srcfield.top - $(window).scrollTop(),
                    offset = 40;

                newMaxHeight = distanceFromTop - offset;
            }

            newMaxHeight = Math.round(newMaxHeight);

            if (newMaxHeight < 350) {
                return 350;
            }
            // this.options.maxHeight = maxHeight;
            return newMaxHeight;
        },
        validateOrientation: function (orientation, fallback) {

            orientation = $.trim(orientation || '').toLowerCase();

            if ($.inArray(orientation, ['auto', 'bottom', 'top']) === -1) {
                orientation = fallback;
            }

            return orientation;
        },
        enableKillerFn: function () {

            let that = this,
                uniqueClass = '.isrc_sc_' + that.options.shortcodeID;

            $('body').on('click.autocomplete', function (event) {
                if (!$(event.target).closest(uniqueClass).length && !$(event.target).closest('.meanings-wrap').length && !$(event.target).hasClass('sclick')) {
                    that.killSuggestions();

                    if ($(event.target).closest('.isrc-closest').find('.isrc-ajaxsearchform-container.inp-opn-st-body').length === 0) {
                        that.inputBodyAnim('hide');
                    }
                    that.wrapper.find('.isrc-ajaxsearchform-container').removeClass('isrc-focused');
                }
            });

            if (that.options.overlayClickOutsideClose) {
                if (that.wrapper.find('.isrc-boxH').length > 0) {
                    that.wrapper.on('click.autocomplete', '.isrc-boxH', function (e) {
                        if (event.target === this) {
                            that.killSuggestions();
                            that.inputBodyAnim('hide');
                            that.wrapper.find('input.isrc-s').val('').trigger('change');
                        }
                    });
                }
            }
        },
        killSuggestions: function () {
            let that = this;
            that.stopKillSuggestions();
            that.intervalId = window.setInterval(function () {
                that.hideFade();
                that.stopKillSuggestions();
            }, 50);
        },

        stopKillSuggestions: function () {
            window.clearInterval(this.intervalId);
        },

        isCursorAtEnd: function () {
            let that = this,
                valLength = that.el.val().length,
                selectionStart = that.element.selectionStart,
                range;

            if (typeof selectionStart === 'number') {
                return selectionStart === valLength;
            }
            if (document.selection) {
                range = document.selection.createRange();
                range.moveStart('character', -valLength);
                return valLength === range.text.length;
            }
            return true;
        },
        onKeyPress: function (e) {
            let that = this;
            // If suggestions are hidden and user presses arrow down, display suggestions:
            if (!that.disabled && !that.visible && e.which === keys.DOWN && that.currentValue) {
                that.suggest();
                return;
            }

            if (that.disabled || !that.visible) {
                return;
            }

            switch (e.which) {
                case keys.ESC:
                    that.el.val(that.currentValue);
                    that.el.blur();
                    that.hideFade();
                    break;
                case keys.RIGHT:
                    if (that.hint && that.options.onHint && that.isCursorAtEnd()) {
                        that.selectHint();
                        break;
                    }
                    return;
                case keys.TAB:
                    if (that.hint && that.options.onHint) {
                        that.selectHint();
                        return;
                    }
                // Fall through to RETURN
                case keys.RETURN:
                    if (this.sugormean === 'meanings') {
                        // Cancel event if function did not return:
                        e.stopImmediatePropagation();
                        e.preventDefault();
                        return;
                    }
                    if (this.sugormean === 'popular') {
                        // Cancel event if function did not return:
                        e.stopImmediatePropagation();
                        e.preventDefault();
                        return;
                    }
                    if (that.selectedIndex === -1) {
                        that.hideFade();
                        return;
                    }
                    return true;
                    break;
                case keys.UP:
                    that.moveUp();
                    break;
                case keys.DOWN:
                    that.moveDown();
                    break;
                default:
                    return;
            }

            // Cancel event if function did not return:
            e.stopImmediatePropagation();
            e.preventDefault();
        },

        onKeyUp: function (e) {
            let that = this;
            if (that.disabled) {
                return;
            }

            switch (e.which) {
                case keys.UP:
                case keys.DOWN:
                    return;
            }

            clearInterval(that.onChangeInterval);

            if (that.el.val().length > 0) {
                that.wrapper.find('.isrc-ajaxsearchform-container').addClass('isrc-haveinp');
            } else {
                that.wrapper.find('.isrc-ajaxsearchform-container').removeClass('isrc-haveinp');
            }

            if (that.currentValue !== that.el.val()) {
                // that.findBestHint();
                if (that.options.deferRequestBy > 0) {
                    // Defer lookup in case when value changes very quickly:
                    that.onChangeInterval = setInterval(function () {
                        that.onValueChange();
                    }, that.options.deferRequestBy);
                } else {
                    that.onValueChange();
                }
            }
        },
        onValueChange: function () {
            let that = this,
                options = that.options,
                value = that.el.val(),
                query = that.getQuery(value),
                index;

            if (that.selection) {
                that.selection = null;
                (options.onInvalidateSelection || $.noop).call(that.element);
            }

            clearInterval(that.onChangeInterval);
            that.currentValue = value;
            that.selectedIndex = -1;

            // Check existing suggestion for the match before proceeding:
            if (options.triggerSelectOnValidInput) {
                index = that.findSuggestionIndex(query);
                if (index !== -1) {
                    that.select(index);
                    return;
                }
            }

            /* show hide delete button */
            if (query.length < 1) {
                that.showHideClearBtn('hide');
            } else {
                that.showHideClearBtn('show');
            }

            if (query.length < options.minChars) {
                that.hideFade();
            } else {
                that.getSuggestions(query);
            }
        },
        showHideClearBtn: function (type) {
            let that = this;

            if (type === 'hide') {
                that.el.next('.isrc_delete_btn').removeClass('isrc-visible');
            } else {
                that.el.next('.isrc_delete_btn').addClass('isrc-visible');
            }
        },
        findSuggestionIndex: function (query) {
            let that = this,
                index = -1,
                queryLowerCase = query.toLowerCase();

            $.each(that.suggestions, function (i, suggestion) {
                if (suggestion.value.toLowerCase() === queryLowerCase) {
                    index = i;
                    return false;
                }
            });

            return index;
        },
        getQuery: function (value) {
            let delimiter = this.options.delimiter,
                parts;

            if (!delimiter) {
                return value;
            }
            parts = value.split(delimiter);
            return $.trim(parts[parts.length - 1]);
        },
        getSuggestionsLocal: function (query) {
            let that = this,
                options = that.options,
                queryLowerCase = query.toLowerCase(),
                filter = options.lookupFilter,
                limit = parseInt(options.lookupLimit, 10),
                data;

            data = {
                suggestions: $.grep(options.lookup, function (suggestion) {
                    return filter(suggestion, query, queryLowerCase);
                })
            };

            if (limit && data.suggestions.length > limit) {
                data.suggestions = data.suggestions.slice(0, limit);
            }

            return data;
        },
        loadmoreSpinner: function (status) {
            let that = this,
                container = $(that.suggestionsContainer);

            if (status === 'show') {
                container.find('.load-more-label').addClass('isrc_hide');
                container.find('.loadmore-spinner').removeClass('isrc_hide');
            } else if (status === 'hide') {
                container.find('.load-more-label').removeClass('isrc_hide');
                container.find('.loadmore-spinner').addClass('isrc_hide');
            }
        },
        getContinuousSuggestions: function (typekey) {
            if (this.isAjaxingMore) {
                return false;
            }
            this.isAjaxingMore = true;

            let that = this,
                options = that.options,
                serviceUrl = options.serviceUrl,
                q = that.currentValue,
                currentSuggestions = that.suggestions[typekey],
                params = options.params,
                data = {},
                not_in = [],
                request;
            $.each(currentSuggestions, function (i, val) {
                not_in.push(val.id);
            });

            data = {
                not_in: not_in.join(","),
                search_in: typekey,
                searchtype: 'loadmore',
                action: params.action,
                locale: options.locale,
                instance: options.shortcodeID,
                limit: params.limit,
                order_by: params.order_by,
                logging: false,
                log_popularity: false,
                hash: params.hash,
                query: params.query
            };
            /* show load more spinner */
            that.loadmoreSpinner('show');
            $.ajax({
                url: serviceUrl,
                data: data,
                type: options.type,
                dataType: options.dataType,
            }).done(function (data) {
                that.isAjaxingMore = false;
                that.ajaxingType = 'loadmore';
                let moreSuggestions = data.suggestions[typekey],
                    limits = data.limits,
                    oldSuggestions = that.suggestions[typekey];

                if (typeof limits[typekey] !== 'undefined') {
                    that.limits[typekey] = limits[typekey];
                } else {
                    that.limits[typekey] = 0;
                }


                $.each(moreSuggestions, function (i, val) {
                    that.suggestions[typekey].push(val);
                });

                that.suggest();
                that.loadmoreSpinner('hide');

            }).fail(function (jqXHR, textStatus, errorThrown) {
                options.onSearchError.call(that.element, q, jqXHR, textStatus, errorThrown);
            });
        },
        getSuggestions: function (q) {
            let response,
                that = this,
                options = that.options,
                serviceUrl = options.serviceUrl,
                params,
                cacheKey;

            options.params[options.paramName] = q;
            params = options.ignoreParams ? null : options.params;

            if (that.isLocal) {
                response = that.getSuggestionsLocal(q);
            } else {
                if ($.isFunction(serviceUrl)) {
                    serviceUrl = serviceUrl.call(that.element, q);
                }
                cacheKey = serviceUrl + '?' + $.param(params || {});
                response = that.cachedResponse[cacheKey];
            }

            if (response && (typeof response.suggestions === "object") && (response.suggestions !== null)) {
                that.suggestions = response.suggestions;
                that.meanings = response.meanings;
                that.popularSearches = response.popular_searches;
                that.limits = response.limits;
                that.suggest();
            } else if (!that.isBadQuery(q)) {
                if (that.onSearchStart()) {
                    return;
                }
                if (that.currentRequest) {
                    that.currentRequest.abort();
                }
                params.caret = that.getCaretPosition();
                that.currentRequest = $.ajax({
                    url: serviceUrl,
                    data: params,
                    type: options.type,
                    dataType: options.dataType,
                }).done(function (data) {
                    that.ajaxingType = 'normal';
                    let result;
                    that.currentRequest = null;
                    result = that.transformResult(data);
                    result = that.reorderResult(result);
                    that.processResponse(result, q, cacheKey);
                    that.onSearchComplete();
                    that.fixPositionCapture();
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    options.onSearchError.call(that.element, q, jqXHR, textStatus, errorThrown);
                });
            }
        },
        onSearchStart: function () {
            let that = this;
            that.showHidePreloader('show');
        },
        onSearchComplete: function () {
            let that = this;
            that.showHidePreloader('hide');
        },
        showHidePreloader: function (visibility) {
            let that = this,
                options = that.options,
                preloader_container = that.el.next('.isrc_delete_btn');

            if (visibility === 'show') {
                preloader_container.addClass('ispreload');
                preloader_container.css({
                    '-webkit-mask-image': 'url(' + options.loader_icon + ')',
                });
            } else if (visibility === 'hide') {
                preloader_container.removeClass('ispreload');
                preloader_container.css({
                    '-webkit-mask-image': 'url(' + options.close_icon + ')',
                });
            } else if (visibility === 'toggle') {
                preloader_container.addClass('ispreload');
                preloader_container.css({
                    '-webkit-mask-image': 'url(' + options.loader_icon + ')',
                });
                if (preloader_container.css('visibility') === 'hidden') {
                    preloader_container.css('visibility', 'visible');
                }
                else {
                    preloader_container.css('visibility', 'hidden');
                }
            }
        },
        sendMeaning: function (q) {

            let selectedSuggestion,
                that = this,
                options = that.options,
                serviceUrl = options.serviceUrl,
                paramsForMeaning = {},
                isLoggingEnabled = options.sendMeanings,
                isSendPopularityEnabled = options.sendPopularity;

            paramsForMeaning.action = 'isrc_ajax_flowpopular';

            if (typeof q === 'object') {
                selectedSuggestion = q;
                q = q.value;
            }

            if (!isLoggingEnabled && !isSendPopularityEnabled) {
                return;
            }

            if (typeof selectedSuggestion !== 'undefined' && isSendPopularityEnabled) {
                paramsForMeaning.title = selectedSuggestion.value;
                paramsForMeaning.selection_id = selectedSuggestion.id;
                paramsForMeaning.type = selectedSuggestion.type;
                paramsForMeaning.ptn = selectedSuggestion.ptn;
            }

            /* add lastnotfound */
            if (this.lastNotFoundValue !== '' && isLoggingEnabled) {
                paramsForMeaning.lastNotFoundValue = this.lastNotFoundValue;
                paramsForMeaning.current_src = this.currentValue;
                paramsForMeaning.clicked = q;
                this.lastNotFoundValue = '';
            } else {
                this.lastNotFoundValue = '';
            }

            /* dont send if nothing to send */
            if (typeof paramsForMeaning.lastNotFoundValue === 'undefined' && typeof paramsForMeaning.selection_id === 'undefined') {
                return;
            }

            paramsForMeaning.logging = isLoggingEnabled;
            paramsForMeaning.caret = that.getCaretPosition();
            paramsForMeaning.log_popularity = isSendPopularityEnabled;
            paramsForMeaning.locale = that.options.locale;
            paramsForMeaning.instance = that.options.shortcodeID;
            paramsForMeaning.hash = isrc_params.hashvar;

            $.ajax({
                url: serviceUrl,
                data: paramsForMeaning,
                type: options.type,
                dataType: options.dataType,
            }).done(function (data) {

            }).fail(function (jqXHR, textStatus, errorThrown) {

            });
        },
        isBadQuery: function (q) {

            if (!this.options.preventBadQueries) {
                return false;
            }

            let badQueries = this.badQueries,
                i = badQueries.length;

            while (i--) {
                if (q.indexOf(badQueries[i]) === 0) {
                    return true;
                }
            }

            return false;
        },
        suggest: function () {
            let that = this,
                $suggestionsContainer = $(that.suggestionsContainer),
                meanings = that.meanings,
                popularSearches = that.popularSearches,
                suggestions = that.suggestions,
                popularEnabled = that.options.popularEnabled,
                meaningsEnabled = that.options.diduMeanEnabled,
                suggestionsLength = Object.keys(suggestions).length;

            $suggestionsContainer.removeClass('ispopularsrc');
            $suggestionsContainer.removeClass('ismeaningsrc');
            $suggestionsContainer.removeClass('noresults');

            if (meanings.length > 0 && suggestionsLength === 0 && meaningsEnabled) {
                $suggestionsContainer.addClass('ismeaningsrc');
                that.sugormean = 'meanings';
                that.lastNotFoundValue = this.currentValue;
                that.showMeanings();
                return;

            } else if (popularSearches.length > 0 && suggestionsLength === 0 && popularEnabled) {
                $suggestionsContainer.addClass('ispopularsrc');
                that.sugormean = 'popular';
                that.lastNotFoundValue = this.currentValue;
                that.showPopulars();
                return;
            }

            that.sugormean = 'noresults';

            if (suggestionsLength === 0) {
                $suggestionsContainer.addClass('noresults');
                /* update the last not found value */
                that.lastNotFoundValue = that.currentValue;
                that.noSuggestions();
                return;
            }

            that.sugormean = 'suggestions';

            let options = that.options,
                value = that.getQuery(that.currentValue),
                container = $(that.suggestionsContainer),
                html_body = '',
                index;

            if (options.triggerSelectOnValidInput) {
                index = that.findSuggestionIndex(value);
                if (index !== -1) {
                    that.select(index);
                    return;
                }
            }

            // Build suggestions inner HTML Body:
            if ($.isFunction(options.htmlBodyRender)) {
                that.checkAndSetTab();
                html_body = options.htmlBodyRender(that);
            }

            this.adjustContainerWidth();

            container.html(html_body);

            container.removeClass('havedatail');


            // Select first value by default:
            if (options.autoSelectFirst && !that.isMobile) {
                that.selectFirst();
            }

            that.show();
            that.visible = true;
            that.rebuildScrollPosition();

            that.rebindEvents();

            // set current tab
            if (options.isTabbed && that.currentTab === false) {
                let selectedTab = $('.suggestion-tabs').find('.tab-result.selected').data('tabid');
                if (selectedTab) {
                    that.currentTab = selectedTab;
                }
            }

            if (that.ajaxingType === 'normal') {
                that.lastIndexes = {};
                that.lastIndexes2 = {};
            }

            if (options.autoSelectFirst && !that.isMobile) {
                that.selectFirst();
            }

            if (that.selectedtypekey === false || that.totalTabs.indexOf(that.selectedtypekey) === -1) {
                that.selectedtypekey = that.totalTabs[0];
            }
            that.setHiddenPostType();
            that.options.onSuggest(that);
        },
        show: function () {
            let that = this,
                container = $(that.suggestionsContainer);

            container.removeClass('isrc-opc-1');
            if (that.visible) {
                container.show();
                return;
            }
            container.removeClass('isrc-opc-1').hide();
            that.options.onShow(that);
            container.fadeIn(150, function () {
                that.visible = true;
            });
        },
        hideFade: function () {
            let that = this,
                container = $(that.suggestionsContainer);

            if (!that.visible) {
                container.hide();
                that.selectedIndex = -1;
                that.signalHint(null);
                return;
            }

            container.fadeOut(150, function () {
                that.visible = false;
                that.selectedIndex = -1;
                that.signalHint(null);
            });

        },
        rebindEvents: function () {
            let that = this,
                $suggestionswrap = $(".suggestions-wrap");

            $suggestionswrap.off('scroll');
            $suggestionswrap.on('scroll', function () {
                that.scrollEvents();

            });
        },
        scrollEvents: function () {
            this.saveScrollPositions();
            this.continousScroll();
        },
        continousScroll: function () {
            let that = this,
                container = $(that.suggestionsContainer),
                autoContinue = (that.options.autoLoadMore && that.options.loadMoreBtn),
                viewallBtns = container.find('.isrcviewall:visible:first');

            if (autoContinue) {
                if (viewallBtns.isrc_isInViewport()) {
                    let typekey = container.find('.isrcviewall:visible:first').data('typekey');
                    if (typekey) {
                        that.getContinuousSuggestions(typekey);
                    }
                }
            }

        },
        saveScrollPositions: function () {
            let that = this,
                container = $(that.suggestionsContainer),
                isTabbed = that.options.isTabbed,
                currentTab = that.currentTab,
                scrollto = container.find('.suggestions-wrap').scrollTop();

            if (isTabbed && currentTab !== false) {
                that.scrollPositions[currentTab] = scrollto;
            }
            that.scrollPositions.main = scrollto;
        },
        rebuildScrollPosition: function () {
            let that = this,
                container = $(that.suggestionsContainer),
                isTabbed = that.options.isTabbed,
                scrollPositions = that.scrollPositions,
                currentTab = that.currentTab,
                scrollto = 0;

            if (isTabbed && currentTab !== false) {
                scrollto = scrollPositions[currentTab];
            } else {
                scrollto = scrollPositions.main;
            }

            if (typeof scrollto === 'undefined') {
                scrollto = 0;
            }
            container.find('.suggestions-wrap').scrollTop(scrollto);
        },
        noSuggestions: function () {
            let that = this,
                container = $(that.suggestionsContainer),
                noResultsEnabled = that.options.noResultsEnabled,
                html = '';

            if (!noResultsEnabled) {
                this.adjustContainerWidth();
                container.html('');
                that.hideFade();
                that.visible = false;
                return false;
            }

            html += '<div class="autocomplete-no-suggestion">' + that.options.noSuggestionNotice + '</div>';

            that.adjustContainerWidth();
            container.html(html);
            that.show();
        },
        showMeanings: function () {
            let that = this,
                container = $(that.suggestionsContainer),
                meaning_label = that.options.diduMeanLabel,
                html = '',
                suggestionClass = that.options.suggestionClass,
                meanings = that.meanings;

            html += '<div class="meanings-wrap">';

            $.each(meanings, function (i, meaning) {
                html += '<div class="' + suggestionClass + ' autocomplete-meaning meaning didumean" data-index="' + i + '">';
                html += '<span class="didumean_label">' + meaning_label + ': </span>';
                html += '<span class="didumean_txt">' + meaning.value + '</span>';
                html += '</div>';
            });
            html += '</div>';

            this.adjustContainerWidth();
            container.html(html);
            that.show();
        },
        showPopulars: function () {
            let that = this,
                container = $(that.suggestionsContainer),
                popularLabel = that.options.popularLabel,
                html = '',
                suggestionClass = that.options.suggestionClass,
                popularSearches = that.popularSearches,
                popularEnabled = that.options.popularEnabled;

            if (!popularEnabled) {
                this.adjustContainerWidth();
                container.html('');
                that.hideFade();
                that.visible = false;
                return false;
            }


            html += '<div class="isrc_meaningheader"><span class="didumean_label">' + popularLabel + ': </span></div>';
            html += '<div class="meanings-wrap">';

            $.each(popularSearches, function (i, popular) {
                html += '<div class="' + suggestionClass + ' autocomplete-meaning meaning" data-index="' + i + '">';
                html += '<span class="diduMeanLabel">- ' + popular.value + '</span>';
                html += '</div>';
            });
            html += '</div>';

            this.adjustContainerWidth();
            container.html(html);
            that.show();
        },
        adjustContainerWidth: function () {
            let that = this,
                options = that.options,
                template = that.options.template,
                isTabbed = that.options.isTabbed,
                suggestionsWidth = options.suggestionsWidth,
                widthreference = (suggestionsWidth === 'auto') ? that.options.appendTo : that.el,
                $deleteBtn = that.wrapper.find('.isrc_delete_btn '),
                width,
                container = $(that.suggestionsContainer);

            container.addClass('isrc_' + template);
            container.addClass('isrc_cont_sc_' + options.shortcodeID);

            if (isTabbed) {
                container.addClass('isrc_tabbed');
            }

            if (that.isMobile) {
                container.addClass('isMobile');
            }

            // If width is auto, adjust width before displaying suggestions,
            // because if instance was created before input had width, it will be zero.
            // Also it adjusts if input width has changed.
            // -2px to account for suggestions border.
            if (options.width === 'auto') {

                if ($deleteBtn && suggestionsWidth === 'input') {
                    width = widthreference.outerWidth() + $deleteBtn.outerWidth() - 2;
                } else {
                    width = widthreference.outerWidth() - 2;
                }

                container.width(width > 0 ? width : 300);

                if (width < 300) {
                    container.addClass('under_300');
                }

                if (width < 500 && width > 300) {
                    container.addClass('under_500');
                }

                if (width < 700 && width > 500) {
                    container.addClass('under_700');
                }

            }
        },
        signalHint: function (suggestion) {
            let hintValue = '',
                that = this;
            if (suggestion) {
                hintValue = that.currentValue + suggestion.value.substr(that.currentValue.length);
            }
            if (that.hintValue !== hintValue) {
                that.hintValue = hintValue;
                that.hint = suggestion;
                (this.options.onHint || $.noop)(hintValue);
            }
        },
        verifySuggestionsFormat: function (suggestions) {
            return suggestions;
            // @TODO
            // If suggestions is string array, convert them to supported format:
            if (suggestions.length && typeof suggestions[0] === 'string') {
                return $.map(suggestions, function (value) {
                    return {value: value, data: null};
                });
            }

            return suggestions;
        }
        ,
        processResponse: function (result, originalQuery, cacheKey) {
            let that = this,
                options = that.options;

            result.suggestions = that.verifySuggestionsFormat(result.suggestions);

            // Cache results if cache is not disabled:
            if (!options.noCache) {
                that.cachedResponse[cacheKey] = result;
                if (options.preventBadQueries && result.suggestions.length === 0) {
                    that.badQueries.push(originalQuery);
                }
            }

            // Return if originalQuery is not matching current query:
            if (originalQuery !== that.getQuery(that.currentValue)) {
                return;
            }

            that.suggestions = result.suggestions;
            that.meanings = result.meanings;
            that.popularSearches = result.popular_searches;
            that.limits = result.limits;
            that.isMobile = (result.isMobile === 'yes');
            if (that.isMobile) {
                that.options.template = 'clean';
            }
            that.suggest();
        },
        activate: function (index_org) {
            let that = this,
                activeItem,
                selected = that.classes.selected,
                container = $(that.suggestionsContainer),
                suggestionClass = that.options.suggestionClass;

            activeItem = container.find('.' + suggestionClass + "[data-index='" + index_org + "']");
            if (activeItem.length < 1) {
                return false;
            }

            container.find('.detail-selector').removeClass('detail-selector');
            container.find('.' + selected).removeClass(selected);

            that.selectedIndex = index_org;
            that.selectedIndex2 = activeItem.data('index2');
            that.selectedtypekey = activeItem.data('typekey');
            that.lastIndexes[that.selectedtypekey] = that.selectedIndex;
            that.lastIndexes2[that.selectedtypekey] = that.selectedIndex2;
            $(activeItem).addClass(selected);
            that.options.onActivate(that);
            /* set hidden field value */
            that.setHiddenPostType();
            return activeItem;

        },
        setHiddenPostType: function () {
            let that = this,
                selectedtypekey = that.selectedtypekey,
                $container = $(that.suggestionsContainer),
                posttypes = that.extractPostType(selectedtypekey),
                $hiddenpostfield = $container.closest('.isrc-ajaxsearchform-container').find('.isrc_form_post_type'),
                setPT = false;

            if (posttypes === false || !setPT) {
                return false;
            }

            if (posttypes.type === 'post_type') {
                $hiddenpostfield.val(posttypes.post_type);
            } else {
                $hiddenpostfield.val();
            }

        },
        extractPostType: function (typekey) {

            if (typeof typekey === 'undefined') {
                return false;
            }

            let type = typekey.substring(0, 3),
                postType = typekey.substring(3, 99),
                ret = {post_type: postType};

            if (type === 'tx_') {
                ret.type = 'taxonomy';
            } else if (type === 'pt_') {
                ret.type = 'post_type';
            }

            return ret;

        },
        selectHint: function () {
            let that = this,
                i = $.inArray(that.hint, that.suggestions);

            that.select(i);
        },
        select: function (i) {
            let that = this;

            that.onSelect(i);
        },
        moveUp: function () {
            let that = this;

            if (that.sugormean === 'suggestions') {
                that.adjustScrollSuggestions('up');
            }
        },
        moveDown: function () {
            let that = this;
            that.adjustScrollSuggestions('down');

        },
        adjustScrollSuggestions: function (direction) {
            let that = this,
                currentIndex = that.selectedIndex,
                selectedtypekey = that.selectedtypekey,
                nextIndex = (direction === 'down') ? currentIndex + 1 : currentIndex - 1,
                activeItem = that.activate(nextIndex);

            if (!activeItem) {
                return;
            }

            /* if tabbed and next id is on the next tab */
            if (that.options.isTabbed) {
                if (selectedtypekey !== that.selectedtypekey) {
                    /* have an active item but its not visible */
                    /* click the next tab? */
                    $('.suggestion-tabs').find('div[data-tabid="' + that.selectedtypekey + '"]').click();
                }
            }

            let $suggestionsContainer = $(that.suggestionsContainer),
                $wrap = $suggestionsContainer.find('.suggestions-wrap'),
                wrapHeight = $wrap.height(),
                heightDelta = wrapHeight / 2,
                heightDeltaNegative = heightDelta * -1;

            $wrap.isrc_scrollTo(activeItem, 0, {offset: heightDeltaNegative});
        },
        onSelect: function (index) {
            let that = this,
                index2 = that.selectedIndex2,
                typekey = that.selectedtypekey,
                onSelectCallback = that.options.onSelect,
                isPreview = that.options.isPreview;

            /* send meanings */
            if (that.sugormean === 'suggestions' && !isPreview) {
                let suggestion = that.suggestions[typekey][index2];
                this.sendMeaning(suggestion);
            }

            if (that.sugormean === 'meanings') {
                let meaning = that.meanings[index];
                that.currentValue = that.getValue(meaning.value);
                that.el.val(that.currentValue).focus();
                that.onValueChange();
                return false;
            } else if (that.sugormean === 'popular') {
                let popular = that.popularSearches[index];
                that.currentValue = that.getValue(popular.value);
                that.el.val(that.currentValue).focus();
                that.onValueChange();
                return;

            } else {
                let suggestion = that.suggestions[typekey][index2];
                that.currentValue = that.getValue(suggestion.value);
            }

            if (that.currentValue !== that.el.val() && !isPreview) {
                that.el.val(that.currentValue);
            }

            if (isPreview) {
                if ('parentIFrame' in window) {
                    window.parentIFrame.sendMessage('msg_01');
                }
                return false;
            }

            that.hideFade();
            that.signalHint(null);
            let suggestion = that.suggestions[typekey][index2];

            if ($.isFunction(onSelectCallback)) {
                onSelectCallback.call(that.element, suggestion);
            } else {
                if (suggestion.id !== -1) {
                    window.location.href = suggestion.url;
                }
            }
        },
        getCaretPosition: function () {
            let that = this,
                ctrl = that.el[0];

            // IE < 9 Support
            if (document.selection) {
                ctrl.focus();
                let range = document.selection.createRange();
                let rangelen = range.text.length;
                range.moveStart('character', -ctrl.value.length);
                let start = range.text.length - rangelen;
                return (start + rangelen === ctrl.value.length);
            }
            // IE >=9 and other browsers
            else if (ctrl.selectionStart || ctrl.selectionStart === '0') {
                return (ctrl.selectionEnd === that.el.val().length);
            } else {
                // error
                return true;
            }

        },
        getValue: function (value) {
            let that = this,
                delimiter = that.options.delimiter,
                currentValue,
                parts;

            if (!delimiter) {
                return value;
            }

            currentValue = that.currentValue;
            parts = currentValue.split(delimiter);

            if (parts.length === 1) {
                return value;
            }

            return currentValue.substr(0, currentValue.length - parts[parts.length - 1].length) + value;
        },
        dispose: function () {
            let that = this;
            that.el.off('.autocomplete').removeData('autocomplete');
            $(window).off('resize.autocomplete', that.fixPositionCapture);
            $(that.suggestionsContainer).remove();
        }
    };

    // Create chainable jQuery plugin:
    $.fn.isrcautocomplete = function (options, args) {
        var dataKey = 'autocomplete';
        // If function invoked without argument return
        // instance of the first matched element:
        if (arguments.length === 0) {
            return this.first().data(dataKey);
        }

        return this.each(function () {
            let inputElement = $(this),
                instance = inputElement.data(dataKey);

            if (typeof options === 'string') {
                if (instance && typeof instance[options] === 'function') {
                    instance[options](args);
                }
            } else {
                // If instance already exists, destroy it:
                if (instance && instance.dispose) {
                    instance.dispose();
                }
                instance = new IsrcAutocomplete(this, options);
                inputElement.data(dataKey, instance);
            }
        });
    };

    $.fn.isrc_isInViewport = function () {
        if ($(this).length < 1) {
            return false;
        }
        let elementTop = $(this).position().top + $(this).outerHeight() - 10;
        let containerHeight = $(this).closest('.isrc_autocomplete-suggestions').outerHeight();
        return (elementTop <= containerHeight);
    };

}));

/*!
 * jQuery.isrc_scrollTo MODIFIED VERSION FOR I-SEARCH
 * Copyright (c) 2007 Ariel Flesler - aflesler ○ gmail • com | https://github.com/flesler
 * Licensed under MIT
 * https://github.com/flesler/jquery.isrc_scrollTo
 * @projectDescription Lightweight, cross-browser and highly customizable animated scrolling with jQuery
 * @author Ariel Flesler
 * @version 2.1.2
 */
(function (factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    } else if (typeof module !== 'undefined' && module.exports) {
        // CommonJS
        module.exports = factory(require('jquery'));
    } else {
        // Global
        factory(jQuery);
    }
})(function ($) {
    'use strict';

    let $isrc_scrollTo = $.isrc_scrollTo = function (target, duration, settings) {
        return $(window).isrc_scrollTo(target, duration, settings);
    };

    $isrc_scrollTo.defaults = {
        axis: 'xy',
        duration: 0,
        limit: true
    };

    function isWin(elem) {
        return !elem.nodeName ||
            $.inArray(elem.nodeName.toLowerCase(), ['iframe', '#document', 'html', 'body']) !== -1;
    }

    $.fn.isrc_scrollTo = function (target, duration, settings) {
        settings = $.extend({}, $isrc_scrollTo.defaults, settings);
        // Speed is still recognized for backwards compatibility
        duration = duration || settings.duration;
        // Make sure the settings are given right
        let queue;
        settings.offset = both(settings.offset);
        settings.over = both(settings.over);

        return this.each(function () {
            // Null target yields nothing, just like jQuery does
            if (target === null) return;

            let win = isWin(this),
                elem = win ? this.contentWindow || window : this,
                $elem = $(elem),
                targ = target,
                attr = {},
                toff;

            switch (typeof targ) {
                // A number will pass the regex
                case 'number':
                case 'string':
                    if (/^([+-]=?)?\d+(\.\d+)?(px|%)?$/.test(targ)) {
                        targ = both(targ);
                        // We are done
                        break;
                    }
                    // Relative/Absolute selector
                    targ = win ? $(targ) : $(targ, elem);
                /* falls through */
                case 'object':
                    if (targ.length === 0) return;
                    // DOMElement / jQuery
                    if (targ.is || targ.style) {
                        // Get the real position of the target
                        toff = (targ = $(targ)).offset();
                    }
            }

            let offset = $.isFunction(settings.offset) && settings.offset(elem, targ) || settings.offset;

            $.each(settings.axis.split(''), function (i, axis) {
                let Pos = axis === 'x' ? 'Left' : 'Top',
                    pos = Pos.toLowerCase(),
                    key = 'scroll' + Pos,
                    prev = $elem[key](),
                    max = $isrc_scrollTo.max(elem, axis);

                if (toff) {// jQuery / DOMElement
                    attr[key] = toff[pos] + (win ? 0 : prev - $elem.offset()[pos]);

                    // If it's a dom element, reduce the margin
                    if (settings.margin) {
                        attr[key] -= parseInt(targ.css('margin' + Pos), 10) || 0;
                        attr[key] -= parseInt(targ.css('border' + Pos + 'Width'), 10) || 0;
                    }

                    attr[key] += offset[pos] || 0;

                    if (settings.over[pos]) {
                        // Scroll to a fraction of its width/height
                        attr[key] += targ[axis === 'x' ? 'width' : 'height']() * settings.over[pos];
                    }
                } else {
                    let val = targ[pos];
                    // Handle percentage values
                    attr[key] = val.slice && val.slice(-1) === '%' ?
                        parseFloat(val) / 100 * max
                        : val;
                }

                // Number or 'number'
                if (settings.limit && /^\d+$/.test(attr[key])) {
                    // Check the limits
                    attr[key] = attr[key] <= 0 ? 0 : Math.min(attr[key], max);
                }

                // Don't waste time animating, if there's no need.
                if (!i && settings.axis.length > 1) {
                    if (prev === attr[key]) {
                        // No animation needed
                        attr = {};
                    } else if (queue) {
                        // Intermediate animation
                        animate(settings.onAfterFirst);
                        // Don't animate this axis again in the next iteration.
                        attr = {};
                    }
                }
            });

            animate(settings.onAfter);

            function animate(callback) {
                let opts = $.extend({}, settings, {
                    // The queue setting conflicts with animate()
                    // Force it to always be true
                    queue: true,
                    duration: duration,
                    complete: callback && function () {
                        callback.call(elem, targ, settings);
                    }
                });
                $elem.animate(attr, opts);
            }
        });
    };

    // Max scrolling position, works on quirks mode
    // It only fails (not too badly) on IE, quirks mode.
    $isrc_scrollTo.max = function (elem, axis) {
        let Dim = axis === 'x' ? 'Width' : 'Height',
            scroll = 'scroll' + Dim;

        if (!isWin(elem))
            return elem[scroll] - $(elem)[Dim.toLowerCase()]();

        let size = 'client' + Dim,
            doc = elem.ownerDocument || elem.document,
            html = doc.documentElement,
            body = doc.body;

        return Math.max(html[scroll], body[scroll]) - Math.min(html[size], body[size]);
    };

    function both(val) {
        return $.isFunction(val) || $.isPlainObject(val) ? val : {top: val, left: val};
    }

    // Add special hooks so that window scroll properties can be animated
    $.Tween.propHooks.scrollLeft =
        $.Tween.propHooks.isrc_scrollTop = {
            get: function (t) {
                return $(t.elem)[t.prop]();
            },
            set: function (t) {
                var curr = this.get(t);
                // If interrupt is true and user scrolled, stop animating
                if (t.options.interrupt && t._last && t._last !== curr) {
                    return $(t.elem).stop();
                }
                var next = Math.round(t.now);
                // Don't waste CPU
                // Browsers don't render floating point scroll
                if (curr !== next) {
                    $(t.elem)[t.prop](next);
                    t._last = this.get(t);
                }
            }
        };

    // AMD requirement
    return $isrc_scrollTo;
});