<?php

add_action( 'template_redirect', function () {
    if ( ! is_404() ) {
        return; // normal pages never pay anything
    }

    $map = [
        '/old-page/'  => '/new-page/',
        '/old-about/' => '/about/',
        // ...
    ];

    /* TODO possible existing links to map
    www.canaparo.org/Materials/Seminar_02/02_Poblacion_patagonia%20copy.pdf
    canaparo.org/Materials/Course_Space_cine/Part_02/Part_02_Lecture.pdf
    www.canaparo.org/Forum/Monthly_papers/Monthly_paper_32.pdf
    www.canaparo.org/Pdf_files/Quiroga_paper_1995.pdf
    www.canaparo.org/Materials/Course_Trapero/Trapero_presen_part_05.pdf
    www.canaparo.org/Materials/Course_Trapero/Trapero_presen_part_02.pdf
    www.canaparo.org/Materials/Course_Space_cine/05_Links.pdf
    www.canaparo.org/Materials/lecTures.shtml

    internal links not found externally
    https://www.canaparo.org/CC/contactUs.shtml
    https://www.canaparo.org/CC/briefBiography.shtml
    https://www.canaparo.org/Publications/arTicles.shtml
    https://www.canaparo.org/Publications/chaptersBooks.shtml
    https://www.canaparo.org/Publications/boOks.shtml
    https://www.canaparo.org/Publications/index.shtml
    https://www.canaparo.org/CC/about.shtml
    */

    $path = trailingslashit( wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) );

    if ( isset( $map[ $path ] ) ) {
        wp_safe_redirect( home_url( $map[ $path ] ), 301 );
        exit;
    }
} );