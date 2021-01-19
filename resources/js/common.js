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
    e.preventDefault()

    var data = {
        '{url}': el.getAttribute('data-url'),
        '{method}': el.getAttribute('data-method'),
    };

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

        $("#min_price").val('');
    }
});

$("#tax_status").on('change', function(e) {
    if (parseInt(this.value) > 0) {
        if ($('#tax-input').is(':hidden')) {
            $('#tax-input').fadeIn();
            $('#tax').attr('required', 'required');
        }
    } else {
        if ($('#tax-input').is(':visible')) {
            $('#tax-input').fadeOut();
            $('#tax').value = '';
            $('#tax').removeAttr('required');
        }
    }
});

function listRoomsByHotel(hotel) {
    $.ajax({
        type: 'GET',
        url: route('api.web.rooms.index', hotel),
        data: {
            hotel: hotel
        },
        success: function(result) {
            var rooms = result.rooms;
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

$('#remove-room').on('click', function() {
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

$('#hotel').on('change', function() {
    $('#room-list').fadeOut();
    $('#any-place').fadeOut();

    $("#assign").html(['<option value="room">' + translator.trans('rooms.room') + '</option>', '<option value="any">' + translator.trans('assets.anyPlace') + '</option>']);
    $("#assign").selectpicker('refresh');
});

$('#assign').on('change', function() {
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

function confirmRedirect(e, url) {
    e.preventDefault();

    Swal.fire({
        title: translator.trans('common.attention'),
        text: translator.trans('common.confirmAction'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: translator.trans('common.continue'),
        cancelButtonText: translator.trans('common.cancel')
    }).then(result => {
        if (result.value) {
            window.location.href = url;
        }
    });
}

function getRoomPriceByNumber(hotel, number) {
    $.ajax({
        type: 'POST',
        url: '/rooms/price',
        data: {
            hotel: hotel,
            number: number
        },
        success: function(result) {
            $('#price').attr('value', Math.round(parseInt(result.price)))
                .attr('min', Math.round(parseInt(result.min_price)))
                .attr('max', Math.round(parseInt(result.price)));

            $('span#tax-value').text(parseFloat(result.tax) * 100);
        },
        error: function(xhr) {
            toastr.error(
                'Ha ocurrido un error',
                'Error'
            );
        }
    });
}

// {
//     url: '/vehicles/search',
//     list_id: 'list',
//     item_container: 'item-search',
//     render: render
// }

/**
 * Object params.
 *
 * string   url             The URI to query
 * string   list_id         The ID of list, include list headers
 * string   item_container  The container ID where the result will be rendered
 * function render          The method to render the results in a string template
 */

/**
 * Standar search methods.
 *
 * @param object event
 * @param string query
 * @param object params
 * @return void
 */
function std_search(event, query, params) {
    event.preventDefault();

    if (query.length == 0) {
        $('#' + params.list_id).hide();
        $('#' + params.item_container).empty();
    }

    if (query.length >= 3) {
        $.ajax({
            url: params.url + '?query=' + query,
            success: function(result) {
                let data = JSON.parse(result.data);

                if (data.length) {
                    $('#' + params.item_container).empty();

                    data.forEach(item => {
                        $('#' + params.item_container).append(params.render(item));
                    });

                    $('#' + params.list_id).show();
                } else {
                    toastr.info(
                        translator.trans('common.noRecords'),
                        translator.trans('common.attention')
                    );
                }
            },
            error: function(xhr) {
                toastr.error(
                    translator.trans('common.error'),
                    'Error'
                );
            }
        });
    }
}

/**
 * Generate standart bar chart.
 *
 * @param string id
 * @param array datasets
 * @return void
 */
function generate_chart(id, datasets) {
    let ctx = document.getElementById(id);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                translator.trans('months.january'),
                translator.trans('months.february'),
                translator.trans('months.march'),
                translator.trans('months.april'),
                translator.trans('months.may'),
                translator.trans('months.june'),
                translator.trans('months.july'),
                translator.trans('months.august'),
                translator.trans('months.september'),
                translator.trans('months.october'),
                translator.trans('months.november'),
                translator.trans('months.december')
            ],
            datasets: datasets
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
}

function buildHotelSelect(id) {
    let select = document.getElementById(id)

    axios.post('/hotels/assigned')
        .then(response => {
            if (response.data.hotels.length) {
                response.data.hotels.forEach(hotel => {
                    let text = document.createTextNode(hotel.business_name)

                    let node = document.createElement('option')
                    node.appendChild(text)
                    node.value = hotel.hash

                    select.appendChild(node)
                })
            } else {
                // Redirect if the parent user has not created hotels
                window.location.href = '/home'
            }
        })
}