/**
 * frontend.js
 *
 * @package S2 Wishlist
 * @since   1.0.0
 * @author Shuban Studio <shuban.studio@gmail.com>
 * @version 1.0.5
 */

/* global s2_wishlist_frontend */
jQuery( document ).ready( function( $ ) {
    'use strict';

    /* slick slider */
    $( '.wishlist-popbox-products' ).slick( {
        infinite: false,
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2000,
        centerMode: true,
        pauseOnHover: false,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                }
            }
        ]
    } );

    var slick = $( '.wishlist-popbox-products' ).slick( 'getSlick' );
    if( slick.$list !== undefined ) slick.$list.width( $( window ).width() );

    // load wishilist
    var load_wishlist = function() {

        var start = $( '#start-wishlist-popbox-products' ).val();
        var end   = $( '#end-wishlist-popbox-products' ).val();

        var data = {
            start:      start,
            end:        end,
            template:   'frontend/wishlist-popbox-content-product.php',
            action:     'load_wishlist',
            security:   s2_wishlist_frontend.wishlist_nonce
        };

        $.ajax( {
            url:  s2_wishlist_frontend.ajaxurl,
            data: data,
            type: 'POST',
            success: function( response ) {
                response = JSON.parse( response );

                $( '#start-wishlist-popbox-products' ).val( response.start );
                $( '#end-wishlist-popbox-products' ).val( response.end );
                $( '#load-more-wishlist-popbox-products' ).val( response.load_more );

                // responsive option breaks html so, before add slide remove responsive option
                var responsive = $( '.wishlist-popbox-products' ).slick( 'getOption', 'responsive' );
                $( '.wishlist-popbox-products' ).slick( 'setOption', 'responsive', null, false );

                $.each( response.html, function(index, element) {
                    $( 'div.wishlist-popbox-products' ).slick( 'slickAdd', element );
                });

                // add slide responsive option
                $( '.wishlist-popbox-products' ).slick( 'setOption', 'responsive', responsive, false );

                var slick = $( '.wishlist-popbox-products' ).slick( 'getSlick' );
                slick.$list.width( $( window ).width() );
                $( '.wishlist-popbox-products' ).slick( 'resize' );
            }
        } );

    };

    $( document ).on( 'click', '.s2-wishlist', function( e ) {

        e.preventDefault();

        var product  = $( this );
        var wishlist = product.attr( 'data-wishlist' );

        var action = '';
        if ( wishlist == 'true' ) {

            wishlist = 'false';
            action   = 'remove_product_from_wishlist';

            product.removeClass( 's2-wishlist-selected' );

        } else {

            wishlist = 'true';
            action   = 'add_product_in_wishlist';

            product.addClass( 's2-wishlist-selected' );

        }

        var data = {
            product_id: product.data( 'product_id' ),
            action:     action,
            security:   s2_wishlist_frontend.wishlist_nonce
        };

        $.ajax( {
            url:  s2_wishlist_frontend.ajaxurl,
            data: data,
            type: 'POST',
            success: function( response ) {
                product.attr( 'data-wishlist', wishlist );

                $( '#start-wishlist-popbox-products' ).val( -5 );
                $( '#end-wishlist-popbox-products' ).val( 0 );

                $( '.wishlist-popbox-products' ).slick( 'getSlick' ).$slideTrack.children().remove();
                load_wishlist();
            }

        } );

    } );

    $( document ).scroll( function( e ) {

        if( $( 'li.last-wishlist-product' ).length == 1 ) {

            var scrollTop = $( window ).scrollTop();
            var top       = $( 'li.last-wishlist-product' ).offset().top;
            var loadMore  = $( '#load-more-wishlist-products' ).val();

            if ( loadMore == 'true' && scrollTop >= top ) {

                $( 'ul.wishlist-products li' ).removeClass( 'last-wishlist-product' );

                var start    = $( '#start-wishlist-products' ).val();
                var end      = $( '#end-wishlist-products' ).val();
                var template = $( '#wishlist-template' ).val();
                var user_id  = $( '#user-id' ).val();

                var data = {
                    start:      start,
                    end:        end,
                    template:   template,
                    user_id:    user_id,
                    action:     'load_wishlist',
                    security:   s2_wishlist_frontend.wishlist_nonce
                };

                $.ajax( {
                    url:  s2_wishlist_frontend.ajaxurl,
                    data: data,
                    type: 'POST',
                    success: function( response ) {
                        response = JSON.parse( response );

                        $( '#start-wishlist-products' ).val( response.start );
                        $( '#end-wishlist-products' ).val( response.end );
                        $( '#load-more-wishlist-products' ).val( response.load_more );

                        $.each( response.html, function(index, element) {
                            $( 'ul.wishlist-products' ).append( element );
                        });
                    }
                } );

            }

        }

    } );

    $( '.wishlist-popbox-products' ).on( 'beforeChange', function( event, slickObject, currentSlide, animSlide) {

        var loadMore = $( '#load-more-wishlist-popbox-products' ).val();
        if( loadMore == 'true' && animSlide % 2 == 0 ) {

            $( 'div.wishlist-popbox-products div div.last-wishlist-product' ).removeClass( 'last-wishlist-product' );

            load_wishlist();

        }

    } );

    $( 'div.sticky-s2-wishlist' ).find( 'span.label' ).on( 'click', function() {

        $( 'div.footerbox div.popbox' ).slideToggle( {
            duration: 'slow',
            start: function( animation ) {

                // show overlay
                if( $( 'div.footerbox div.popbox:hidden' ).length == 0 ) {
                    
                    $( '#wishlist-popbox-overlay' ).show();

                }

            },
            complete: function() {

                // hide overlay
                if( $( 'div.footerbox div.popbox:hidden' ).length == 1 ) {

                    $( '#wishlist-popbox-overlay' ).hide();

                }

            }
        } );

    } );
    /* slick slider */

    $( document ).on( 'click', '#wishlist-popbox-overlay', function( e ) {
        $( 'div.sticky-s2-wishlist' ).find( 'span.label' ).trigger( 'click' );
    } );

} );
