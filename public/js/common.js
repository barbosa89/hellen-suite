$('body').on('keydown', 'input, select, textarea', function(e) {
    var self = $(this),
        form = self.parents('form:eq(0)'),
        focusable, next;
    if (e.keyCode == 13) {
        focusable = form.find('input,a,select,button,textarea').filter(':visible');
        next = focusable.eq(focusable.index(this) + 1);
        if (next.length) {
            next.focus();
        } else {
            form.submit();
        }
        return false;
    }
});

$.fn.datepicker.dates['es'] = {
    days: ["Domingo", "Lunes", "martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
    daysShort: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sáb"],
    daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sá"],
    months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
    monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
    today: "Hoy",
    clear: "Limpiar",
    weekStart: 0
};

var lang = document.documentElement.lang;

$('.datepicker').datepicker({
    format: 'yyyy-mm-dd',
    language: lang
});

$('.selectpicker').selectpicker();


$('div.alert').not('.alert-important').delay(7000).fadeOut(350);

$.ajaxSetup({
    headers: {
        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
    }
});

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

function confirmAction(el, e) {
    var data = {
        '{url}': el.getAttribute('data-url'),
        '{method}': el.getAttribute('data-method'),
    }

    var modal = $('div.hide > div#modal-confirm').prop('outerHTML');

    $.each(data, function(key, value) {
        modal = modal.replace(new RegExp(key, 'g'), value);
    });

    $(modal).modal('show');
}

function calculateTotal(url, element, quantity) {
    if (!empty(element) && !empty(quantity)) {
        $.post(url, {
                element: element,
                quantity: quantity
            },
            function(data, status) {
                $('#total').removeAttr('value').attr('value', data.value);
                $('#total-input').show();
            }
        );
    } else {
        $('#total').removeAttr('value');
        $('#total-input').hide();
    }
}

function empty(data) {
    if (typeof(data) == 'number' || typeof(data) == 'boolean') {
        return false;
    }

    if (typeof(data) == 'undefined' || data === null) {
        return true;
    }

    if (typeof(data.length) != 'undefined') {
        return data.length == 0;
    }

    var count = 0;
    for (var i in data) {
        if (data.hasOwnProperty(i)) {
            count++;
        }
    }

    return count == 0;
}