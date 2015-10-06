jQuery(function($){


    /*
    |--------------------------------------------------------------------------
    | Allgemeines
    |--------------------------------------------------------------------------
    |
    |
    */
    
    $(document).ready( function() {

        var $watson_id   = getUrlParameter('watson_id');
        var $watson_text = getUrlParameter('watson_text');
        if ($watson_id && $watson_text) {
            $('#' + $watson_id).val($watson_text).focus();
        }
    });

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

        console.log('quicklook');
        if ($('#facebox_overlay').length > 0) {

            console.log('$.facebox.close');
            $.facebox.close();

        } else if ($link !== undefined) {
            
            console.log('$.facebox.open');
            $.facebox({ iframe: $link });

        }
    }






    /*
    |--------------------------------------------------------------------------
    | Searcher
    |--------------------------------------------------------------------------
    |
    |
    */
    var $watson_searcher      = $('#watson-searcher');
    var $watson_searcher_help = $('#watson-searcher-help');
    var $watson_overlay       = $('#watson-overlay');

    $(document).ready( function() {

        $watson_overlay.click(function(){
            hideWatsonSearcher();
        });

        $('.watson-searcher-help-open').click(function(){
            showWatsonSearcherHelp();
        });
        $('.watson-searcher-help-close').click(function(){
            hideWatsonSearcherHelp();
        });
    });

    $(document).keydown(function(e) {
        if ((WatsonSearch.agent_hotkey == '16-32' && e.shiftKey && e.keyCode == 32) ||
            (WatsonSearch.agent_hotkey == '16-17-32' && e.shiftKey && e.ctrlKey && e.keyCode == 32) ||
            (WatsonSearch.agent_hotkey == '16-18-32' && e.shiftKey && e.altKey && e.keyCode == 32) ||
            (WatsonSearch.agent_hotkey == '17-32' && e.ctrlKey && e.keyCode == 32) ||
            (WatsonSearch.agent_hotkey == '17-18-32' && e.ctrlKey && e.altKey && e.keyCode == 32) ||
            (WatsonSearch.agent_hotkey == '17-91-32' && e.ctrlKey && e.metaKey && e.keyCode == 32) ||
            (WatsonSearch.agent_hotkey == '18-32' && e.altKey && e.keyCode == 32)) {

            checkWatsonSearcher();
        }
    });

    $(document).keyup(function(e) {
        // Escape
        if (e.keyCode == 27) {
            //hideQuicklook();
            hideWatsonSearcher();
        }
        /*
        if (e.keyCode == 37 || e.keyCode == 38 || e.keyCode == 40) {
            hideQuicklook();
        }
        */
    });


    function checkWatsonSearcher() {
        if ($watson_searcher.hasClass('watson-active')) {
            hideWatsonSearcher();
        } else {
            showWatsonSearcher();
        }
    }

    function showWatsonSearcher() {

        var $watson_results = new Bloodhound({
            filter: function(data) {
              // assume data is an array of strings e.g. ['one', 'two', 'three']
              return $.map(data, function(str) { return { value: str }; });
            },
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.value);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,

            remote: {
                url: WatsonSearch.backendUrl,
                wildcard: WatsonSearch.wildcard,
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

        // instantiate the typeahead UI
        var $watsonTypeahead = $('#watson-searcher .typeahead');

        var $curTypeaheadItem;

        $watsonTypeahead.typeahead({
                highlight: true, 
                hint: true, 
                minLength: 1
            }, {
                name: 'watson', 
                source: $watson_results,
                limit: WatsonSearch.resultLimit,
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
        $watsonTypeahead.on('typeahead:autocompleted', onAutocompleted);
        $watsonTypeahead.on('typeahead:select', onSelect);
        
        $watsonTypeahead.on('typeahead:cursorchange', function($e, $item){
            $curTypeaheadItem = $item;
            $curUserInput = $(this).typeahead('val');

            if ($curTypeaheadItem && $curTypeaheadItem.quick_look_url !== undefined) {
                $('.watson-quick-look-frame').html('<iframe src="' + $curTypeaheadItem.quick_look_url + '"></iframe>').show();
            }
        });
        
        $watsonTypeahead.on('typeahead:closed', function(){
            $curTypeaheadItem = null;
        });


        $watsonTypeahead.keydown(function(e) {

            if ($curTypeaheadItem && $curTypeaheadItem.quick_look_url !== undefined && 
                (WatsonSearch.quicklook_hotkey == '16' && e.shiftKey) ||
                (WatsonSearch.quicklook_hotkey == '17' && e.ctrlKey) ||
                (WatsonSearch.quicklook_hotkey == '18' && e.altKey) ||
                (WatsonSearch.quicklook_hotkey == '91' && e.metaKey)
                ) {
                quicklook($curTypeaheadItem.quick_look_url);
            }
            
        });

        $watson_overlay.fadeIn('fast');
        $watson_searcher.fadeIn('fast').addClass('watson-active');
        $watson_searcher.find('input').focus();
    }

    
 
    function onOpen($e) {
        //console.log('opened');
        destroyWatsonQuickLookFrame();
        $('#watson-searcher .twitter-typeahead').append('<div class="watson-quick-look-frame"></div>');
    }

    function onAutocompleted($e, item) {
        console.log('autocompleted');
        console.log(item);

        if (item.html_fields !== undefined) {
            var html_fields = $.parseJSON(item.html_fields);

            for (var i = 0; i < html_fields.length; i++) {
                $(html_fields[i].label).val(html_fields[i].value);
            }
        }
    }
     
    function onSelect($e, item) {
        console.log('selected');
        console.log(item);

        if (item.url !== undefined) {
            if (item.url_open_window) {
                window.open(item.url, '_newtab');
            } else {
                window.location.href = item.url;
            }
        }
    }

    function hideWatsonSearcher() {
        hideWatsonSearcherHelp();
        $watson_overlay.fadeOut('fast');
        $watson_searcher.fadeOut('fast').removeClass('watson-active');
        $('#watson-searcher .typeahead').typeahead('destroy');
    }

    function showWatsonSearcherHelp() {
        $watson_searcher_help.fadeIn('fast').addClass('watson-active');
    }

    function hideWatsonSearcherHelp() {
        $watson_searcher_help.fadeOut('fast').removeClass('watson-active');
    }

    function destroyWatsonQuickLookFrame() {
        $('#watson-searcher .twitter-typeahead').find('.watson-quick-look-frame').remove();
    }
});