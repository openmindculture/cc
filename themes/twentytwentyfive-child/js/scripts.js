( function () {
    'use strict';

    var navs = Array.prototype.slice.call( document.querySelectorAll( '.cc-band .wp-block-navigation' ) );

    if ( ! navs.length ) {
        return;
    }

    function measure( nav ) {
        var list = nav.querySelector( '.wp-block-navigation__container' );
        var container = nav.querySelector( '.wp-block-navigation__responsive-container' );
        if ( ! list || ! container || container.classList.contains( 'is-menu-open' ) ) {
            return; // Never re-measure while the overlay is open.
        }

        // Lay the links out inline on one line, read the widths, then decide.
        // Class changes and reads happen in one task, so nothing flashes.
        nav.classList.add( 'cc-nav--measuring' );
        nav.classList.remove( 'cc-nav--collapsed' );
        var overflows = list.scrollWidth > list.clientWidth + 1 || list.scrollWidth > container.clientWidth + 1;
        nav.classList.remove( 'cc-nav--measuring' );
        nav.classList.toggle( 'cc-nav--collapsed', overflows );
    }

    var pending = false;
    function measureAll() {
        if ( pending ) {
            return;
        }
        pending = true;
        window.requestAnimationFrame( function () {
            pending = false;
            navs.forEach( measure );
        } );
    }

    measureAll();

    if ( 'ResizeObserver' in window ) {
        var resizeObserver = new ResizeObserver( measureAll );
        navs.forEach( function ( nav ) {
            resizeObserver.observe( nav.closest( '.cc-band' ) || nav );
        } );
    } else {
        window.addEventListener( 'resize', measureAll, { passive: true } );
    }

    if ( document.fonts && document.fonts.ready ) {
        document.fonts.ready.then( measureAll );
    }
}() );

/**
 * 2. The window: a restrained, scroll-linked picture motion.
 *
 * Each element with the class "cc-window" (a Cover or Image block) gets a CSS
 * custom property --cc-progress from 0 to 1:
 *   0 = the frame's top edge is entering at the bottom of the viewport
 *   1 = the frame's centre has reached the viewport centre (then it stays 1)
 * style.css turns that number into a small drift of the picture inside its
 * frame (about 6% of the frame height), in the same direction as the scroll.
 *
 * - IntersectionObserver: scroll work runs only while a frame is near the viewport.
 * - requestAnimationFrame: at most one update per frame; passive listeners.
 * - Honours prefers-reduced-motion, including changes while the page is open.
 * - Without JavaScript, or with reduced motion, the picture is simply still.
 */
( function () {
    'use strict';

    var SELECTOR = '.cc-window';
    var frames = Array.prototype.slice.call( document.querySelectorAll( SELECTOR ) );

    if ( ! frames.length || ! ( 'IntersectionObserver' in window ) ) {
        return;
    }

    var reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' );
    var visible = new Set();
    var observer = null;
    var ticking = false;
    var listening = false;

    function clamp( value ) {
        return Math.min( 1, Math.max( 0, value ) );
    }

    function update() {
        ticking = false;
        var viewport = window.innerHeight || document.documentElement.clientHeight;

        visible.forEach( function ( frame ) {
            var rect = frame.getBoundingClientRect();
            var start = viewport; // Top edge at the bottom of the viewport.
            var end = ( viewport - rect.height ) / 2; // Frame centred in the viewport.
            var progress = start === end ? 1 : clamp( ( start - rect.top ) / ( start - end ) );
            frame.style.setProperty( '--cc-progress', progress.toFixed( 3 ) );
        } );
    }

    function requestUpdate() {
        if ( ! ticking ) {
            ticking = true;
            window.requestAnimationFrame( update );
        }
    }

    function listen( on ) {
        if ( on === listening ) {
            return;
        }
        listening = on;
        var method = on ? 'addEventListener' : 'removeEventListener';
        window[ method ]( 'scroll', requestUpdate, { passive: true } );
        window[ method ]( 'resize', requestUpdate, { passive: true } );
    }

    function onIntersect( entries ) {
        entries.forEach( function ( entry ) {
            if ( entry.isIntersecting ) {
                visible.add( entry.target );
            } else {
                visible.delete( entry.target );
            }
        } );
        listen( visible.size > 0 );
        requestUpdate();
    }

    function start() {
        if ( observer ) {
            return;
        }
        observer = new IntersectionObserver( onIntersect, {
            rootMargin: '10% 0px 10% 0px', // Begin just before the frame is visible.
            threshold: 0,
        } );
        frames.forEach( function ( frame ) {
            observer.observe( frame );
        } );
    }

    function stop() {
        if ( observer ) {
            observer.disconnect();
            observer = null;
        }
        listen( false );
        visible.clear();
        frames.forEach( function ( frame ) {
            frame.style.removeProperty( '--cc-progress' );
        } );
    }

    function applyPreference() {
        if ( reducedMotion.matches ) {
            stop();
        } else {
            start();
        }
    }

    applyPreference();

    if ( typeof reducedMotion.addEventListener === 'function' ) {
        reducedMotion.addEventListener( 'change', applyPreference );
    } else if ( typeof reducedMotion.addListener === 'function' ) {
        reducedMotion.addListener( applyPreference ); // Safari < 14.
    }
}() );