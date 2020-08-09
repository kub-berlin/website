window.addEventListener('load', function() {
    var header = document.getElementById('header');
    var nav = document.getElementById('nav');
    var langNav = document.getElementById('language-nav');

    var wrapper = document.createElement('div');
    wrapper.innerHTML = '<button type="button" class="menu-button"></button>';

    var menuButton = wrapper.children[0];
    menuButton.innerHTML = '<i class="icon-menu"></i>';

    menuButton.addEventListener('click', function(event) {
        nav.classList.toggle('is-visible');
        langNav.classList.toggle('is-visible');
        menuButton.classList.toggle('is-active');
    });

    header.appendChild(menuButton);
});
