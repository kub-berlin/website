// inspired by http://foundation.zurb.com/sites/docs/table.html#scrolling-table
window.addEventListener('load', function() {
    "use strict";

    var wrap = function(element, wrapper) {
        element.parentElement.insertBefore(wrapper, element);
        wrapper.appendChild(element);
    };

    var getMinWidth = function(table, wrapper) {
        table.style.setProperty('white-space', 'nowrap');
        wrapper.style.setProperty('width', '0');
        var minWidth = table.getBoundingClientRect().width;
        wrapper.style.removeProperty('width');
        table.style.removeProperty('white-space');
        return minWidth + 'px';
    }

    var wrapTable = function(table) {
        var wrapper = document.createElement('div');
        wrapper.className = 'table-wrapper';
        wrap(table, wrapper);

        table.style.setProperty('min-width', getMinWidth(table, wrapper));
    };

    var tables = document.getElementsByTagName('table');

    for (var i = 0; i < tables.length; i++) {
        wrapTable(tables[i]);
    }
});
