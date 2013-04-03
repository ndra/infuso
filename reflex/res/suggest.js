$(function() {

        $.suggest = function(input, options) {

            var $input = $(input).attr("autocomplete", "off");
            var $results = $(document.createElement("ul"));

            var timeout = false;        // hold timeout ID for suggestion results to appear
            var prevLength = 0;            // last recorded length of $input.val()
            var cache = [];                // cache MRU list

            $results.addClass(options.resultsClass).appendTo('body');


            resetPosition();
            $(window)
                .load(resetPosition)        // just in case user is changing size of page while loading
                .resize(resetPosition);

            $input.blur(function() {
                setTimeout(function() { $results.hide() }, 200);
            });

            // I really hate browser detection, but I don't see any other way
            if ($.browser.mozilla)
                $input.keypress(processKey);    // onkeypress repeats arrow keys in Mozilla/Opera
            else
                $input.keydown(processKey);        // onkeydown repeats arrow keys in IE/Safari




            function resetPosition() {
                // requires jquery.dimension plugin
                var offset = $input.offset();
                $results.css({
                    top: (offset.top + input.offsetHeight) + 'px',
                    left: offset.left + 'px'
                });
            }


            function processKey(e) {

                // handling up/down/escape requires results to be visible
                // handling enter/tab requires that AND a result to be selected
                if ((/27$|38$|40$/.test(e.keyCode) && $results.is(':visible')) ||
                    (/^13$|^9$/.test(e.keyCode) && getCurrentResult())) {

                    if (e.preventDefault)
                        e.preventDefault();
                    if (e.stopPropagation)
                        e.stopPropagation();

                    e.cancelBubble = true;
                    e.returnValue = false;

                    switch(e.keyCode) {

                        case 38: // up
                            prevResult();
                            break;

                        case 40: // down
                            nextResult();
                            break;

                        case 9:  // tab
                        case 13: // return
                            selectCurrentResult();
                            break;

                        case 27: //    escape
                            $results.hide();
                            break;

                    }

                } else if ($input.val().length != prevLength) {

                    if (timeout)
                        clearTimeout(timeout);
                    timeout = setTimeout(suggest, options.delay);
                    prevLength = $input.val().length;

                }


            }


            function suggest() {

                var q = $.trim($input.val());

                if (q.length >= options.minchars) {

                    cached = checkCache(q);

                    if (cached) {

                        displayItems(cached['items']);

                    } else {

                        mod.cmd({cmd:"reflex:search:search",q:q}, function(items) {
                            $results.hide();
                            displayItems(items);
                            addToCache(q, items);

                        });

                    }

                } else {

                    $results.hide();

                }

            }


            function checkCache(q) {

                for (var i = 0; i < cache.length; i++)
                    if (cache[i]['q'] == q) {
                        cache.unshift(cache.splice(i, 1)[0]);
                        return cache[0];
                    }

                return false;

            }

            function addToCache(q, items) {
                cache.push({
                    q: q,
                    items: items
                    });
            }

            function displayItems(items) {

                if (!items)
                    return;

                if (!items.length) {
                    $results.hide();
                    return;
                }

                $results.html("").show();

                for (var i = 0; i < items.length; i++)
                    $("<li/>").html(items[i].html).data("url",items[i].url).appendTo($results);

                $results
                    .children('li')
                    .mouseover(function() {
                        $results.children('li').removeClass(options.selectClass);
                        $(this).addClass(options.selectClass);
                    })
                    .click(function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        selectCurrentResult();
                    });

            }

            function getCurrentResult() {

                if (!$results.is(':visible'))
                    return false;

                var $currentResult = $results.children('li.' + options.selectClass);

                if (!$currentResult.length)
                    $currentResult = false;

                return $currentResult;

            }

            function selectCurrentResult() {
                window.location.href = getCurrentResult().data("url");
            }

            function nextResult() {

                $currentResult = getCurrentResult();

                if ($currentResult)
                    $currentResult
                        .removeClass(options.selectClass)
                        .next()
                            .addClass(options.selectClass);
                else
                    $results.children('li:first-child').addClass(options.selectClass);

            }

            function prevResult() {

                $currentResult = getCurrentResult();

                if ($currentResult)
                    $currentResult
                        .removeClass(options.selectClass)
                        .prev()
                            .addClass(options.selectClass);
                else
                    $results.children('li:last-child').addClass(options.selectClass);

            }

        }

        var suggest = function(source, options) {

            options = options || {};
            options.delay = options.delay || 100;
            options.resultsClass = options.resultsClass || 'ac_results';
            options.selectClass = options.selectClass || 'ac_over';
            options.matchClass = options.matchClass || 'ac_match';
            options.minchars = options.minchars || 2;
            options.onSelect = options.onSelect || false;

            $(source).each(function() {
                new $.suggest(this, options);
            });

            return this;

        };

        suggest("#reflex-search",{
            onSelect: function(a) {
                window.location.href = a;
            },
            source:"/reflex/meta/search/"
        });

});
