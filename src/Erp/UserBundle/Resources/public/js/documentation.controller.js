var DocumentationController = function () {
    var fileStatus;
};

DocumentationController.prototype.getUrlByRoute = function (route) {
    return $('input[name=route__' + route + ']').val();
};

DocumentationController.prototype.helloSignGetDocSigned = function (jsonData, urlOk, $objErr) {
    HelloSign.init(jsonData.CLIENT_ID);
    HelloSign.open({
        debug: true,
        url: jsonData.SIGN_URL,
        uxVersion: 2,
        messageListener: function (eventData) {
            if (eventData.event === HelloSign.EVENT_SIGNED) {
                $objErr.html('You successfully signed the document.');
                $.ajax({
                    url: urlOk,
                    type: 'POST',
                    data: {
                        signatureID: jsonData.SIGNATURE_ID
                    },
                    dataType: 'json',
                    success: function (response) {
                        console.log(response);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log('Sorry, something went wrong in saving signatureID into current document to sign. Please try again later.');
                    }
                });
            } else if (eventData.event === HelloSign.EVENT_CANCELED) {
                $objErr.html('You closed the document, please reopen to continue.');
            } else if (eventData.event === HelloSign.EVENT_ERROR) {
                $objErr.html('Uh oh something went wrong. Sorry about that!');
            } else if (eventData.event === HelloSign.EVENT_SENT) {
                //not used in this example
                //only used for embedded requesting
            }
        }
    });
};

DocumentationController.prototype.Edit = function () {
    var $this = this,
            that = this,
            docEdit = $('.doc-edit'),
            docDelete = $('.doc-delete'),
            docMenu = $('.dropdown-menu li'),
            okBtn = $('.ok-btn'),
            statusBtn = $('.btn-status'),
            signBtn = $('.signature-btn');

    docEdit.on('click', function (e) {
        var currentRow = $(e.currentTarget).parents('.doc-table-row'),
                isTenant = $(this).data('is-tenant'),
                currentText = currentRow.find('.file-name').text().replace(/ /g, '');

        $(this).css('display', 'none');
        currentRow.css('background-color', '#f8f8f8');
        currentRow.find('.ok-btn').css('display', 'inline-block');
        if (!isTenant) {
            currentRow.find('.dropdown .btn').css('border', '1px solid #e3e1e1');
            currentRow.find('.dropdown-toggle').removeClass('disabled').append('<span class="select-container"></span>');
        }
        currentRow.find('.file-name-line').css('display', 'none');
        currentRow.find('.file-name-input').css('display', 'inline-block').val(currentText);
    });

    docMenu.on('click', function () {
        var selectedOption = $(this),
                dropdownBtn = $(this).parents('.dropdown').find('.btn');

        dropdownBtn.html('');
        selectedOption.clone().append('<span class="select-container"></span>').appendTo(dropdownBtn);
        dropdownBtn.val($(this).data('value'));
        DocumentationController.fileStatus = selectedOption.data('status');
    });

    okBtn.on('click', function (e) {
        var currentRow = $(e.currentTarget).parents('.doc-table-row'),
                fileId = currentRow.find('.btn-status').data('file-id'),
                fileName = currentRow.find('.file-name-input').val(),
                route = $this.getUrlByRoute('erp_user_document_update_ajax');

        fileStatus = DocumentationController.fileStatus;

        $(this).hide();
        currentRow.find('.doc-edit').css('display', 'inline-block');
        if (currentRow.find('.dropdown .btn').text() !== '') {
            currentRow.find('.dropdown .btn').css('border', 'none');
            currentRow.find('.select-container').hide();
        }
        currentRow.find('.dropdown-toggle').addClass('disabled');
        currentRow.css('background-color', '#fff');
        currentRow.find('.file-name-line').css('display', 'block');
        currentRow.find('.file-name-input').hide();
        currentRow.find('.file-name-line a').text(fileName);

        route = route.replace('documentId', fileId);

        $.ajax({
            url: route,
            type: 'POST',
            data: {
                fileName: fileName,
                fileStatus: fileStatus
            },
            dataType: 'json',
            success: function (response) {
                if (response.errors) {
                    $('.errors').css({'opacity': 1}).html(response.errors).animate({'opacity': 0}, 3000);
                }
            }
        });
    });

    signBtn.on('click', function (event) {
        event.preventDefault();
        
        var $this = $(this);
        var $errorSpan = $this.siblings('.error');
        var label = $this.html();
        
        $errorSpan.html('');
        
        $this.attr('disabled', true).html('Loading...');
        var url = this.href || this.getAttribute('href');
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                var response;
                try {
                    response = JSON.parse(data);
                } catch (err) {
                    response = data;
                }
                
                if (response.SIGN_URL) {
                    that.helloSignGetDocSigned(response, $this.data('doc'), $errorSpan);
                } else {
                    $this.removeAttr('disabled').html(label).css('opacity', '1.0');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $errorSpan.html('Sorry, please try again later.');
                $this.removeAttr('disabled').html(label).css('opacity', '1.0');
            }
        });
    });

    if (statusBtn.data('selected-file-status') !== ' ') {
        statusBtn.parents('.doc-table-row').find('.dropdown .btn').css('border', 'none');
    }
};

DocumentationController.prototype.run = function () {
    this.Edit();

    var $submit = $('button[type=submit]'),
            $file = $('._file'),
            $error = $('.errors'),
            maxFileSize = $file.data('max-file-size');

    $file.fileValidator({
        onValidation: function (files) {
            $submit.removeAttr('disabled');
            $error.html('');
        },
        onInvalid: function (validationType, file) {
            $submit.attr('disabled', 'disabled');
            $error.html($file.data('max-file-size-message'));
        },
        maxSize: maxFileSize
    });
};

$(function () {
    var controller = new DocumentationController();
    controller.run();
});
