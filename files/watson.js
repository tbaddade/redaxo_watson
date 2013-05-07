jQuery(function($){

    var $watson         = $('#watson');
    var $watson_overlay = $('#watson-overlay');

    $(document).ready( function() {

        var watson_id   = getUrlParameter('watson_id');
        var watson_text = getUrlParameter('watson_text');
        if (watson_id && watson_text) {
            $('#' + watson_id).val(watson_text).focus();
        }

        $watson_overlay.click(function(){
            hideWatson();
        });
    });

    $(document).keydown(function(e) {
        if ((e.keyCode == 32 && e.ctrlKey) || (e.keyCode == 32 && e.ctrlKey && e.altKey) || (e.keyCode == 32 && e.ctrlKey && e.metaKey)) {
            checkWatson();
        }
    });

    $(document).keyup(function(e) {
        // Escape
        if (e.keyCode == 27) {
            hideQuicklook();
            hideWatson();
        }
        if (e.keyCode == 37 || e.keyCode == 38 || e.keyCode == 40) {
            hideQuicklook();
        }
    });


    function checkWatson() {
        if ($watson.hasClass('watson-active')) {
            hideWatson($watson);
        } else {
            showWatson($watson);
        }
    }

    function showWatson() {

        $('.typeahead').typeahead({
            name: 'watson-result',
            remote: rex.backendUrl + '?watson=%QUERY',
            //prefetch: rex.backendUrl + '?watson.json',
            limit: 10,
            template: [
                '<div class="watson-result">',
                '<span class="watson-value{{class}}" style="{{style}}">{{value}}<em>{{description}}</em></span>',
                '</div>'
             ].join(''),

            engine: Hogan
        });

        $('.typeahead').on('typeahead:autocompleted', function(evt, item) {
            if (item.quick_look_url !== undefined) {
                showQuicklook(item.quick_look_url);
            }
        });
        $('.typeahead').on('typeahead:selected', function(evt, item) {
            if (item.url !== undefined) {
                if (item.url_open_window) {
                    window.open(item.url, '_newtab');
                } else {
                    window.location.href = item.url;
                }
            }
        });

        $watson_overlay.fadeIn('fast');
        $watson.fadeIn('fast').addClass('watson-active');
        $watson.find('input').focus();
    }

    function hideWatson() {
        $watson_overlay.fadeOut('fast');
        $watson.fadeOut('fast').removeClass('watson-active');
        $('.typeahead').typeahead('destroy');
    }

    function getUrlParameter(name) {
        return decodeURI(
            (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]
        );
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
});