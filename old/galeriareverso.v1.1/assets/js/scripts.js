document.addEventListener('DOMContentLoaded', () => {



    //add captions to lighbox elements
    if( p.page( ['single-rg_artist', 'wpmlobj-id-1725'] ) ) {


        document.querySelectorAll('.rg-eg-holder :is([data-skin="artist-objects"],[data-skin="shop"])').forEach( element => {

            const 

                temp = document.createElement('div'),

                info = element.querySelector('.rg-info').cloneNode( true ),

                info_artist_link = info.querySelector('.rg-info-item.artist a'),

                trigger = element.querySelector('.esgbox');

            if( info_artist_link ) {

                info_artist_link.removeAttribute('class'); 

            }

            temp.appendChild( info );

            trigger.setAttribute('data-caption', temp.innerHTML);

        });


    }

});