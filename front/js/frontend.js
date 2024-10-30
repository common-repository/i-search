/* start frontend */
let iSearchActivate = function () {
    "use strict";
    var $ = jQuery.noConflict();
    let el = $('.isrc-s').not('.isrc-active');

    el.each(function () {
        $(this).addClass('isrc-active');
        let $t = $(this),
            datakeyNo = $t.data('key'),
            datakey = window[datakeyNo],
            ajaxurl = isrc_params.ajax_url,
            append_to = $t.closest('.isrc-ajaxsearchform-container'),
            atc_btn = (isrc_params.atc_btn === 'yes'),
            atc_label = isrc_params.atc_label,
            close_icon = datakey.close_icon || isrc_params.xclose,
            custom_order = datakey.custom_order,
            popular_max = datakey.popular_max,
            ph_advert = datakey.ph_advert,
            ph_adverts = datakey.ph_adverts,
            placeholder = datakey.placeholder,
            featured_label = isrc_params.featured_label,
            haveSubmitBtn = datakey.submit_btn,
            is_preview = datakey.is_preview || false,
            isTabbed = (datakey.tabs_enabled !== 'undefined') ? datakey.tabs_enabled : false,
            locale = (datakey.locale !== 'undefined') ? datakey.locale : isrc_params.locale,
            shortcodeID = (datakey.shortcode_id !== 'undefined') ? datakey.shortcode_id : 0,
            wrapperClass = '.isrc_sc_' + shortcodeID,
            loader_icon = datakey.loader_icon,
            maxHeight = datakey.max_height || isrc_params.maxHeight,
            limit = (datakey.limit !== 'undefined') ? datakey.limit : isrc_params.limit,
            logging = (datakey.logging !== 'undefined') ? datakey.logging : false,
            min_chars = (datakey.min_chars !== 'undefined') ? datakey.min_chars : 3,
            order_by = datakey.order_by,
            orientation = datakey.orientation || 'auto',
            offset_top = (datakey.offset_top !== 'undefined') ? datakey.offset_top : 0,
            outofstock_label = isrc_params.outofstock_label,
            instock_label = isrc_params.instock_label,
            detailed_stock = (isrc_params.detailed_stock === 'yes'),
            backorder_label = isrc_params.backorder_label,
            show_popularity = datakey.show_popularity,
            preventBadQueries = (logging === false),
            ptdiv_enabled = (datakey.ptdiv_enabled !== 'undefined') ? datakey.ptdiv_enabled : false,
            sale_label = isrc_params.sale_label,
            search_in = (datakey.search_in !== 'undefined') ? datakey.search_in : false,
            sendMeanings = (logging === true),
            show_cat = (isrc_params.show_cat === 'yes'),
            ed_noresult = datakey.ed_noresult,
            noresults_lbl = datakey.noresult_label,
            ed_didumean = datakey.ed_didumean,
            didumean_label = datakey.didumean_label,
            log_popularity = datakey.log_popularity,
            popular_label = datakey.popular_label,
            overlay_click_outside = true,
            ed_viewall = (datakey.ed_viewall !== 'undefined') ? datakey.ed_viewall : false,
            viewall_label = datakey.viewall_label,
            suggestionClass = 'isrc_autocomplete-suggestion',
            ed_continue = (datakey.ed_continue !== 'undefined') ? datakey.ed_continue : false,
            sug_w = (datakey.sug_w !== 'undefined') ? datakey.sug_w : 'auto',
            theme = datakey.theme || 'clean';

        $t.isrcautocomplete({
            datakeyNo: datakeyNo,
            appendTo: append_to,
            offsetTop: offset_top,
            overlayClickOutsideClose: overlay_click_outside,
            shortcodeID: shortcodeID,
            placeholder: placeholder,
            phAdvert: ph_advert,
            phAdverts: ph_adverts,
            autoLoadMore: ed_continue,
            loadMoreBtn: ed_viewall,
            atc_btn: atc_btn,
            atc_label: atc_label,
            autoSelectFirst: (theme === 'advanced'),
            close_icon: close_icon,
            customOrder: custom_order,
            diduMeanEnabled: ed_didumean,
            diduMeanLabel: didumean_label,
            haveSubmitBtn: haveSubmitBtn,
            isPreview: is_preview,
            isTabbed: isTabbed,
            limit: limit,
            suggestionsWidth: sug_w,
            loader_icon: loader_icon,
            logging: logging,
            maxHeight: maxHeight,
            locale: locale,
            minChars: min_chars,
            noResultsEnabled: ed_noresult,
            noSuggestionNotice: noresults_lbl,
            popularEnabled: log_popularity,
            popularLabel: popular_label,
            orientation: orientation,
            params: {
                action: isrc_params.ajax_action,
                locale: locale,
                instance: shortcodeID,
                search_in: search_in,
                limit: limit,
                order_by: order_by,
                logging: logging,
                log_popularity: log_popularity,
                show_popularity: show_popularity,
                sh_didumean: ed_didumean,
                hash: isrc_params.hashvar,
                popular_max: popular_max
            },
            preventBadQueries: preventBadQueries,
            search_in: search_in,
            sendMeanings: sendMeanings,
            sendPopularity: show_popularity,
            serviceUrl: ajaxurl,
            suggestionClass: suggestionClass,
            wrapperClass: wrapperClass,
            template: theme,
            themeFunctionsMouseOver: function (that, el) {
                let template = that.options.template;
                if (template === 'advanced') {
                    advanced_template_functions_mouseOver(that, el);
                }

            },
            themeFunctionsMouseLeave: function (that, el) {
                let template = that.options.template;
                if (template === 'advanced') {
                    advanced_template_functions_mouseLeave(that, el);
                }

            },
            onShow: function (that) {
                let template = that.options.template;
                if (template === 'advanced') {
                    let container = $(that.suggestionsContainer),
                        el = container.find('.' + suggestionClass).first();
                    advanced_template_functions_mouseOver(that, el);
                }
            },
            onSuggest: function (that) {
                let container = $(that.suggestionsContainer),
                    el = container.find('.' + suggestionClass).first(),
                    template = that.options.template;
                if (template === 'advanced') {
                    advanced_template_functions_mouseOver(that, el);
                }
            },
            onTabClick: function (that) {
                let template = that.options.template;
                if (template === 'advanced') {
                    advanced_template_functions_tabclick(that);
                }
            },
            onActivate: function (that) {
                let template = that.options.template;
                if (template === 'advanced') {
                    advanced_template_functions_tabclick(that);
                }
            },
            triggerSelectOnValidInput: false,
            htmlBodyRender: function (that) {
                let template = that.options.template;
                if (template === 'clean') {
                    return template_clean(that);
                }
                if (template === 'advanced') {
                    return template_advanced(that);
                }
            }
        });

        let truncateString = function (sentence, amount, tail) {
            if (amount === 0) {
                return '';
            }
            const words = sentence.split(' ');
            if (amount >= words.length) {
                return sentence;
            }
            const truncated = words.slice(0, amount);
            return `${truncated.join(' ')}${tail}`;
        };

        let template_clean = function (that) {
            let options = that.options,
                html = '',
                atc_btn = that.options.atc_btn,
                atc_label = that.options.atc_label,
                datai = 0,
                tabs_inner_html = '',
                isTabbed = options.isTabbed,
                className = that.classes.suggestion,
                suggestions = that.suggestions,
                currentTab = that.currentTab,
                curr_post_type = false,
                tabs_outer_html = '',
                pattern = $.IsrcAutocomplete.utils.escapeRegExChars(that.currentValue),
                css = '',
                limits = that.limits,
                sql_limit = that.options.limit,
                havemore = 0,
                classNameHide = '',
                ptlabels = options.customOrder;


            html += '<div class="suggestions-wrap">';

            $.each(suggestions, function (typekey, suggestions_top) {

                havemore = (limits[typekey] > sql_limit) ? 1 : 0;

                if (!isTabbed && ptdiv_enabled) {
                    html += '<div class="isrc_pt_div">' + ptlabels[typekey].label + '</div>';
                }

                if (isTabbed) {

                    let tabLabel = ptlabels[typekey].label;

                    if (currentTab === false) {
                        /* no tab is selected maybe its the fisrt time */
                        css = 'selected';
                        currentTab = typekey;
                    } else if (currentTab === typekey) {
                        css = 'selected';
                    } else {
                        css = '';
                    }

                    tabs_inner_html += '<div data-havemore="' + havemore + '" data-tabid="' + typekey + '" class="tab-result ' + css + '">' + tabLabel + '</div>';

                    tabs_outer_html = '<div class="suggestion-tabs">' + tabs_inner_html + '</div>';

                    if (currentTab !== false) {
                        curr_post_type = currentTab;
                    } else if (currentTab === false) {
                        // if no tab is selected or its the first time set the first tab as selected.
                        curr_post_type = typekey;
                    }
                    classNameHide = '';
                    if (typekey !== curr_post_type && isTabbed) {
                        classNameHide = ' isrc_hidesuggestion';
                    }

                }

                let image_enabled = that.options.customOrder[typekey].have_img,
                    title_only = that.options.customOrder[typekey].title_only,
                    exc_multi_line = that.options.customOrder[typekey].exc_multi_line,
                    excerptLength = that.options.customOrder[typekey].exc_max_words;

                if (!image_enabled) {
                    classNameHide += ' isrc-no-img';
                }
                if (title_only) {
                    classNameHide += ' isrc-title-only';
                }

                html += '<div class="suggestion-wrap ' + classNameHide + '" data-typekey="' + typekey + '">';

                $.each(suggestions_top, function (i, suggestion) {
                    if (typeof suggestion.ptn !== 'undefined') {


                        html += '<div data-typekey="' + typekey + '" class="' + className + '" data-index="' + datai + '" data-index2="' + i + '">';

                        /* add to cart button */
                        if (atc_btn && suggestion.ptn === 'product' && typeof suggestion.atc_off !== 'undefined' && suggestion.atc_off === false) {

                            html += '<div class="isrc_atc">';

                            if (isrc_params.ed_buynow === 'yes') {
                                html += '<a href="/?add-to-cart=' + suggestion.id + '&quantity=1&isrc_buy_now=1" class="button product_type_simple add_to_cart_button isrc-buynow-btn" rel="nofollow">' + isrc_params.buynow_label + '</a>';
                            }

                            html += '<a href="/?wc-ajax=1&add-to-cart=' + suggestion.id + '" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' + suggestion.id + '" aria-label="Add “' + suggestion.value + '” to your cart" rel="nofollow">' + atc_label + '</a>';
                            html += '</div>'
                        }


                        let image = suggestion.cnt + suggestion.img,
                            classdivname = 'isrc_result_image align-left';

                        if (typeof suggestion.img !== 'undefined' && image_enabled) {
                            if (suggestion.img === '/plugins/i_search_pro/front/css/img/blank50.png' && (!!(isrc_params.no_image))) {
                                /* no image (will removed in future versions)*/
                                image = isrc_params.no_image;
                                classdivname += ' sug-no-img';
                            }
                            html += '<div class="' + classdivname + '"><img src="' + image + '"></div>';
                        }

                        let title = suggestion.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
                        title += isearch_format_extras(suggestion, title_only, 'append_to_title', that);

                        html += '<div class="isrc_result_content">';
                        html += '<div class="title">' + title + '</div>';
                        html += isearch_format_extras(suggestion, title_only, 'after_title', that);

                        if (!title_only && typeof suggestion.p_cats !== 'undefined' && suggestion.p_cats !== '' && suggestion.p_cats != null && show_cat) {
                            let value = suggestion.p_cats.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>'),
                                cat_label = (typekey === 'pt_product') ? isrc_params.cat_label : isrc_params.post_cat_label;

                            html += '<div class="sug_cats">';
                            html += (cat_label.length > 0) ? cat_label + ' ' : '';
                            html += value;
                            html += '</div>';
                        }

                        html += isearch_format_extras(suggestion, title_only, 'after_categories', that);

                        if (!title_only && typeof suggestion.price !== 'undefined' && suggestion.price !== '') {
                            html += '<div class="sug_price">';
                            html += suggestion.price;
                            html += '</div>';
                        }

                        html += isearch_format_extras(suggestion, title_only, 'after_price', that);

                        if (!title_only && typeof suggestion.excerpt !== 'undefined' && suggestion.excerpt !== '' && suggestion.excerpt != null) {
                            let excerptClass = (exc_multi_line) ? 'nowrap' : '',
                                excerpt = truncateString(suggestion.excerpt, excerptLength, '...');

                            html += '<div class="sug_exc ' + excerptClass + '">';
                            html += excerpt;
                            html += '</div>';
                        }
                        html += isearch_format_extras(suggestion, title_only, 'after_excerpt', that);
                        html += '</div>'; // end div result content
                        html += '<div class="sug_badges">';
                        html += isearch_format_extras(suggestion, title_only, 'before_badges', that);

                        if (!title_only && typeof suggestion.badges !== 'undefined') {
                            $.each(suggestion.badges, function (key) {

                                if (key === 'on_sale' && sale_label.length > 0) {
                                    html += '<div class="isrc_badge sug_onsale">';
                                    html += sale_label;
                                    html += '</div>';
                                }

                                if (key === 'outofstock' && outofstock_label.length > 0) {
                                    html += '<div class="isrc_badge sug_outofstock">';
                                    html += outofstock_label;
                                    html += '</div>';
                                }

                                if (key === 'backorder' && backorder_label.length > 0) {
                                    html += '<div class="isrc_badge sug_backorder">';
                                    html += backorder_label;
                                    html += '</div>';
                                }

                                if (key === 'featured' && featured_label.length > 0) {
                                    html += '<div class="isrc_badge sug_featured">';
                                    html += featured_label;
                                    html += '</div>';
                                }
                            });
                        }

                        html += isearch_format_extras(suggestion, title_only, 'after_badges', that);
                        html += '</div>';
                        html += '</div>';


                    }
                    datai++;

                });


                if (ed_viewall && havemore) {
                    html += '<div data-pagination="1" data-typekey="' + typekey + '" class="link-result isrcviewall">';
                    html += '<span class="load-more-label">' + viewall_label + '</span>';
                    html += '<div class="loadmore-spinner isrc_hide"><div class="rect1"></div>';
                    html += '<div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>';
                    html += '</div>';
                }

                // end div suggestion wrap
                html += '</div>';
            });

            // end div suggestions wrap
            html += '</div>';


            html = tabs_outer_html + html;

            return html;
        };


        let template_advanced = function (that) {
            let options = that.options,
                html = '',
                datai = 0,
                tabs_inner_html = '',
                isTabbed = options.isTabbed,
                className = that.classes.suggestion,
                suggestions = that.suggestions,
                currentTab = that.currentTab,
                curr_post_type = false,
                tabs_outer_html = '',
                pattern = $.IsrcAutocomplete.utils.escapeRegExChars(that.currentValue),
                css = '',
                limits = that.limits,
                sql_limit = that.options.limit,
                havemore = 0,
                classNameHide = '',
                transformclass = '',
                ptlabels = options.customOrder,
                isVisible = $('.isrc_autocomplete-suggestions').is(':visible'),
                isVisibleClass = (isVisible) ? "is-visible" : "is-not-visible";

            html += '<div class="suggestions-wrap ' + isVisibleClass + '">';
            html += '<div class="suggestions-left-wrap">';

            $.each(suggestions, function (typekey, suggestions_top) {

                havemore = (limits[typekey] > sql_limit) ? 1 : 0;

                if (!isTabbed && ptdiv_enabled) {
                    html += '<div class="isrc_pt_div">' + ptlabels[typekey].label + '</div>';
                }

                if (isTabbed) {

                    let tabLabel = ptlabels[typekey].label;

                    if (currentTab === false) {
                        /* no tab is selected maybe its the fisrt time */
                        css = 'selected';
                        currentTab = typekey;
                    } else if (currentTab === typekey) {
                        css = 'selected';
                    } else {
                        css = '';
                    }

                    tabs_inner_html += '<div data-havemore="' + havemore + '" data-tabid="' + typekey + '" class="tab-result ' + css + '">' + tabLabel + '</div>';

                    tabs_outer_html = '<div class="suggestion-tabs">' + tabs_inner_html + '</div>';

                    if (currentTab !== false) {
                        curr_post_type = currentTab;
                    } else if (currentTab === false) {
                        // if no tab is selected or its the first time set the first tab as selected.
                        curr_post_type = typekey;
                    }
                    classNameHide = '';
                    if (typekey !== curr_post_type && isTabbed) {
                        classNameHide = ' isrc_hidesuggestion';
                    }

                }

                let image_enabled = that.options.customOrder[typekey].have_img,
                    title_only = that.options.customOrder[typekey].title_only;

                if (!image_enabled) {
                    classNameHide += ' isrc-no-img';
                }
                if (title_only) {
                    classNameHide += ' isrc-title-only';
                }

                html += '<div class="suggestion-wrap ' + classNameHide + '" data-typekey="' + typekey + '">';

                $.each(suggestions_top, function (i, suggestion) {
                    if (typeof suggestion.ptn !== 'undefined') {


                        html += '<div data-typekey="' + typekey + '" class="' + className + '" data-index="' + datai + '" data-index2="' + i + '">';
                        let classdivname = 'isrc_result_image align-left';

                        if (typeof suggestion.img !== 'undefined' && image_enabled) {
                            let image = suggestion.cnt + suggestion.img;
                            if (suggestion.img === '/plugins/i_search_pro/front/css/img/blank50.png' && (!!(isrc_params.no_image))) {
                                /* no image (will removed in future versions)*/
                                image = isrc_params.no_image;
                                classdivname += ' sug-no-img';
                            }
                            html += '<div class="' + classdivname + '"><img src="' + image + '"></div>';
                        }

                        if (typeof suggestion.img2 !== 'undefined' && image_enabled) {
                            let image2 = suggestion.cnt + suggestion.img2;
                            if (suggestion.img2 === '/plugins/i_search_pro/front/css/img/blank50.png' && (!!(isrc_params.no_image_x2))) {
                                /* no image (will removed in future versions)*/
                                image2 = isrc_params.no_image_x2;
                            }
                            html += '<span style="display:none"><img src="' + image2 + '"></span>';
                        }

                        let title = suggestion.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
                        title += isearch_format_extras(suggestion, title_only, 'append_to_title', that);
                        html += '<div class="isrc_result_content">';
                        html += '<div class="title">' + title + '</div>';
                        html += '</div>'; // end div result content

                        if (typeof suggestion.price !== 'undefined' && suggestion.price !== '') {
                            let css = (suggestion.price.includes('&ndash;')) ? 'isrc-price-w-sep' : '';
                            html += '<div class="sug_price ' + css + '">';
                            html += suggestion.price.replace('&ndash;', '');
                            html += '</div>';
                        }

                        html += '<div class="sug_badges">';
                        html += isearch_format_extras(suggestion, title_only, 'before_badges', that);

                        if (!title_only && typeof suggestion.badges !== 'undefined') {
                            $.each(suggestion.badges, function (key) {

                                if (key === 'on_sale' && sale_label.length > 0) {
                                    html += '<div class="isrc_badge sug_onsale">';
                                    html += sale_label;
                                    html += '</div>';
                                }

                                if (key === 'outofstock' && outofstock_label.length > 0) {
                                    html += '<div class="isrc_badge sug_outofstock">';
                                    html += outofstock_label;
                                    html += '</div>';
                                }

                                if (key === 'backorder' && backorder_label.length > 0) {
                                    html += '<div class="isrc_badge sug_backorder">';
                                    html += backorder_label;
                                    html += '</div>';
                                }

                                if (key === 'featured' && featured_label.length > 0) {
                                    html += '<div class="isrc_badge sug_featured">';
                                    html += featured_label;
                                    html += '</div>';
                                }
                            });
                        }
                        html += isearch_format_extras(suggestion, title_only, 'after_badges', that);
                        html += '</div>';
                        html += '</div>';


                    }
                    datai++;

                }); //end each level suggestions 2nd

                if (ed_viewall && havemore) {
                    html += '<div data-pagination="1" data-typekey="' + typekey + '" class="link-result isrcviewall">';
                    html += '<span class="load-more-label">' + viewall_label + '</span>';
                    html += '<div class="loadmore-spinner isrc_hide"><div class="rect1"></div>';
                    html += '<div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>';
                    html += '</div>';
                }

                // end div suggestion wrap
                html += '</div>';

            }); // end suggestions each level top

            // end div left wrap
            html += '</div>';

            // start right wrap
            if (that.ajaxingType === 'normal') {
                transformclass = 'isrc-transform-out';
            } else {
                transformclass = 'isrc-transform-in';
            }
            html += '<div class="suggestions-right-wrap ' + transformclass + '">';
            // start end detail container
            html += '<div class="isrc-details-wrap"></div>';
            // end right wrap
            html += '</div>';

            // end div suggestions wrap
            html += '</div>';

            html = tabs_outer_html + html;
            return html;
        };

        let advanced_template_functions_mouseOver = function (that, el) {
            // build details
            advanced_template_details_builder(that, el);
        };

        let advanced_template_functions_mouseLeave = function (that, el) {

        };

        let advanced_template_functions_tabclick = function (that) {
            let selectedIndex = that.selectedIndex,
                container = $(that.suggestionsContainer),
                el = container.find('.isrc_autocomplete-suggestion[data-index="' + selectedIndex + '"]'),
                right_wrap = container.find('.suggestions-right-wrap');
            right_wrap.removeClass('isrc-transform-in');
            right_wrap.addClass('isrc-transform-out');
            setTimeout(
                function () {
                    advanced_template_details_builder(that, el);
                }, 350);
        };

        let advanced_template_details_builder = function (that, element) {
            if (that.sugormean !== 'suggestions') {
                return false;
            }

            /* first start without tabs*/
            if (typeof that.selectedIndex === 'undefined') {
                that.selectedIndex = 0;
                that.selectedIndex2 = 0;
                that.selectedtypekey = Object.keys(that.suggestions)[0];
            }

            let html = '',
                pattern = $.IsrcAutocomplete.utils.escapeRegExChars(that.currentValue),
                currentTab = (that.currentTab === false) ? that.selectedtypekey : that.currentTab,
                suggestions = that.suggestions[currentTab],
                container = $(that.suggestionsContainer),
                atc_btn = that.options.atc_btn,
                atc_label = that.options.atc_label,
                classdivname = 'isrc_details_img',
                typekey = currentTab,
                selectedIndex2 = (typeof that.lastIndexes2[typekey] !== 'undefined') ? that.lastIndexes2[typekey] : 0,
                suggestion = suggestions[selectedIndex2],
                image_enabled = that.options.customOrder[typekey].have_img,
                el = container.find('.isrc_autocomplete-suggestion[data-index2="' + selectedIndex2 + '"][data-typekey="' + typekey + '"]'),
                title_only = that.options.customOrder[typekey].title_only,
                excerptLength = that.options.customOrder[typekey].exc_max_words,
                exc_multi_line = that.options.customOrder[typekey].exc_multi_line;

            container.find('.detail-selector').removeClass('detail-selector');
            el.addClass('detail-selector');
            /* build the container content */
            /* START TOP content */
            html = '<div class="isrc-details-inner">';

            /* image */
            if (typeof suggestion.img2 !== 'undefined' && image_enabled) {
                let image = suggestion.cnt + suggestion.img2;
                if (suggestion.img2 === '/plugins/i_search_pro/front/css/img/blank50.png' && (!!(isrc_params.no_image_x2))) {
                    /* no image (will removed in future versions)*/
                    image = isrc_params.no_image_x2;
                    classdivname += ' sug-no-img';
                }
                html += '<div class="slctr ' + classdivname + '"><img src="' + image + '"></div>';
            }

            /* after img */
            if (typeof suggestion.value !== 'undefined') {
                let title = suggestion.value;
                title += isearch_format_extras(suggestion, title_only, 'append_to_title', that);
                html += '<div class="isrc_details_after_img"><span class="slctr adv_title_isrc">' + title + '</span>';
            }
            html += isearch_format_extras(suggestion, title_only, 'after_title', that);

            if (typeof suggestion.price !== 'undefined' && suggestion.price !== '' && !title_only) {
                html += '<div class="isrc_details_price">';
                html += suggestion.price;
                html += '</div>';
            }


            if (typeof suggestion.rtng !== 'undefined' && suggestion.rtng !== '' && !title_only) {
                html += '<div class="sug_rating">';
                html += '<div class="isrc_star-rating">';
                html += '<span style="width:' + suggestion.rtng.rat_p + '%"></span>';
                html += '</div>';
                html += '<span class="isrc_review_cnt">(' + suggestion.rtng.rat_c + ')</span>';
                html += '</div>';
            }

            html += isearch_format_extras(suggestion, title_only, 'after_price', that);

            if (typeof suggestion.wc_type !== 'undefined' && suggestion.wc_type === 'variable' && !title_only) {
                let attr_labels = '',
                    attr_html = '',
                    attr_css = '',
                    stock_label = '';

                if (typeof suggestion.wc_var !== 'undefined' && detailed_stock) {
                    $.each(suggestion.wc_var, function (key, val) {
                        if (typeof val.attr_labels !== 'undefined') {
                            attr_labels = val.attr_labels.join(', ');
                        }
                        if (val.is_in_stock) {
                            stock_label = instock_label;
                            attr_css = 'isrc-inst';
                        } else {
                            stock_label = outofstock_label;
                            attr_css = 'isrc-oost';
                        }
                        attr_html += '<span class="isrc-pattr ' + attr_css + '">' + attr_labels + ': <span class="isrc-pattrstck">' + stock_label + '</span></span>';
                    });
                }
                html += '<div class="isrc-attr-outer">';
                html += attr_html;
                html += '</div>';
            }

            html += '</div>';

            html += '</div>';
            /* END TOP content */

            /* START MIDDLE content */
            html += '<div class="isrc-details-inner isrc-details-middle">';
            html += isearch_format_extras(suggestion, title_only, 'before_categories', that);

            if (typeof suggestion.p_cats !== 'undefined' && suggestion.p_cats !== '' && suggestion.p_cats != null && isrc_params.show_cat === 'yes' && !title_only) {
                let value = suggestion.p_cats.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>'),
                    cat_label = (typekey === 'pt_product') ? isrc_params.cat_label : isrc_params.post_cat_label;
                html += '<div class="sug_cats">';
                html += (cat_label.length > 0) ? cat_label + ' ' : '';
                html += value;
                html += '</div>';
            }

            html += isearch_format_extras(suggestion, title_only, 'before_excerpt', that);

            if (typeof suggestion.excerpt !== 'undefined' && !title_only) {
                let excerptClass = (exc_multi_line) ? 'nowrap' : '',
                    excerpt = truncateString(suggestion.excerpt, excerptLength, '...');

                html += '<div class="isrc_details_excerpt ' + excerptClass + '">' + excerpt + '</div>';
            }

            html += isearch_format_extras(suggestion, title_only, 'after_excerpt', that);

            /* close inner div */
            html += '</div>';

            /* add to cart button */
            if (atc_btn && suggestion.ptn === 'product' && typeof suggestion.atc_off !== 'undefined' && suggestion.atc_off === false) {

                html += '<div class="isrc_atc">';

                if (isrc_params.ed_buynow === 'yes') {
                    html += '<a href="/?add-to-cart=' + suggestion.id + '&quantity=1&isrc_buy_now=1" class="button product_type_simple add_to_cart_button isrc-buynow-btn" rel="nofollow">' + isrc_params.buynow_label + '</a>';
                }

                html += '<a href="/?wc-ajax=1&add-to-cart=' + suggestion.id + '" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' + suggestion.id + '" aria-label="Add “' + suggestion.value + '” to your cart" rel="nofollow">' + atc_label + '</a>';
                html += '</div>'
            }

            /* END MIDDLE content */
            let details_inner = container.find('.isrc-details-wrap'),
                right_wrap = container.find('.suggestions-right-wrap');

            details_inner.html('');
            details_inner.html(html);
            right_wrap.addClass('isrc-transform-in');

        }


    });
};


let isearch_format_extras = function (suggestion, title_only, extra_name, that) {

    if (title_only || typeof isrc_params.builder !== 'object' || typeof suggestion.cb === 'undefined') {
        return '';
    }

    let builder = isrc_params.builder,
        datakeyNo = that.options.datakeyNo,
        $ = jQuery,
        ptn = suggestion.ptn,
        pt = (suggestion.type === 'post_type') ? 'pt_' + ptn : 'tx_' + ptn,
        isMobile = that.isMobile,
        pattern = $.IsrcAutocomplete.utils.escapeRegExChars(that.currentValue),
        retrundata = '',
        instanceCbAllow = true;
    if (typeof window[datakeyNo] !== 'undefined' && window[datakeyNo].cb_flds !== false) {
        let instanceCB = window[datakeyNo].cb_flds;
        instanceCbAllow = !(typeof instanceCB[pt] !== 'undefined' && typeof instanceCB[pt][extra_name] !== 'undefined' && instanceCB[pt][extra_name] === true);
    }

    /* check if exists */
    if (builder[ptn] && typeof builder[ptn][extra_name] !== 'undefined' && instanceCbAllow) {
        let currentBuilder = builder[ptn][extra_name],
            sugBuilder = suggestion.cb;

        $.each(currentBuilder, function (key, val) {
            let dataKey = val.data_key.replace('_cb_ex_mk_', ''),
                content = '',
                sugKey = (val.data_type === 'meta_key') ? 'mk_' + dataKey : 'tx_' + dataKey;
            if (typeof sugBuilder[sugKey] !== 'undefined') {
                let value = sugBuilder[sugKey].val,
                    label = val.label,
                    hide_on_mobile = (val.mobileHide === 'true'),
                    clickAble = (val.clickAble === 'true'),
                    clickCss = '';
                if (clickAble) {
                    clickCss = 'isrc_clickable';
                }

                if (hide_on_mobile && isMobile) {
                } else {
                    if (value.includes('<a ') || label.includes('<a ')) {
                    } else {
                        if (clickAble && value.includes(', ')) {
                            let cntArray = value.split(', '),
                                newArray = [];
                            $.each(cntArray, function (key, val) {
                                newArray.push('<iclick>' + val + '</iclick>');
                            });
                            value = newArray.join(', ');
                        } else if (clickAble) {
                            value = '<iclick>' + value + '</iclick>';
                        }
                    }

                    value = value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
                    if (label) {
                        content = '<span class="ex_lbl">' + label + '</span><span class="ex_val">' + value + '</span>';
                    } else {
                        content = '<span class="ex_val">' + value + '</span>';
                    }
                    if (label.indexOf('%s') !== -1) {
                        content = '<span class="ex_val">' + label.replace(/%s/g, value) + '</span>';
                    }
                    retrundata += '<span class="isrc_extras ' + clickCss + ' ex_' + key + ' ' + sugKey + '">' + content + '</span>';
                }

            }
        });

    }
    return retrundata;

};

let isearchInMenu = function () {
    let $ = jQuery.noConflict(),
        scIds = [],
        menuHeight = $("li[class*='menu-item-type-isearch']").outerHeight(),
        data = {};

    $("li[class*='menu-item-type-isearch']").each(function () {
        let $element = $(this),
            classList = $element.attr('class').split(/\s+/),
            scId = 0;

        $.each(classList, function (key, val) {
            if (val.indexOf('isrc-sc-menu_') !== -1) {
                scId = val.replace('isrc-sc-menu_', '');
                if (!isNaN(scId)) {
                    scId = parseInt(scId);
                    scIds.push(scId);
                }
            }
        });
    });

    if (scIds.length < 1) {
        return false;
    }

    data.action = 'isrc_get_instance';
    data.output = 'json';
    data.shortcodes = scIds;
    data.locale = isrc_params.locale;
    $.ajax({
        url: isrc_params.ajax_url_org,
        data: data,
        type: 'POST',
        dataType: 'json',
    }).done(function (data) {
        $.each(data, function (key, val) {
            if (!val) {
                return;
            }
            let classname = '.isrc-sc-menu_' + key;
            $(classname).html(val);
            let currentMenuHeight = $(classname).outerHeight();
            // equalize menu height.
            if (menuHeight > currentMenuHeight) {
                let paddings = (menuHeight - currentMenuHeight) / 2;
                // TODO
                // $(classname).css({'padding': paddings});
            }
        });
        iSearchActivate();
    }).fail(function (jqXHR, textStatus, errorThrown) {

    });

};
jQuery(function () {
    iSearchActivate();
    isearchInMenu();
});
var timeoutId;
