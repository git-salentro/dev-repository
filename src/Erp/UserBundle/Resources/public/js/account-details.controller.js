var UserController = function() {
    this.routeDeletePhoto = $( '#route_delete_photo' ).val();
};

UserController.prototype.customUpload = function() {
    var multipleSupport = typeof $( '<input/>' )[0].multiple !== 'undefined',
        isIE = /msie/i.test( navigator.userAgent ),
        inputCustom = $( 'input[type=file]' );

    $.fn.customFile = function() {
        return this.each(function() {
            var $file = $( this ).addClass( 'custom-file-upload-hidden' ),
                $wrap = $( '<div class="file-upload-wrapper">' ),
                $button = $( '<button type="button" class="btn red-btn file-upload-button">Select File</button>' ),
                $input = $( '<textarea class="file-upload-input" disabled="disabled"></textarea>' ),
                $label = $( '<label class="btn red-btn file-upload-button" for="' + $file[0].id + '">Select File</label>' );

            $file.css({
                position: 'absolute',
                left: '-9999px'
            });

            $wrap.insertAfter( $file )
                .append( $file, ( isIE ? $label : $button ), $input );
            $file.attr( 'tabIndex', -1 );
            $button.attr( 'tabIndex', -1 );
            $button.click( function() {
                $file.focus().click();
            });

            $file.change(function() {
                var files = [],
                    fileArr, filename;

                if ( multipleSupport ) {
                    fileArr = $file[0].files;
                    for ( var i = 0, len = fileArr.length; i < len; i++ ) {
                        files.push( fileArr[i].name );
                    }
                    filename = files.join( ', ' );
                } else {
                    filename = $file.val().split( '\\' ).pop();
                }

                $input.val( filename ).attr( 'title', filename )
            });

            $input.on({
                blur: function() {
                    $file.trigger( 'blur' );
                },
                keydown: function(e) {
                    if ( e.which === 13 ) {
                        if ( !isIE ) {
                            $file.trigger( 'click' );
                        }
                    } else if ( e.which === 8 || e.which === 46 ) {
                        $file.replaceWith( $file = $file.clone( true ) );
                        $file.trigger( 'change' );
                        $input.val( '' );
                    } else if ( e.which === 9 ) {
                        return this;
                    } else {
                        return false;
                    }
                }
            });
        });
    };

    inputCustom.customFile();
};

UserController.prototype.deleteImage = function() {
    var self = this,
        photoDelete = $( '#delete-user-photo' );

    photoDelete.click(function() {
        if ( confirm( 'Are you sure to delete photo?' ) ) {
            $.ajax({
                url: self.routeDeletePhoto,
                type: 'POST',
                success: function() {
                    window.location.reload();
                }
            });
        }
    });
};

UserController.prototype.run = function() {
    this.customUpload();
    this.deleteImage();

    var $submit = $('button[type=submit]'),
        $image = $('._image'),
        $error = $('.errors'),
        maxFileSize = $image.data('max-file-size');

    $image.fileValidator({
        onValidation: function(files) {
            $error.html('');
        },
        onInvalid:    function(validationType, file) {
            $image.val('');
            switch (validationType) {
                case 'type':
                    $error.html($image.data('mime-types-message'));
                    break;
                case 'maxSize':
                    $error.html($image.data('max-file-size-message'));
                    break;
                default:
                    $error.html('Unknown error');
                    break;
            }
        },
        maxSize: maxFileSize,
        type: 'image'
    });
}

$(function() {
    var controller = new UserController();
    controller.run();
});
