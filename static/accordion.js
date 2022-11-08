// wrap sections (starting with <h3>) in <details> elements.

var body = document.querySelector('.accordion');

if (body) {
    var untranslated = body.querySelector('.untranslated');
    if (untranslated) {
        body = untranslated;
    }

    var details = document.createElement('details');

    // iterate backwards in order to not change the node indices
    var i = body.childNodes.length;
    while (i--) {
        var node = body.childNodes[i];
        if (node.nodeType === Node.ELEMENT_NODE && node.nodeName === 'H3') {
            node.before(details);

            var summary = document.createElement('summary');
            summary.className = 'accordion-summary';
            summary.append(node);
            details.prepend(summary);

            details = document.createElement('details');
        } else {
            details.prepend(node);
        }
    }

    // the first section does not have a heading, so it is unwrapped
    var k = details.childNodes.length;
    while (k--) {
        body.prepend(details.childNodes[k]);
    }
}
