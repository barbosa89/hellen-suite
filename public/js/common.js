const translator = new I18n();

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

$("#min_price").on('keyup', function(e) {
    var price = $("#price").val();

    if (parseFloat($("#min_price").val()) > parseFloat(price)) {
        toastr.info(
            'El precio mínimo es mayor al valor de la habitación',
            'Ciudado'
        );

        $("#min_price").val(price * 0.5);
    }
});

$("#min_price").on('focusout', function(e) {
    var price = parseFloat($("#price").val());
    var min_price = parseFloat($("#min_price").val());

    if (min_price < (price * 0.5)) {
        toastr.info(
            'El precio mínimo es muy bajo',
            'Ciudado'
        );

        $("#min_price").val(price * 0.5);
    }
});

$("#tax_status").on('change', function(e) {
    if (parseInt(this.value) > 0) {
        if ($('#tax-input').is(':hidden')) {
            $('#tax-input').fadeIn();
        }
    } else {
        if ($('#tax-input').is(':visible')) {
            $('#tax-input').fadeOut();
            $('#tax').value = '';
        }
    }
});

// $(document).ready(function() {
//     $('#room-store').on('click', function(e) {
//         e.preventDefault();

//         $("#room-form").submit(function(e) {
//             e.preventDefault();
//         });

//         var price = $('#price').val();
//         var min_price = $('#min_price').val();

//         if (min_price <= (price * 0.75)) {
//             Swal.fire({
//                 title: 'Estás seguro?',
//                 text: "El precio mínimo es muy bajo, ¿desea continuar?",
//                 type: 'warning',
//                 showCancelButton: true,
//                 confirmButtonColor: '#3085d6',
//                 cancelButtonColor: '#d33',
//                 confirmButtonText: 'Continuar!',
//                 cancelButtonText: 'Cancelar'
//             }).then(function(result) {
//                 if (result.value) {
//                     $('#room-form').submit();
//                 }
//             });
//         } else {
//             $('#room-form').submit();
//         }
//     });
// });


function listRoomsByHotel(hotel) {
    $.ajax({
        type: 'POST',
        url: '/rooms/list',
        data: {
            hotel: hotel
        },
        success: function(result) {
            var rooms = JSON.parse(result.rooms);
            $('#room').empty();

            if (rooms.length) {
                if ($("#room-list").is(':hidden')) {
                    $("#room-list").fadeIn();
                    $("#room").attr('required', 'required');
                }

                var newOptions = [];
                rooms.forEach(function(room) {
                    newOptions.push("<option value=" + room.hash + ">" + room.number + "</option>");
                });

                $("#room").html(newOptions);
                $("#room").selectpicker('refresh');
            } else {
                toastr.info(
                    'El hotel seleccionado no tiene habitaciones',
                    'Sin habitaciones'
                );

                $('#room').val('');

                if ($("#room-list").is(':visible')) {
                    $("#room-list").fadeOut();
                    $("#room").removeAttr('required');
                }
            }
        },
        error: function(xhr) {
            toastr.error(
                'Ha ocurrido un error',
                'Error'
            );
        }
    });
}

$('#remove-room').click(function() {
    options = [];
    $("#room").children().each(function(index, item) {
        item.removeAttribute('selected');

        if (index > 0) {
            options.push(item);
        }
    });

    $("#room").html(options);
    $("#room").selectpicker('refresh');
});

$('#hotel').change(function() {
    $('#room-list').fadeOut();
    $('#any-place').fadeOut();

    $("#assign").html(['<option value="room">' + translator.trans('rooms.room') + '</option>', '<option value="any">' + translator.trans('assets.anyPlace') + '</option>']);
    $("#assign").selectpicker('refresh');
});

$('#assign').change(function() {
    if (this.value == 'room') {
        listRoomsByHotel($('#hotel').val());

        $('#any-place').fadeOut();
        $("#location").removeAttr('required');
    } else {
        if ($('#any-place').is(':hidden')) {
            $('#room-list').fadeOut();
            $("#room").removeAttr('required');

            $('#any-place').fadeIn();
            $("#location").attr('required', 'required');
        }
    }
});