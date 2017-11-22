var PageController = function() {
    this.dropdownItem = $( '.dropdown-menu li' );
    this.selectControl = $( '.select-control' );
    this.selectArrow = $( '.select2-selection__arrow' );
};

PageController.prototype.dropdown = function() {
    this.dropdownItem.on( 'click', function() {
        var selText = $( this ).text();
        $( this ).parents( '.dropdown' ).find( '.dropdown-toggle' ).html( selText + '<span class="fa fa-chevron-down"></span>' );
    });
};

PageController.prototype.selectCustomization = function() {
    this.selectControl.select2();
    this.selectArrow.hide();
    $( window ).resize(function() {
        this.selectControl.select2();
        this.selectArrow.hide();
    }.bind( this ));
};

PageController.prototype.terms = function() {
    $( '.checkbox .terms-link' ).on( 'click', function( event ) {
        event.preventDefault();
        $( '.terms-text' ).show( 'slow' );
    });

    $( document ).ready(function() {
        $( '.checkbox input' ).prop( 'checked', false );
    });

    $( document ).on( 'change touchstart', '.checkbox input', function( event ) {
        if ( $( '.checkbox input' ).prop( 'checked' ) ) {
            $( '.terms-text' ).hide( 'slow' );
            $( '.submit-popup-btn' ).prop( "disabled", false );
        } else {
            $( '.submit-popup-btn' ).prop( "disabled", true );
        }
    });
};

PageController.prototype.run = function() {
    this.dropdown();
    this.selectCustomization();
    this.terms();
};


$(function() {
    var controller = new PageController();
    controller.run();
});
