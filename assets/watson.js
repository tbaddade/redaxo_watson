jQuery(function($){


    /*
    |--------------------------------------------------------------------------
    | Allgemeines
    |--------------------------------------------------------------------------
    |
    |
    */

    var $watson_id   = getUrlParameter('watson_id');
    var $watson_text = getUrlParameter('watson_text');
    if ($watson_id && $watson_text) {
        $('#' + $watson_id).val($watson_text).focus();
    }

    function getUrlParameter(name) {
        return decodeURI(
            (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]
        );
    }

    function getQueryString() {
        var s = window.location.href;
        return s.substring(s.lastIndexOf("?") + 1).replace('&', '+++');
    }



    // Facebox -----------------------------------------------------------------
    $.facebox.settings.closeImage   = '';
    $.facebox.settings.loadingImage = '';
    $.facebox.settings.opacity      = 0.5;

    var iframe_min_width  = 800;
    var iframe_min_height = 600;

    var width  = $(window).width()  - 200;
    var height = $(window).height() - 200;

    if (width < iframe_min_width) {
        width = iframe_min_width;
    }
    if (height < iframe_min_height) {
        height = iframe_min_height;
    }
    $.facebox.settings.iframe_width  = width;
    $.facebox.settings.iframe_height = height;


    function showQuicklook(link) {
        $.facebox({ iframe: link });
    }

    function hideQuicklook() {
        if ($('#facebox_overlay').length > 0)
            $.facebox.close();
    }

    function quicklook($link) {

        // console.log('quicklook');
        if ($('#facebox_overlay').length > 0) {

            // console.log('$.facebox.close');
            $.facebox.close();

        } else if ($link !== undefined) {

            // console.log('$.facebox.open');
            $.facebox({ iframe: $link });

        }
    }






    /*
    |--------------------------------------------------------------------------
    | Watson
    |--------------------------------------------------------------------------
    |
    |
    */

    // instantiate the typeahead UI
    var $watsonAgent     = $('#watson-agent');
    var $watsonTypeahead = $('#watson-agent .typeahead');
    var $watsonOverlay   = $('#watson-overlay');

    $watsonOverlay.click(function(){
        hideWatsonAgent();
    });
    
    // support buttons created dynamically
    $(document).on('click', '.watson-btn', function(){
        checkWatsonAgent();
    });

    $(document).keydown(function(e) {
        if (($watsonSettings.agentHotkey == '16-32' && e.shiftKey && e.keyCode == 32) ||
            ($watsonSettings.agentHotkey == '16-17-32' && e.shiftKey && e.ctrlKey && e.keyCode == 32) ||
            ($watsonSettings.agentHotkey == '16-18-32' && e.shiftKey && e.altKey && e.keyCode == 32) ||
            ($watsonSettings.agentHotkey == '17-32' && e.ctrlKey && e.keyCode == 32) ||
            ($watsonSettings.agentHotkey == '17-18-32' && e.ctrlKey && e.altKey && e.keyCode == 32) ||
            ($watsonSettings.agentHotkey == '17-91-32' && e.ctrlKey && e.metaKey && e.keyCode == 32) ||
            ($watsonSettings.agentHotkey == '18-32' && e.altKey && e.keyCode == 32)) {

            checkWatsonAgent();
        }
    });

    $(document).keyup(function(e) {
        // Escape
        if (e.keyCode == 27) {
            hideWatsonAgent();
        }
    });


    function checkWatsonAgent() {
        if ($watsonAgent.hasClass('watson-active')) {
            hideWatsonAgent();
        } else {
            showWatsonAgent();
        }
    }

    function showWatsonAgent() {

        var $watsonResults = new Bloodhound({
            filter: function(data) {
              // assume data is an array of strings e.g. ['one', 'two', 'three']
              return $.map(data, function(str) { return { value: str }; });
            },
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.value);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,

            remote: {
                url: $watsonSettings.backendRemoteUrl,
                wildcard: $watsonSettings.wildcard,
                cache: false
            }
        });


        var $template =
            Hogan.compile([
                '<div class="watson-result" data-legend="{{legend}}">',
                '<span class="watson-value{{class}}" style="{{style}}"><i class="watson-icon{{icon}}"></i> {{value_name}}<em class="watson-value-suffix">{{value_suffix}}</em><em class="watson-description">{{description}}</em></span>',
                //'<div class="watson-preview"><iframe src="{{quick_look_url}}"></iframe></div>',
                '</div>'
        ].join(''));


        var $curTypeaheadItem;

        $watsonTypeahead.typeahead({
                highlight: true,
                hint: true,
                minLength: 1
            }, {
                name: 'watson',
                source: $watsonResults,
                // limit: $watsonSettings.resultLimit,
                limit: 3000,
                displayKey: function (str) {
                    return str.displayKey;
                },
                templates: {
                    empty: [
                        '<div class="empty-message">',
                        'Please sign in.',
                        '</div>'
                    ].join('\n'),
                    suggestion: function (data) {
                        return $template.render(data);
                    }
                }
            });

        $watsonTypeahead.on('typeahead:open', onOpen);
        $watsonTypeahead.on('typeahead:autocomplete', onAutocomplete);
        $watsonTypeahead.on('typeahead:select', onSelect);

        $watsonTypeahead.on('typeahead:render', function($e, $item){
            hideWatsonQuickLookFrame();
        });

        $watsonTypeahead.on('typeahead:cursorchange', function($e, $item){
            $curTypeaheadItem = $item;
            $curUserInput = $(this).typeahead('val');

            if ($curTypeaheadItem && $curTypeaheadItem != null && typeof($curTypeaheadItem) !== 'undefined' && typeof($curTypeaheadItem.quick_look_url) !== 'undefined') {
                $('.watson-quick-look-frame').html('<iframe src="' + $curTypeaheadItem.quick_look_url + '"></iframe>').show();
            } else {
                hideWatsonQuickLookFrame();
            }
        });

        $watsonTypeahead.on('typeahead:close', function(){
            $curTypeaheadItem = null;
        });


        $watsonTypeahead.keydown(function(e) {
            if (($watsonSettings.quicklookHotkey == '16' && e.shiftKey) ||
                ($watsonSettings.quicklookHotkey == '17' && e.ctrlKey) ||
                ($watsonSettings.quicklookHotkey == '18' && e.altKey) ||
                ($watsonSettings.quicklookHotkey == '91' && e.metaKey)
                ) {
                if (typeof($curTypeaheadItem) !== 'undefined' && typeof($curTypeaheadItem.quick_look_url) !== 'undefined') {
                    quicklook($curTypeaheadItem.quick_look_url);
                }
            }

        });

        $watsonOverlay.fadeIn('fast');
        $watsonAgent.fadeIn('fast').addClass('watson-active');
        $watsonAgent.find('input').focus();
    }



    function onOpen($e) {
        //console.log('opened');
        $watsonAgent.find('.twitter-typeahead').append('<div class="watson-quick-look-frame"></div>').hide();
    }

    function onAutocomplete($e, item) {
        // console.log('autocomplete');
        // console.log(item);

        if (item.html_fields !== undefined) {
            var html_fields = $.parseJSON(item.html_fields);

            for (var i = 0; i < html_fields.length; i++) {
                $(html_fields[i].label).val(html_fields[i].value);
            }
        }
    }

    function onSelect($e, item) {
        //console.log('selected');
        //console.log(item);
        if (item.ajax !== undefined) {
            var $data  = $.parseJSON(item.ajax);
            //console.log($data);
            // console.log(JSON.stringify($data['params']));
            var $url = $watsonSettings.backendUrl;
            $.ajax({
                url: $url,
                type: 'POST',
                data: {
                    watsonCallClass : $data['class'],
                    watsonCallMethod: $data['method'],
                    watsonCallParams: JSON.stringify($data['params'])
                },
                error: function(jqXHR, textStatus, errorThrown) {
                        // console.log(JSON.stringify(jqXHR));
                        // console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                 }
            }).done(function(result) {
                // console.log(result);
                var $result = $.parseJSON(result);
                if ($result.url !== undefined) {
                    window.location.href = $result.url;
                }
            });
        }
        if (item.url !== undefined) {
            if (item.url_open_window) {
                window.open(item.url, '_newtab');
            } else {
                window.location.href = item.url;
            }
        }
    }

    function hideWatsonAgent() {
        destroyWatsonQuickLookFrame();
        $watsonOverlay.fadeOut('fast');
        $watsonAgent.fadeOut('fast').removeClass('watson-active');

        $watsonTypeahead.typeahead('destroy');
    }


    function hideWatsonQuickLookFrame() {
        $('.watson-quick-look-frame').hide();
    }

    function destroyWatsonQuickLookFrame() {
        $watsonAgent.find('.twitter-typeahead .watson-quick-look-frame').remove();
    }
});
