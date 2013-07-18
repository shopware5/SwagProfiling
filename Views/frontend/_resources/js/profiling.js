//https://github.com/einars/js-beautify
(function ($) {
    /**
     * Helper function to trace all sended ajax request on the current page.
     * This function appends for each ajax request a new table with the ajax
     * data into the ajax-wrapper element.
     */
    $(document).ajaxComplete(function (event, response, request) {
        var toolbar = $('.developer-bar'),
            details = $('.developer-bar-details'),
            ajaxTable = details.find('.ajax-wrapper'),
            toolbarContent = toolbar.find('.ajax.bar-element .element-content'),
            item = '',
            counter = toolbarContent.html();

        counter = counter * 1;
        counter++;
        toolbarContent.html(counter);

        var currentDate = new Date();
        item = item + '<tr><td colspan="2" class="sub-head">Request</td></tr>';
        item = item + '<tr><td>Time</td><td>' + currentDate.getHours() + ':' + currentDate.getMinutes() + ':' + currentDate.getSeconds() + '</td></tr>';
        item = item + getObjectAsTable(request);
        item = item + '<tr><td colspan="2" class="sub-head">Response</td></tr>';
        item = item + getObjectAsTable(response);
        //        item = item + '<tr style="background: #999"><td colspan="2" class="sub-head">Event</td></tr>';
        //        item = item + getObjectAsTable(event);

        ajaxTable.append('<table>' + '<thead><th>Key</th><th style="width: 2000px;">Value</th></thead>' + '<tbody>' + item + '</tbody>' + '</table><div class="spacer"></div>');

    });

    /**
     * Helper function to convert an object into an array
     * @param object
     * @returns {string}
     */
    var getObjectAsTable = function (object) {
        var keys = $.map(object, function (value, key) {
            return key;
        });
        var item = '';
        $.each(keys, function (index, key) {
            var value = object[key];
            if (isFunction(value)) {
                value = js_beautify(value.toString());
                value = '<pre>' + value + '</pre>'
            }
            item = item + '<tr><td>' + key + '</td><td>' + value + '</td></tr>';
        });
        return item;
    }


    function isFunction(functionToCheck) {
        var getType = {};
        return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
    }

})(jQuery);

/* Add class to the searchfield */ (function ($) {
    $(document).ready(function () {
        $('.developer-bar .bar-element').bind('click', function () {
            $('#header').toggle();
            $('#wrapper').toggle();
            $('#footer_wrapper').toggle();
            $('#compare_bigbox').toggle();
            $('html').css('background-image', 'none');
            $('.developer-bar-details').toggle();
        });
        $('.developer-bar-details .close-button').bind('click', function () {
            $('#header').toggle();
            $('#wrapper').toggle();
            $('#footer_wrapper').toggle();
            $('#compare_bigbox').toggle();
            $('html').css('background-image', 'none');
            $('.developer-bar-details').toggle();
            return false;
        });

    });

    /* Add class to the searchfield */ (function ($) {
        $(document).ready(function () {
            $('.developer-bar-details table .toggle').bind('click', function () {
                var $this = $(this),
                    parent = $this.parents('tr'),
                    details = parent.next();

                if (details.hasClass('collapsible')) {
                    if (details.hasClass('expanded')) {
                        details.hide();
                        details.removeClass('expanded')
                    } else {
                        details.show();
                        details.addClass('expanded');
                    }
                }

                details = details.next();
                if (details.hasClass('collapsible')) {
                    if (details.hasClass('expanded')) {
                        details.hide();
                        details.removeClass('expanded')
                    } else {
                        details.show();
                        details.addClass('expanded');
                    }
                }
            });

            $('.developer-bar .clear-cache').bind('click', function () {
                var $this = $(this);
                var url = $this.find('input[name=clear-cache-url]').val();
                document.body.style.cursor = 'wait';
                jQuery.ajax({
                    url: url,
                    type: 'POST',
                    complete: function () {
                        document.body.style.cursor = 'default';
                        alert('Cache cleared!');
                    }
                });
            });

            $('.developer-bar-details table .toggle-all').bind('click', function () {
                var $this = $(this),
                    table = $this.parents('table'),
                    details = table.find('.details.collapsible');

                details.each(function (index, detail) {
                    detail = $(detail);
                    if (detail.hasClass('expanded')) {
                        detail.hide();
                        detail.removeClass('expanded')
                    } else {
                        detail.show();
                        detail.addClass('expanded');
                    }
                });
            });

            $('.array-wrapper .btn.toggle').bind('click', function () {
                var $this = $(this),
                    item = $this.parents('.item.array'),
                    detail = item.find('.array-wrapper');

                var first = $(detail[0]);
                if (first.hasClass('collapsed')) {
                    $this.html('-');
                    first.removeClass('collapsed');
                    first.show();
                } else {
                    first.hide();
                    $this.html('+');
                    first.addClass('collapsed');
                }
            });

            $('.developer-bar-details .developer-content .navigation li a').bind('click', function (event) {
                var $this = $(this),
                    li = $this.parents('li'),
                    key = '.developer-bar-details .element-content' + $this.attr('href').replace('#', '.'),
                    newActive = $(key);

                event.preventDefault();

                //remove active class of all navigation items
                var listItems = $('.developer-bar-details .developer-content .navigation li');
                listItems.each(function (index, item) {
                    $(item).removeClass('active');
                });

                //get the current displayed content over the active class of the element-content items
                var currentContent = $('.developer-bar-details .element-content.active');
                currentContent.removeClass('active');

                //display the new active items
                $(li).addClass('active');

                currentContent.fadeOut('fast', function () {
                    newActive.fadeIn('fast', function() {

                        // Set PHP info frame to correct height
                        var frame = document.getElementById('phpFrame'),
                            frameBody = frame.contentWindow.document.body,
                            frameHeight = frameBody.offsetHeight;

                        frame.style.height = frameHeight + 'px';

                    });
                    newActive.addClass('active');
                });
            });
        });
    })(jQuery);
})(jQuery);