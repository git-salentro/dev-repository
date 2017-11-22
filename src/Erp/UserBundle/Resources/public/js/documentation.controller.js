var DocumentationController = function () {
    var fileStatus;
};

DocumentationController.prototype.getUrlByRoute = function( route ) {
    return $( 'input[name=route__' + route + ']' ).val();
};

DocumentationController.prototype.Edit = function() {

    var $this = this,
        docEdit = $( '.doc-edit' ),
        docDelete = $( '.doc-delete' ),
        docMenu = $( '.dropdown-menu li' ),
        okBtn = $( '.ok-btn' ),
        statusBtn = $( '.btn-status' );

    docEdit.on( 'click', function (e) {
        var currentRow = $( e.currentTarget ).parents( '.doc-table-row' ),
            isTenant = $( this).data('is-tenant'),
            currentText =  currentRow.find( '.file-name' ).text().replace(/ /g,'');

        $( this ).css( 'display', 'none' );
        currentRow.css( 'background-color', '#f8f8f8' );
        currentRow.find( '.ok-btn' ).css( 'display', 'inline-block' );
        if ( !isTenant ) {
            currentRow.find( '.dropdown .btn' ).css( 'border', '1px solid #e3e1e1' );
            currentRow.find( '.dropdown-toggle' ).removeClass( 'disabled' ).append( '<span class="select-container"></span>' );
        }
        currentRow.find( '.file-name-line' ).css( 'display', 'none' );
        currentRow.find( '.file-name-input' ).css( 'display', 'inline-block' ).val( currentText );
    });

    docMenu.on( 'click', function() {
        var selectedOption = $( this ),
            dropdownBtn = $( this ).parents( '.dropdown' ).find( '.btn' );

        dropdownBtn.html('');
        selectedOption.clone().append( '<span class="select-container"></span>' ).appendTo( dropdownBtn );
        dropdownBtn.val( $( this ).data( 'value' ) );
        DocumentationController.fileStatus = selectedOption.data( 'status' );
    });

    okBtn.on( 'click', function (e) {
        var currentRow =  $( e.currentTarget ).parents( '.doc-table-row' ),
            fileId = currentRow.find( '.btn-status' ).data( 'file-id' ),
            fileName = currentRow.find( '.file-name-input' ).val(),
            route = $this.getUrlByRoute( 'erp_user_document_update_ajax' );

        fileStatus = DocumentationController.fileStatus;

        $( this ).hide();
        currentRow.find( '.doc-edit' ).css('display', 'inline-block');
        if ( currentRow.find( '.dropdown .btn' ).text() !== '' ) {
            currentRow.find( '.dropdown .btn' ).css( 'border', 'none' );
            currentRow.find( '.select-container' ).hide();
        }
        currentRow.find( '.dropdown-toggle' ).addClass( 'disabled' );
        currentRow.css( 'background-color', '#fff' );
        currentRow.find( '.file-name-line' ).css( 'display', 'block' );
        currentRow.find( '.file-name-input' ).hide();
        currentRow.find( '.file-name-line a' ).text( fileName );

        route = route.replace( 'documentId', fileId );

        $.ajax({
            url: route,
            type: 'POST',
            data: {
                fileName: fileName,
                fileStatus: fileStatus
            },
            dataType: 'json',
            success: function ( response ) {
                if (response.errors) {
                    $( '.errors' ).css( {'opacity': 1} ).html( response.errors ).animate( {'opacity': 0}, 3000 );
                }
            }
        });
    });

    if ( statusBtn.data( 'selected-file-status' ) !== ' ') {
        statusBtn.parents( '.doc-table-row' ).find( '.dropdown .btn' ).css( 'border', 'none' );
    }
};

DocumentationController.prototype.run = function () {
    this.Edit();

    var $submit = $('button[type=submit]'),
        $file = $('._file'),
        $error = $('.errors'),
        maxFileSize = $file.data('max-file-size');

    $file.fileValidator({
        onValidation: function(files) {
            $submit.removeAttr('disabled');
            $error.html('');
        },
        onInvalid:    function(validationType, file) {
            $submit.attr('disabled', 'disabled');
            $error.html( $file.data('max-file-size-message') );
        },
        maxSize: maxFileSize
    });
};

$( function () {
    var controller = new DocumentationController();
    controller.run();
});
