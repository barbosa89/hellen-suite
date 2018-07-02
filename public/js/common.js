$('.selectpicker').selectpicker();

$('div.alert').not('.alert-important').delay(7000).fadeOut(350);

function changeIcon(el, e, first, second) {
    e.preventDefault();
    var ico = $(el).find('span').first();

    if ($(ico).hasClass(first)) {
        $(ico).removeClass(first);
        $(ico).addClass(second);
    } else {
        $(ico).removeClass(second);
        $(ico).addClass(first);
    }
}

function sendFormByConfirmation() {

}