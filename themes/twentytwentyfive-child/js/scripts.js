document.addEventListener('DOMContentLoaded', function () {
    var themeMenuOpener = document.getElementById('site-navigation-menu-link');
    if (themeMenuOpener) {
        themeMenuOpener.addEventListener('click', function (event) {
            event.preventDefault();
            var themeNavigationMenu = document.getElementById('navigation-menu');
            if (themeNavigationMenu && themeNavigationMenu.classList) {
                themeNavigationMenu.classList.add('active');
                var themeNavigationWrapper = document.getElementById('navigation-menu');
                if (themeNavigationWrapper) {
                    themeNavigationWrapper.classList.add('active');
                }
            }
            document.body.classList.add('is-menu-open');
        });
    }

    var themeMenuCloser = document.getElementById('site-navigation-menu-close');
    if (themeMenuCloser) {
        themeMenuCloser.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            var themeNavigationMenu = document.getElementById('navigation-menu');
            if (themeNavigationMenu && themeNavigationMenu.classList) {
                themeNavigationMenu.classList.remove('active');
                var themeNavigationWrapper = document.getElementById('navigation-menu');
                if (themeNavigationWrapper) {
                    themeNavigationWrapper.classList.remove('active');
                }
            }
            document.body.classList.remove('is-menu-open');
        });
    }

    const detailsBlocksCloseButtons = document.querySelectorAll('details .wp-block-details-close');
    for (const closeButton of detailsBlocksCloseButtons) {
        closeButton.addEventListener('click', () => {
            const details = closeButton.closest('details');
            details.classList.add('collapsing');
            details.removeAttribute('open');
            window.setTimeout(() => {
                details.classList.remove('collapsing');
            }, 2000)
        })
    }

    if (navigator.clipboard && location.origin) {
        const copyTriggers = document.querySelectorAll('.copy-link-location');
        console.log(copyTriggers);
        for (const trigger of copyTriggers) {
            console.log(trigger);
            trigger.addEventListener('click', async (e) => {
                e.preventDefault();
                await navigator.clipboard.writeText(location.origin + location.pathname);
                alert('Die Web-Adresse wurde in die Zwischenabglage kopiert.');
            });
            trigger.classList.remove('initially-hidden');
        }
    }

    /* ensure indicating JS support, should be done by WordPress core, TODO remove when obsolete */
    document.documentElement.className = document.documentElement.className.replace('no-js', 'js');
});
