var PropertiesSettingsController = function () {
    this.dateSelector = '.date';
    this.popupAncestorSelector = 'body';
};

PropertiesSettingsController.prototype.initErpPopup = function () {
    var self = this;
    var $popupAncestorSelector= $(self.popupAncestorSelector);
    var callback = function () {
        $('#epr-modal-popup').find(self.dateSelector).each(function () {
            $(this).datepicker({
                startDate: new Date(),
                minDate: new Date(),
                autoclose: true
            });
        });

    };
    var settings = {
        selector: 'a[ role="settings-popup" ]',
        callback: callback
    };

    $popupAncestorSelector.erpPopup(settings);
};

PropertiesSettingsController.prototype.run = function() {
    this.initErpPopup();
};

$( document ).ready(function() {
    var controller = new PropertiesSettingsController();
    controller.run();
});


