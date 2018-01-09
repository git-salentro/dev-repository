//TODO Create React form instead
var PropertiesSettingsController = function () {
    this.selector = 'a[role="property-settings"]';
    this.nextSelector = 'a[role="next"]';
    this.backSelector = 'a[role="back"]';
    this.loadedSteps = [];
    this.currentStep = null;
};

//TODO Parse backend errors
PropertiesSettingsController.prototype.processResponse = function (response) {
    var that = this;

    $('#erp-settings-popup').find('.modal-body').append(response.html);
    $('#erp-settings-popup').find('.modal-title').html(response.modalTitle);
    $('body').trigger('popup-open');

    var modalBody =   $('#erp-settings-popup').find('.modal-body');
    var form = modalBody.find('#' + that.currentStep + ' form');

    if (form) {
        form.submit(function(e) {
            var $form = $(this);
            var currentStep = $form.find('button[type=submit]').attr('step');
            var data = {};

            if (currentStep === 'properties-confirmation') {
                data = {
                    erp_property_payment_settings: {
                        dayUntilDue: $('[name="erp_property_payment_settings[dayUntilDue]"]').val(),
                        paymentAmount: $('[name="erp_property_payment_settings[paymentAmount]"]').val()
                    }
                };

                if ($('[name="erp_property_payment_settings[allowPartialPayments]"]').is(":checked")) {
                    data.erp_property_payment_settings.allowPartialPayments = '1';
                }

                if ($('[name="erp_property_payment_settings[allowCreditCardPayments]"]').is(":checked")) {
                    data.erp_property_payment_settings.allowCreditCardPayments = '1';
                }

                if ($('[name="erp_property_payment_settings[allowAutoDraft]"]').is(":checked")) {
                    data.erp_property_payment_settings.allowAutoDraft = '1';
                }

            }

            $form.find('button[type=submit]').prop('disabled', true);

            $.each($form.serializeArray(), function(index, obj){
                data[obj.name] = obj.value;
            });

            $.ajax({
                type: 'POST',
                cache: false,
                url: form.attr('action'),
                data: data,
                async: true,
                dataType: 'json',
                success: function (response) {
                    if (response.redirect) {
                        window.location = response.redirect;

                        return;
                    }

                    $form.find('button[type=submit]').prop('disabled', false);
                    $('#' + that.currentStep).hide();

                    that.loadedSteps.push(currentStep);
                    that.currentStep = currentStep;

                    that.processResponse(response);
                }

            });
            return false;
        });

    }

    $('#erp-settings-popup').modal('show');
};

PropertiesSettingsController.prototype.initSettingsPopup = function () {
    if (!$('#erp-settings-popup').length) {
        var modalHtml =
            '<div class="modal fade" id="erp-settings-popup" tabindex="-1" role="dialog" aria-labelledby="my-modal-label" aria-hidden="true">'
                + '<div class="modal-dialog">'
                    + '<div class="modal-content">'
                        + '<div class="modal-header">'
                            + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'
                            + '<h4 class="modal-title"></h4>'
                        + '</div>'
                        + '<div class="modal-body"></div>'
                        + '<div class="modal-footer"></div>'
                    + '</div>';
                + '</div>';
            + '</div>';

        $(modalHtml).appendTo('body');
    }
};

PropertiesSettingsController.prototype.listenNext = function () {
    var that = this;
    $('body').on('click', this.nextSelector, function (e) {
        e.preventDefault();
        var $this = $(this);
        var currentStep = $this.attr('step');


        if (-1 === $.inArray(currentStep, that.loadedSteps)) {
            $.ajax({
                type: 'GET',
                cache: false,
                url: $this.attr('href'),
                dataType: 'json',
                async: true,
                success: function (response) {
                    $('#' + that.currentStep).hide();

                    that.loadedSteps.push(currentStep);
                    that.currentStep = currentStep;

                    that.processResponse(response);
                }
            });
        } else {
            $('#' + currentStep).show();
            $('#' + that.currentStep).hide();

            that.currentStep = currentStep;
        }
    });
};

PropertiesSettingsController.prototype.listenBack = function () {
    var that = this;
    $('body').on('click', this.backSelector, function (e) {
        e.preventDefault();
        var $this = $(this);
        var currentStep = $this.attr('step');

        $('#' + that.currentStep).hide();
        $('#' + currentStep).show();
        
        that.currentStep = currentStep;
    });
};

PropertiesSettingsController.prototype.listenClosePopup = function () {
    var that = this;
    $('#erp-settings-popup').on('hide.bs.modal', function (e) {
        that.loadedSteps = [];
        that.currentStep = null;
        $('#erp-settings-popup').find('.modal-body').empty();
    })
};

PropertiesSettingsController.prototype.run = function() {
    this.initSettingsPopup();
    this.listenNext();
    this.listenBack();
    this.listenClosePopup();
};

$(document).ready(function() {
    var controller = new PropertiesSettingsController();
    controller.run();
});
