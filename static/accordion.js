/* Wrap sections on voluntary-work page in <detail> elements */

window.addEventListener('load', function() {
    "use strict";

    // https://mathiasbynens.be/notes/html5-details-jquery#comment-35
    var isDetailsSupported = function() {
        var el = document.createElement('details');
        var root = document.body;
        el.innerHTML = '<summary>a</summary>b';
        el.style.display = 'block';
        root.appendChild(el);

        var height1 = el.offsetHeight;
        el.open = true;
        var height2 = el.offsetHeight;

        root.removeChild(el);
        return height1 != height2;
    };

    // wrap sections (starting with <h3>) in <details> elements.

    var prependChild = function(par, child) {
        if (par.childNodes.length === 0) {
            par.appendChild(child);
        } else {
            par.insertBefore(child, par.childNodes[0]);
        }
    };

    var wrap = function(node, wrapName) {
        var wrapper = document.createElement(wrapName);
        node.parentNode.insertBefore(wrapper, node);
        wrapper.appendChild(node);
        return wrapper;
    };

    var body = document.querySelector('.accordion');

    if (body && isDetailsSupported()) {
        var sections = [document.createElement('details')];

        // iterate backwards in order to not change the node indices
        var i = body.childNodes.length;
        while (i--) {
            var node = body.childNodes[i];
            if (node.nodeType === Node.ELEMENT_NODE && node.nodeName === 'H3') {
                prependChild(sections[0], wrap(node, 'summary'));
                sections.unshift(document.createElement('details'));
            } else {
                prependChild(sections[0], node);
            }
        }

        var j = sections.length;
        while (--j) {
            prependChild(body, sections[j]);
        }

        // the first section does not have a heading, so it is unwrapped
        var k = sections[0].childNodes.length;
        while (k--) {
            prependChild(body, sections[0].childNodes[k]);
        }
    }
});
