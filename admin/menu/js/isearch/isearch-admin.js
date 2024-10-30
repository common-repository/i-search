(function ($) {

    $.fn.serialize = function (options) {
        return $.param(this.serializeArray(options));
    };

    $.fn.serializeArray = function (options) {
        var o = $.extend({
            checkboxesAsBools: false
        }, options || {});

        var rselectTextarea = /select|textarea/i;
        var rinput = /text|hidden|password|number|search/i;

        return this.map(function () {
            return this.elements ? $.makeArray(this.elements) : this;
        })
            .filter(function () {
                return this.name && !this.disabled &&
                    (this.checked
                        || (o.checkboxesAsBools && this.type === 'checkbox')
                        || rselectTextarea.test(this.nodeName)
                        || rinput.test(this.type));
            })
            .map(function (i, elem) {
                var val = $(this).val();
                return val == null ?
                    null :
                    $.isArray(val) ?
                        $.map(val, function (val, i) {
                            return {name: elem.name, value: val};
                        }) :
                        {
                            name: elem.name,
                            value: (o.checkboxesAsBools && this.type === 'checkbox') ? //moar ternaries!
                                (this.checked ? 'true' : 'false') :
                                val
                        };
            }).get();
    };

})(jQuery);
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

    function Isearch_init(el, options) {
        var noop = function () {
            },
            that = this,
            defaults = {
                page: (typeof page !== 'undefined') ? page : false,
                label_taxonomy: 'Taxonomy',
                label_label: 'Label',
                label_remove: 'Remove',
                advancedTabNonce: '',
                confirm_value: 'This action cannot be undone.',
                confirm_value2: 'This action cannot be undone. It will also delete all isearch taxonomy meta data.',
                clicks_disabled: 'Clicks are disabled in preview',
                preview_src: ''
            };

        // Shared variables:
        that.options = $.extend({}, defaults, options);
        that.current_page = (typeof isearch_current_page !== 'undefined') ? isearch_current_page : false;
        that.current_tab = (typeof isearch_current_tab !== 'undefined') ? isearch_current_tab : false;
        that.tab_label;
        that.tab_taxonomy;
        that.current_remodal;
        that.previewRequest = null;

        // Initialize and set options:
        that.initialize();
    }

    Isearch_init.prototype = {
        killerFn: null,
        initialize: function () {
            let that = this,
                page = that.current_page,
                current_tab = that.current_tab;

            that.apply_global_functions();

            switch (current_tab) {
                case 'settings':
                    this.apply_settings_tab_functions();
                    break;
                case 'advanced_settings':
                    this.apply_advanced_settings_tab_functions();
                    break;
                case 'search_analyze':
                    this.apply_analyze_tab_functions();
                    break;
                case 'scbuilder-list':
                    this.apply_sc_list_tab_functions();
                    break;
                case 'shortcode_add_new':
                    this.apply_sc_new_tab_functions();
                    break;
                case 'content_builder':
                    this.apply_content_builder_tab_functions();
                    break;
            }

        },
        apply_video_tutorial: function () {
            let that = this,
                url = that.options.video_tut_url,
                $videoframe = $('#isrc_vid_tutorial');

            if ($videoframe && url) {
                $(document).on('click', '#isrc_vid_tutorial', function () {
                    window.open(url, '_blank');
                });
            }
        },
        apply_content_builder_tab_functions: function () {
            let that = this,
                copyHelper,
                builderData = that.options.builder_data;

            $.each(builderData, function (key, val) {
                $.each(val, function (key2, val2) {
                    $.each(val2, function (key3, val3) {
                        let source = $('.isrc-metas-source[data-pt="' + key + '"]').find('li[data-type="' + val3.data_type + '"][data-key="' + val3.data_key + '"]');
                        if (source.length > 0) {
                            let target = $('.isrc-metas-target[data-pt="' + key + '"]').find('ul[data-jskey="' + key2 + '"]'),
                                clone = source.clone();
                            if (typeof val3.label !== 'undefined') {
                                clone.find('.jslabel').val(val3.label);
                            }
                            if (typeof val3.mobileHide !== 'undefined' && val3.mobileHide === 'true') {
                                clone.find('.jsmobile').prop('checked', true);
                            }
                            if (typeof val3.clickAble !== 'undefined' && val3.clickAble === 'true') {
                                clone.find('.jsclickable').prop('checked', true);
                            }
                            target.append(clone);
                        }
                    });
                });

            });

            that.stickys();

            $("#isrc-meta-data").sticky({
                topSpacing: 97,
                zIndex: 5
            });

            $('.isrc-metas-source ul').each(function () {
                let $connectwith = $(this).closest('.isrc-metakeys-wrapper').find('.connectwith');
                $(this).sortable({
                    connectWith: $connectwith,
                    forcePlaceholderSize: false,
                    placeholder: "ui-state-highlight",
                    helper: function (e, li) {
                        copyHelper = li.clone().insertAfter(li);
                        return li.clone();
                    },
                    stop: function () {
                        copyHelper && copyHelper.remove();
                    }
                });

            });

            $('.isrc-metas-target ul.connectwith').each(function () {
                let $connectwith = $(this).closest('.isrc-metakeys-wrapper').find('.connectwith');
                $(this).sortable({
                    connectWith: $connectwith,
                    placeholder: "ui-state-highlight",
                    receive: function (e, ui) {
                        copyHelper = null;
                    }
                });

            });

            $(document).on('click', '.cb-remove', function () {
                let todelete = $(this).closest('li');
                todelete.slideUp(150, function () {
                    todelete.remove();
                });
            });

            $(document).on('click', '.cb-open', function () {
                let li = $(this).closest('li');
                li.toggleClass('open');
            });

            let excludesObj = {};
            $('#post').submit(function () {

                $('.isrc-metas-target').each(function (index, val) {
                    let $outer_1 = $(this),
                        pt = $outer_1.data('pt');
                    excludesObj[pt] = {};
                    $outer_1.find('ul').each(function () {
                        let $outer_2 = $(this),
                            jskey = $outer_2.data('jskey');
                        excludesObj[pt][jskey] = [];
                        $outer_2.find('li').each(function () {
                            let $li = $(this),
                                dataType = $li.data('type'),
                                dataKey = $li.data('key'),
                                label = $li.find('.jslabel').val(),
                                mobileHide = $li.find('.jsmobile').is(':checked'),
                                clickAble = $li.find('.jsclickable').is(':checked'),
                                targetData = {
                                    data_type: dataType,
                                    data_key: dataKey,
                                    label: label,
                                    mobileHide: mobileHide,
                                    clickAble: clickAble,
                                };
                            excludesObj[pt][jskey].push(targetData)
                        });
                    });
                });

                $('#builder_data').val($.param(excludesObj));

                return true;

            });

            let select2_selector = $('.select2_meta-finder');

            select2_selector.select2({
                theme: 'select2isrc',
                minimumInputLength: 1,
                placeholder: "Start typing...",
                ajax: {
                    url: ajaxurl,
                    dataType: 'json',
                    data: function (params) {
                        return {
                            search: params.term,
                            search_in: 'post_type',
                            action: 'isrc_select2',
                            type: 'posts_for_analyze_screen',
                            _isrc_table_nonce: that.options.select2Nonce,
                        };

                    }
                }
            });

            select2_selector.on("select2:selecting", function (e) {
                let selection = e.params.args.data,
                    post_type = selection.post_type,
                    post_id = selection.id;
                /* get all keys on screen */
                let wrapper = $('.metas-2-outer[data-pt="' + post_type + '"] ul li'),
                    preloader = $('.metas-2-outer[data-pt="' + post_type + '"] .isrc-preview-preloader');
                preloader.show();
                let metadata = {meta_key: [], taxonomy: []};
                $.each(wrapper, function () {
                    let $this = $(this),
                        data_type = $this.data('type'),
                        data_key = $this.data('key');
                    metadata[data_type].push(data_key);
                });

                let ajaxPost = {
                    'action': 'isrc_cnt_example_data',
                    'nonce': that.options.contentBuilderNonce,
                    'post_type': post_type,
                    'post_id': post_id,
                    'meta_data': metadata
                };

                $.ajax({
                    url: ajaxurl,
                    type: 'post',
                    dataType: 'json',
                    data: ajaxPost,
                    success: function (data) {
                        $('.isrc-preview-preloader').hide();
                        if (typeof data.meta_data !== 'undefined') {

                            let $allLi = $('.metas-2-outer ul li.cb-with-example');
                            $allLi.find('.cb-example-data').html('');
                            $allLi.removeClass('cb-with-example');

                            $.each(data.meta_data, function (metaType, value) {
                                $.each(value, function (key, val) {
                                    let $li = $('.metas-2-outer[data-pt="' + post_type + '"] ul li[data-type="' + metaType + '"][data-key="' + key + '"]');
                                    $li.addClass('cb-with-example');
                                    $li.find('.cb-example-data').html(val);
                                });
                            });
                        }
                    }
                });
            });


        },
        apply_sc_list_tab_functions: function () {
            let that = this;

            $(document).on('click', '.sc.sc-cpy', function () {
                let id = $(this).data('sc_id');
                let copyText = document.getElementById("sc_inp_" + id);
                /* Select the text field */
                copyText.select();
                /* Copy the text inside the text field */
                document.execCommand("copy");
            });
            $(document).on('click', '.php_c.sc-cpy', function () {
                let id = $(this).data('phpcode_id');
                let copyText = document.getElementById("php_inp_" + id);
                /* Select the text field */
                copyText.select();
                /* Copy the text inside the text field */
                document.execCommand("copy");
            });

            $('.tooltipcopy').tooltipster({
                trigger: 'custom',
                interactive: true,
                functionReady: function () {
                    $('html').click(function () {
                        $.fn.tooltipster('hide');
                    });
                }
            }).on('hover', function () {
                $(this).tooltipster('content', that.options.label_copy_code);
                $(this).tooltipster('show');
            }).on('mouseout', function () {
                $(this).tooltipster('hide');
            }).on('click', function () {
                $(this).tooltipster('content', that.options.label_copied_code);
            });

            $('.tooltipclone').tooltipster({
                trigger: 'custom',
                interactive: true,
                functionReady: function () {
                    $('html').click(function () {
                        $.fn.tooltipster('hide');
                    });
                }
            }).on('hover', function () {
                $(this).tooltipster('content', that.options.label_clone_sc);
                $(this).tooltipster('show');
            }).on('mouseout', function () {
                $(this).tooltipster('hide');
            }).on('click', function () {
                $(this).tooltipster('content', that.options.label_cloned_sc);
                let instance_id = $(this).closest('.isrc-clone').data('scid');
                if (instance_id) {
                    let status = that.clone_instance(instance_id);
                }
            });

        },
        clone_instance: function (instance_id) {
            if (!instance_id) {
                return false;
            }
            let ajaxPost = {
                'action': 'isrc_clone_instance',
                'nonce': isrc_nonce,
                'instance_id': instance_id,
            };


            $.ajax({
                url: ajaxurl,
                type: 'post',
                dataType: 'json',
                data: ajaxPost,
                success: function (data) {

                    if (data.status === 'success') {
                        location.reload();
                        return true;
                    } else {
                        return false;
                    }

                }
            });

        },
        apply_sc_new_tab_functions: function () {
            let that = this,
                clicks_disabed_txt = that.options.clicks_disabled;

            that.stickys();
            that.reloadPreview();
            that.docu();
            alertify.set('notifier', 'position', 'top-center');
            alertify.defaults.transition = "fade";

            /* codemirror */
            if ($('#codemirror').length) {
                CodeMirror.fromTextArea(document.getElementById('codemirror'), {
                    tabMode: 'indent',
                    theme: 'material',
                    lineNumbers: true,
                    lineWrapping: true,
                    viewportMargin: Infinity,
                }).on('change', editor => {
                    $('#codemirror').val(editor.getValue());
                });
            }

            /* hide WPML lang switcher in top admin bar */
            $('#wp-admin-bar-WPML_ALS').hide();

            $(window).on('previewLoaded', that.previewLoaded);
            $(window).on('previewLoading', that.previewLoading);
            $('.isrc-preview-wrap').sticky({
                topSpacing: 97,
                zIndex: 5
            });
            $('.isrc-css-builder-wrap .isrc-upd-prv').sticky({
                topSpacing: 104,
                zIndex: 5
            });

            let iframebodyBg = $('#preview_bg_color').val();
            if (!iframebodyBg) {
                iframebodyBg = '#f3f3f2';
            }
            $('#preview_bg_color').change(function () {
                document.getElementsByTagName('iframe')[0].iFrameResizer.sendMessage({
                    func: 'body_bg_color',
                    color: $(this).val()
                });
            });
            $('#preview_width').change(function () {
                let width = $(this).val();
                if (!width) {
                    width = '100%';
                }
                $('#isrc_frame').css({width: width});
            });

            $('#isrc_frame').iFrameResize({
                heightCalculationMethod: 'grow',
                inPageLinks: true,
                checkOrigin: false,
                initCallback: function () {
                    $(window).trigger('previewLoaded');
                },
                messageCallback: function (messageData) { // Callback fn when message is received
                    if (messageData.message === 'msg_01') {
                        /* clicks are disabled in preview */
                        if ($('.ajs-message.ajs-visible').length === 0) {
                            alertify.notify(clicks_disabed_txt);
                        }
                    }
                },
            });

            // listen for change for update iframe content
            $(document).on('change', '.prv-upd', function () {
                that.reloadPreview();
            });

            $("#sortable_tabs").sortable({
                placeholder: "ui-state-highlight",
                update: function () {
                    that.reloadPreview();
                }
            });
            $("#sortable").disableSelection();

            $('.sh_preloader').on('click', function () {
                document.getElementsByTagName('iframe')[0].iFrameResizer.sendMessage({
                    func: 'sh_preloader'
                });
            });

            $('#isrc_adv_add').on('click', function () {
                let template = $('#adv_template_row').html(),
                    totalRows = $('.plc-advert-wrap').length,
                    rowNo = totalRows + 1;
                template = template.replace(/TPL_ADV_NO/g, rowNo);
                template = template.replace(/TPL_ADV_ID/g, totalRows);
                $('.plc-adverts-wrap').append(template);
                $('#advert_row_' + totalRows).removeClass('isrc-hpb_0');
            });

            $('.isrc-upd-prv').on('click', function () {
                that.reloadPreview();
            });

            $(document.body).on('click', '.adv-handler', function () {
                let todelete = $(this).closest('.plc-advert-wrap');
                todelete.slideUp(150, function () {
                    todelete.remove();
                    /* reasign index no */
                    $('.plc-advert-wrap').each(function (index) {
                        $(this).find('.adv-no').html(index + 1);
                    });
                    that.reloadPreview();
                });
            });

            $('#isrc_submit_color').data('alpha', 'true');
            $('#isrc_submit_color').wpColorPicker({
                    mode: 'Hex',
                    defaultColor: '#ea2330',
                    change:
                        function (event, ui) {
                            let color = $(this).iris('color', true).toCSS('Hex');
                            document.getElementsByTagName('iframe')[0].iFrameResizer.sendMessage({
                                func: 'btn_bg_color',
                                color: color
                            });
                        },
                    clear: function () {
                        document.getElementsByTagName('iframe')[0].iFrameResizer.sendMessage({
                            func: 'btn_bg_color',
                            color: 'transparent'
                        });

                    }
                }
            );

            $('#subm_icon_color').data('alpha', 'true');
            $('#subm_icon_color').wpColorPicker({
                    mode: 'Hex',
                    defaultColor: '#ffffff',
                    change:
                        function (event, ui) {
                            let color = $(this).iris('color', true).toCSS('Hex');
                            document.getElementsByTagName('iframe')[0].iFrameResizer.sendMessage({
                                func: 'btn_icon_bg_color',
                                color: color
                            });
                        },
                    clear: function () {
                        document.getElementsByTagName('iframe')[0].iFrameResizer.sendMessage({
                            func: 'btn_icon_bg_color',
                            color: 'transparent'
                        });

                    }
                }
            );

            // css builder
            $('.isrc-colors').data('alpha', 'true');
            $('.isrc-colors').wpColorPicker({
                    mode: 'Hex',
                    defaultColor: '',
                    change:
                        function (event, ui) {
                            let color = $(this).iris('color', true).toCSS('Hex');
                            document.getElementsByTagName('iframe')[0].iFrameResizer.sendMessage({
                                func: 'btn_icon_bg_color',
                                color: color
                            });
                        },
                    clear: function () {
                        document.getElementsByTagName('iframe')[0].iFrameResizer.sendMessage({
                            func: 'btn_icon_bg_color',
                            color: 'transparent'
                        });

                    }
                }
            );


            $(document.body).on('click', '.sc-tabs .isrc-tab-arrow', function () {
                let _this = $(this).closest('li'),
                    animHeight = 182;
                status = (_this.hasClass('_open')) ? 'open' : 'close';

                if (_this.data('type') == 'post_type') {
                    animHeight = 295;
                }
                if (status === 'close') {
                    _this.animate({height: animHeight}, 200).removeClass('_closed').addClass('_open');
                } else {
                    _this.animate({height: 32}, 200).removeClass('_open').addClass('_closed');
                }
            });

            $(document.body).on('click', '.isrc-css-builder-wrap .isrc-tab-arrow', function () {
                let _this = $(this).closest('.isrc-css-builder-wrap'),
                    status = (_this.hasClass('_open')) ? 'open' : 'close';
                if (status === 'close') {
                    _this.animate({maxHeight: 6000}, 500, 'linear', function () {
                        _this.css({'overflow': 'visible'});
                    }).removeClass('_closed').addClass('_open');
                } else {
                    _this.css({'overflow': 'hidden'});
                    _this.animate({maxHeight: 46}, 500, 'linear', function () {

                    }).removeClass('_open').addClass('_closed');
                }
            });

            $('#search_input_style').change(function () {
                let value = $(this).val();
                $('#txt_label_inp_style').closest('.isrc-settings').addClass('isrc-hide-on-hidden');
                $('#inp_opening_Style').closest('.isrc-settings').addClass('isrc-hide-on-hidden');
                $('#inp_txt_ic_clr').closest('.isrc-settings').addClass('isrc-hide-on-hidden');
                $('#overl_bg').closest('.isrc-settings').addClass('isrc-hide-on-hidden');
                $('#overl_ic_bg').closest('.isrc-settings').addClass('isrc-hide-on-hidden');

                if (value === 'normal') {
                    $('#txt_label_inp_style').closest('.isrc-settings').addClass('isrc-hide-on-hidden');
                    $('#inp_opening_Style').closest('.isrc-settings').addClass('isrc-hide-on-hidden');
                    $('#inp_txt_ic_clr').closest('.isrc-settings').addClass('isrc-hide-on-hidden');
                    $('#overl_bg').closest('.isrc-settings').addClass('isrc-hide-on-hidden');
                    $('#overl_ic_bg').closest('.isrc-settings').addClass('isrc-hide-on-hidden');
                }
                if (value === 'text' || value === 'text_icon') {
                    $('#txt_label_inp_style').closest('.isrc-settings').removeClass('isrc-hide-on-hidden');
                    $('#inp_opening_Style').closest('.isrc-settings').removeClass('isrc-hide-on-hidden');
                    $('#inp_txt_ic_clr').closest('.isrc-settings').removeClass('isrc-hide-on-hidden');
                    $('#overl_bg').closest('.isrc-settings').removeClass('isrc-hide-on-hidden');
                    $('#overl_ic_bg').closest('.isrc-settings').removeClass('isrc-hide-on-hidden');
                }
                if (value === 'icon') {
                    $('#inp_opening_Style').closest('.isrc-settings').removeClass('isrc-hide-on-hidden');
                    $('#inp_txt_ic_clr').closest('.isrc-settings').removeClass('isrc-hide-on-hidden');
                }
            });
            $('#search_input_style').trigger('change');

            $('.sc-builder-form').submit(function () {
                let title = $('#isrc_sc_title').val();
                if (!title) {
                    alertify.error(that.options.enter_title);
                    $('#isrc_sc_title').focus();
                    return false;
                }
                return true;
            });
        },
        previewLoaded: function () {
            $('.isrc-preview-preloader').css('opacity', 0).hide();
        },
        previewLoading: function () {
            $('.isrc-preview-preloader').css('opacity', 1).show();
        },
        reloadPreview: function () {
            let $iframe = $('#isrc_frame'),
                that = this,
                preview_src = that.options.preview_src,
                formdata = $('#post').serialize({checkboxesAsBools: true});


            let ajaxPost = {
                'action': 'isrc_set_preview_data',
                'nonce': isrc_nonce,
                'preview_data': formdata
            };
            if (that.previewRequest) {
                that.previewRequest.abort();
            }

            that.previewRequest = $.ajax({
                url: ajaxurl,
                type: 'post',
                dataType: 'json',
                data: ajaxPost,
                beforeSend: function () {
                    $(window).trigger('previewLoading');
                },
                success: function (data) {
                    that.previewRequest = null;
                    if (data.status === 'success') {
                        $iframe.attr('src', preview_src);
                    } else {
                        alert('Preview error');
                    }
                },
                error: function (e) {
                    that.previewRequest = null;
                }
            });

        },
        apply_analyze_tab_functions: function () {

            loading = $('#loadingDiv').hide();
            $(document).ajaxStop(function () {
                loading.hide();
            });

            alertify.set('notifier', 'position', 'top-center');
            alertify.defaults.transition = "fade";

            $('#isrc_log_bad_words').isrc_tagBox({
                tag_key: 'isrc_log_bad_words',
                ajax_save: true,
                security: isrc_nonce
            });

            $('.select2_add_to_post_modal').select2({
                theme: 'select2isrc',
                minimumInputLength: 1,
                placeholder: "Start typing...",
                ajax: {
                    url: ajaxurl,
                    dataType: 'json',
                    data: function (params) {
                        return {
                            search: params.term,
                            action: 'isrc_select2',
                            type: 'posts_for_analyze_screen',
                            _isrc_table_nonce: $('#_isrc_table_nonce').val(),
                        };

                    }
                }
            });
        },
        apply_settings_tab_functions: function () {
            let that = this;
            that.docu();
            that.stickys();
            that.tabshandler();
            that.enableImageUpload();

        },
        apply_advanced_settings_tab_functions: function () {
            let that = this,
                options = that.options;

            that.stickys();
            that.docu();

            $(document).on('click', '.db_actions', function () {
                event.preventDefault();
                let id = $(this).attr('id'),
                    value = $(this).val(),
                    params = {};

                params.action = 'isrc_menu_db_actions';
                params.action_id = id;
                params.security = options.advancedTabNonce;

                let confirm_value = options.confirm_value;
                if (id === 'delete_all_isearch') {
                    confirm_value = options.confirm_value2;
                }

                alertify.set('notifier', 'position', 'top-center');
                alertify.defaults.transition = "fade";
                alertify.confirm(value, confirm_value,
                    function () {
                        that.make_database_action(params);
                    },
                    function () {
                        //alertify.error('Cancel');
                    });
            });

            jQuery('#isrc_bad_words').isrc_tagBox({});
            jQuery('#isrc_hide_words').isrc_tagBox({});
            that.build_meta_fields('meta-keys', 'isrc_get_meta_keys', 'meta-keys-pre', 'isrc_opt_adv[meta_inc][]');
            that.build_meta_fields('taxo-keys', 'isrc_get_taxo_keys', 'taxo-keys-pre', 'isrc_opt_adv[taxonomy_includes][]');

            $('#isrc_add_new_excl').on('click', function (e) {
                e.preventDefault();
                that.appendExcludeTaxoTemplate();
            });

            $(document).on('click', '.isrc-excl-tax-remove-wrap', function () {
                let todelete = $(this).closest('.isrc-excl-outer-wrap');
                todelete.slideUp(150, function () {
                    todelete.remove();
                });
            });

            $(document).on('change', '.excl_taxo_selector', function () {
                let value = $(this).val(),
                    $tagsSelector = $(this).closest('.closest-wrapper').find('.select2_exclude_taxonomies');
                $tagsSelector.val(null).trigger("change");
                if (value === 'off') {
                    $tagsSelector.attr('disabled', 'disabled');
                } else {
                    $tagsSelector.removeAttr('disabled');
                }
            });

            if ($('.select2_exclude_taxonomies').length < 1) {
                that.appendExcludeTaxoTemplate();
            } else {
                that.activateS2onExcludeTaxoTemplate();
            }


        },
        appendExcludeTaxoTemplate: function () {
            return false;
        },
        activateS2onExcludeTaxoTemplate: function () {
            let exluded_taxonomy = $('.select2_exclude_taxonomies').not('.s2active');
            exluded_taxonomy.select2({
                minimumInputLength: 1,
                theme: 'select2isrc',
                placeholder: "Start Typing...",
                ajax: {
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    data: function (params) {
                        let $currentSelect2 = $(this),
                            selectedTaxonomy = $(this).closest('.closest-wrapper').find('.excl_taxo_selector').val(),
                            exclude = $currentSelect2.val();
                        if (selectedTaxonomy === 'off') {
                            alertify.error(that.options.pleaseSelectTaxonomyFirst);
                            return false;
                        }
                        return {
                            search: params.term,
                            action: 'isrc_exlude_taxonomies',
                            taxonomy: selectedTaxonomy,
                            exclude: exclude,
                            nonce: isrc_def_nonce,
                        };

                    }
                }
            });

            exluded_taxonomy.addClass('.s2active');
        },
        make_database_action: function (params) {

            jQuery.ajax({
                url: ajaxurl,
                type: 'post',
                dataType: 'json',
                data: params,
                success: function (data) {
                    if (data.status === 'error') {
                        if (typeof data.msg !== undefined) {
                            alertify.error(data.msg);
                            return;
                        }
                        alertify.error('ERROR');
                        return;
                    }
                    alertify.success('OK');
                }
            });

        },
        create_the_selects: function (id, data, inp_name) {
            let sel = jQuery('<select></select>').attr({
                'multiple': 'multiple',
                'name': inp_name,
                'id': 'inc_' + id
            }).appendTo('#' + id);
            jQuery(data).each(function () {
                let selected = false;
                if (this.selected) {
                    selected = true;
                }
                let option = jQuery('<option></option>');
                option.attr({'value': this.name}).text(this.label).prop('selected', selected);
                option.appendTo(sel);
            });
            jQuery('#' + id + ' select').multiSelect({
                selectableHeader: "<div class='custom-header'>" + m_keys_localize.select_meta_head + "</div>",
                selectionHeader: "<div class='custom-header'>" + m_keys_localize.select_meta_foot + "</div>",
            });

        },
        build_meta_fields: function (id, action, preloaderClass, inp_name) {
            let that = this,
                options = that.options;

            let data_p = {
                action: action,
                security: options.advancedTabNonce
            };

            jQuery.ajax({
                url: ajaxurl,
                type: 'post',
                dataType: 'json',
                success: function (data) {
                    jQuery('.' + preloaderClass).hide();
                    that.create_the_selects(id, data, inp_name);
                    if (id === 'meta-keys') {
                        jQuery('#side-update').removeClass('hide');
                    }
                },
                data: data_p
            });
        },
        apply_global_functions: function () {
            let that = this;

            /* admin custom notice click function */
            $(document).on('click', '.isearch-remove-notice-icon', function () {
                that.remove_row($(this));
            });

            that.hideons();
            that.tooltip();
            that.apply_video_tutorial();

        },
        exclude_taxo_select2: function () {
            let excl = [];
            jQuery('.s2_exclude_1').each(function () {
                excl.push(jQuery(this).val());
            });
            return excl;
        },
        tabshandler: function () {
            let that = this,
                options = that.options,
                tab_label = that.tab_label,
                tab_taxonomy = that.tab_taxonomy,
                current_remodal = that.current_remodal;

            $("#sortable_tabs").sortable({
                placeholder: "ui-state-highlight"
            });
            $("#sortable").disableSelection();

            /* add new tab functions */
            $('#isrc_add_new_tab').on('click', function (event) {
                event.preventDefault();

                current_remodal = jQuery('[data-remodal-id=modal_add_new_tab]').remodal({
                    hashTracking: false
                });
                current_remodal.open();

            });

            $(document.body).on('click', '.remove_taxonomy', function () {
                let todelete = $(this).parent();
                todelete.slideUp(150, function () {
                    todelete.remove();
                });
            });

            $(document.body).on('opening', '.modal_add_new_tab', function () {
                $('#isrc_taxonomies_select2').val('').trigger('change');
            });

            $(document.body).on('click', ".remodal[data-remodal-id='modal_add_new_tab'] .remodal-confirm", function () {
                let selected_taxonomy = $('#isrc_taxonomies_select2').val();
                if (selected_taxonomy) {
                    /* append to tabs section */
                    let where_to_append = $('#sortable_tabs'),
                        html = '';
                    html += "<li class='ui-state-default ui-sortable-handle'>";
                    html += "<span class='ui-icon ui-icon-arrowthick-2-n-s'></span>";
                    html += "<span class='sort-name'>" + options.label_taxonomy + " : <b>" + tab_label + "</b></span>";
                    html += "<span class='sort-rename'>" + options.label_label + " : </span>";
                    html += "<input name='isrc_opt[front][taborder][" + tab_taxonomy + "][label]' type='text' value='" + tab_label + "'>";
                    html += "<input name='isrc_opt[front][taborder][" + tab_taxonomy + "][type]' type='hidden' value='taxonomy'>";
                    html += "<input class='s2_exclude_1' name='isrc_opt[front][taborder][" + tab_taxonomy + "][name]' type='hidden' value='" + tab_taxonomy + "'>";
                    html += "<span class='remove_taxonomy'>" + options.label_remove + "</span>";
                    html += "</li>";
                    where_to_append.append(html);
                    current_remodal.close();
                }
            });
            let select2_selector = jQuery('.select2_add_to_post_modal');
            select2_selector.select2({
                theme: 'select2isrc',
                placeholder: "Start Typing...",
                ajax: {
                    url: ajaxurl,
                    type: "POST",
                    dataType: 'json',
                    data: function (params) {
                        return {
                            search: params.term,
                            action: 'isrc_select2_taxonomies',
                            exclude: that.exclude_taxo_select2(),
                            type: 'posts_for_analyze_screen',
                            _isrc_table_nonce: $('#_wpnonce').val(),
                        };

                    }
                }
            });

            select2_selector.on("select2:selecting", function (e) {
                let selection = e.params.args.data;
                tab_label = selection.label;
                tab_taxonomy = selection.id;
            });

        },
        tooltip: function () {

            $('.isrc_tooltip').tooltipster({
                animation: 'fade',
                delay: 200,
                maxWidth: 500,
                theme: 'tooltipster-light',
                side: 'right',
                trigger: 'click',
                contentAsHTML: true,
                content: 'Loading...',
                interactive: true,
                functionBefore: function (instance, helper) {
                    let $origin = $(helper.origin),
                        id = $(helper.origin).attr('id');
                    // we set a variable so the data is only loaded once via Ajax, not every time the tooltip opens
                    if ($origin.data('loaded') !== true) {
                        $.get(ajaxurl + '?action=isrc_help&content_id=' + id, function (data) {
                            // call the 'content' method to update the content of our tooltip with the returned data.
                            // note: this content update will trigger an update animation (see the updateAnimation option)
                            let html = '';
                            if (typeof data.html !== 'undefined') {
                                html += data.html;
                            }
                            if (typeof data.img !== 'undefined') {
                                html += '<div class="help_img" style="min-height:' + data.img_h + 'px;text-align:center"><img src="' + data.img + '">';
                            }
                            if (typeof data.video !== 'undefined') {
                                html += '<video width="' + data.video_w + '" height="' + data.video_h + '" controls>';
                                html += '<source src="' + data.video + '" type="video/mp4">';
                                html += '</video>';
                            }
                            instance.content(html);
                            // to remember that the data has been loaded
                            $origin.data('loaded', true);
                        });
                    }
                }
            })

        },
        hideons: function () {

            $('div[data-showonoparent_select]').each(
                function () {
                    let that = this,
                        $this = $(this),
                        parent_id = $this.attr('data-showonoparent_select'),
                        $parent = $('#' + parent_id),
                        parent_selection = $parent.val(),
                        show_on_selected = $this.attr('data-showonselection');

                    if (parent_selection !== show_on_selected) {
                        $this.addClass('isrc-hide-on-hidden');
                    }

                    $parent.change(function () {
                        let selected_value = $(this).val();
                        $('div[data-showonoparent_select=' + parent_id + ']').addClass('isrc-hide-on-hidden');
                        $('div[data-showonselection=' + selected_value + ']').removeClass('isrc-hide-on-hidden');
                    });


                });

            $(".isrc-hide-if-disabled").each(
                function () {
                    let _this = this;
                    let toHide = $(_this).attr('data-hide');

                    if (_this.checked) {
                        $('.' + toHide).removeClass('isrc-hide-on-hidden');
                    } else {
                        $('.' + toHide).addClass('isrc-hide-on-hidden');
                    }

                    $(this).change(function () {
                        let _this = this;
                        if (_this.checked) {
                            $('.' + toHide).removeClass('isrc-hide-on-hidden');
                        } else {
                            $('.' + toHide).addClass('isrc-hide-on-hidden');
                        }
                    });
                }
            );

            $(".isrc-show-if-disabled").each(
                function () {
                    let _this = this;
                    let toHide = $(_this).attr('data-hide');

                    if (_this.checked) {
                        $('.' + toHide).addClass('isrc-hide-on-hidden');
                    } else {
                        $('.' + toHide).removeClass('isrc-hide-on-hidden');
                    }

                    $(this).change(function () {
                        let _this = this;
                        if (_this.checked) {
                            $('.' + toHide).addClass('isrc-hide-on-hidden');
                        } else {
                            $('.' + toHide).removeClass('isrc-hide-on-hidden');
                        }
                    });
                }
            );
        },
        docu: function () {

            $(document).on('click', '.isrc_contents ul li a', function (event) {
                event.preventDefault();
                let content_id = $(this).attr("href"),
                    $target = $(content_id);

                if ($(this).attr('data-group')) {
                    let data_group = $(this).attr('data-group');
                    $target = $("div[data-group='" + data_group + "']");
                }

                $.scrollTo(content_id, 400, {
                    offset: {top: -100},
                    onAfter: function () {
                        $target.removeClass('scrolled');
                        $target.delay(100).queue(function () {
                            $target.addClass('scrolled').dequeue();
                        });
                    }
                });
            });

        },
        stickys: function () {

            if (jQuery().sticky) {
                $("#contents-holder").sticky({
                    topSpacing: 97
                });
                jQuery("#isrc-tabs").sticky({
                    topSpacing: 30,
                    zIndex: 9
                });
            }

        },
        enableImageUpload: function () {
            // Set all variables to be used in scope
            let frame,
                metaBox = jQuery('.metabox-holder'),
                addImgLink = metaBox.find('.upload-isrc-custom-img'),
                delImgLink = metaBox.find('.delete-custom-img'),
                imgContainer = metaBox.find('.isrc-meta-img-container'),
                imgIdInput = metaBox.find('.isrc_img_id');

            // ADD IMAGE LINK
            addImgLink.on('click', function (event) {

                event.preventDefault();

                // If the media frame already exists, reopen it.
                if (frame) {
                    frame.open();
                    return;
                }

                // Create a new media frame
                frame = wp.media({
                    title: 'Select or Upload Media',
                    button: {
                        text: 'Use this media'
                    },
                    multiple: false  // Set to true to allow multiple files to be selected
                });


                // When an image is selected in the media frame...
                frame.on('select', function () {

                    // Get media attachment details from the frame state
                    let attachment = frame.state().get('selection').first().toJSON();

                    // Send the attachment URL to our custom image input field.
                    imgContainer.append('<img src="' + attachment.url + '" alt="" style="max-width:100%;"/>');

                    // Send the attachment id to our hidden input
                    imgIdInput.val(attachment.id);

                    // Hide the add image link
                    addImgLink.addClass('hidden');

                    // Show the remove image link
                    delImgLink.removeClass('hidden');
                });

                // Finally, open the modal on click
                frame.open();
            });


            // DELETE IMAGE LINK
            delImgLink.on('click', function (event) {

                event.preventDefault();

                // Clear out the preview image
                imgContainer.html('');

                // Un-hide the add image link
                addImgLink.removeClass('hidden');

                // Hide the delete image link
                delImgLink.addClass('hidden');

                // Delete the image id from the hidden input
                imgIdInput.val('');

            });

        },
        remove_row: function (element) {
            let that = this;

            element.closest('.rmthis').fadeTo(300, 0.01, function () {
                $(this).slideUp(150, function () {
                    $(this).remove();
                });
            });
        }
    };
    // Create chainable jQuery plugin:
    $.fn.isearch_init = function (options) {
        return new Isearch_init(this, options);
    };

}));
let isearch;
jQuery(function () {
    isearch = jQuery().isearch_init(isrc_js_params);
});
