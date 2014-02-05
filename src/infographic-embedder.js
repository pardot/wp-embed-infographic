/* Save Values */
var prev = jQuery("#embed_width").val();
var unit = jQuery("#embed_unit").val();
var initial = jQuery("#embedcode").val();

/* Updater */
function infographic_embedder_update() {
    prev = jQuery('#embed_width').val();
    unit = jQuery("#embed_unit").val();
    initial = jQuery("#embedcode").val();
    var _test = new RegExp(/width=['"][0-9]{1,4}%?['"]/);
    if ( prev != '' ) {
        if ( unit != 'px' ) {
            unit = '%';
        } else {
            unit = '';
        }
        jQuery("#embedcode").val(initial.replace(_test, "width=\"" + prev + unit + "\""));
        jQuery("#embed_width_hidden").text(initial.replace(_test, "width=\"" + prev + unit + "\""));
    }
}

/* Update Event */
jQuery("#embed_width").keyup(function() {
    infographic_embedder_update();
});
jQuery("#embed_unit").change(function() {
    infographic_embedder_update();
});

/* Prevent Weird Characters */
jQuery('#embed_width').on('keypress', function(ev) {
    var keyCode = window.event ? ev.keyCode : ev.which;
    if (keyCode < 48 || keyCode > 57) {
        if (keyCode != 0 && keyCode != 8 && keyCode != 13 && !ev.ctrlKey) {
            ev.preventDefault();
        }
    }
});

/* Select on Focus */
jQuery("#embedcode").focus(function() {
    var $this = jQuery(this);
    $this.select();
    $this.mouseup(function() {
        $this.unbind("mouseup");
        return false;
    });
});
