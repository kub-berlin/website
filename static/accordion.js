// wrap sections (starting with <h3>) in <details> elements.

var body = document.querySelector('.accordion');

if (body) {
    var untranslated = body.querySelector('.untranslated');
    if (untranslated) {
        body = untranslated;
    }

    var details = null;
    for (var i = 0; i < body.childNodes.length; i++) {
        var node = body.childNodes[i];
        if (node.nodeType === Node.ELEMENT_NODE && node.nodeName === 'H3') {
            details = document.createElement('details');
            node.before(details);

            var summary = document.createElement('summary');
            summary.className = 'accordion-summary';
            summary.append(node);
            details.append(summary);
        } else if (details) {
            details.append(node);
            i -= 1;
        }
    }
}
