define(['durandal/composition', 'knockout', 'jquery', 'kloudspeaker/ui/texts'], function(composition, ko, $, texts) {
    var ctor = function() {};
    ctor.prototype.activate = function(settings) {
        this.settings = settings;
    };
    ctor.prototype.attached = function(e) {
        var that = this;
        var $e = $(e);
        var $input = $e.find("input");

        var api = kloudspeaker.ui.controls.datepicker($e, {
            format: texts.get('shortDateTimeFormat'),
            time: true,
            value: that.settings.value()
        });
        $e.on("changeDate", function(ev) {
            that.settings.value(api.get());
        });
        that.settings.value.subscribe(function(newValue) {
            api.set(newValue);
        });
    }
    return ctor;
});
