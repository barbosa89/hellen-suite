import { wTrans } from "laravel-vue-i18n"
import { toast } from 'vue3-toastify'
import { Chart } from "chart.js"

document.body.addEventListener('keydown', e => {
    const target = e.target

    // Check if the event target is an input, select, or textarea
    if (['INPUT', 'SELECT', 'TEXTAREA'].includes(target.tagName)) {
        const form = target.closest('form')

        if (e.keyCode === 13) {  // Enter key
            e.preventDefault()

            // Get all visible, focusable elements within the form
            const focusable = Array.from(form.querySelectorAll('input, a, select, button, textarea'))
                .filter(el => el.offsetWidth > 0 || el.offsetHeight > 0 || el === document.activeElement)

            // Find the next focusable element
            const nextIndex = focusable.indexOf(target) + 1
            const next = focusable[nextIndex]

            if (next) {
                next.focus()
            } else {
                form.submit()
            }
        }
    }
})

document.querySelectorAll('div.alert:not(.alert-important)').forEach(alert => {
    setTimeout(() => {
        alert.style.transition = 'opacity 0.35s'
        alert.style.opacity = '0'

        // Remove the element from the DOM after fade-out
        setTimeout(() => alert.remove(), 350)
    }, 7000)
})

function changeIcon(el, event, first, second) {
    event.preventDefault();
    const icon = el.querySelector('span');

    if (icon.classList.contains(first)) {
        icon.classList.remove(first);
        icon.classList.add(second);
    } else {
        icon.classList.remove(second);
        icon.classList.add(first);
    }
}

function confirmAction(el, event) {
    event.preventDefault();

    const data = {
        '{url}': el.getAttribute('data-url'),
        '{method}': el.getAttribute('data-method')
    };

    let modal = document.querySelector('div.hide > div#modal-confirm').outerHTML;

    for (const [key, value] of Object.entries(data)) {
        modal = modal.replace(new RegExp(key, 'g'), value);
    }

    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = modal;
    const modalElement = tempDiv.firstChild;

    document.body.appendChild(modalElement);
    const bootstrapModal = new bootstrap.Modal(modalElement);
    bootstrapModal.show();

    modalElement.addEventListener('hidden.bs.modal', () => {
        modalElement.remove();
    });
}

function calculateTotal(url, element, quantity) {
    if (element && quantity) {
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                element: element,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            const totalElement = document.getElementById('total');
            totalElement.removeAttribute('value');
            totalElement.setAttribute('value', data.value);

            const totalInput = document.getElementById('total-input');
            totalInput.style.display = 'block';
        })
        .catch(error => console.error('Error:', error));
    } else {
        const totalElement = document.getElementById('total');
        totalElement.removeAttribute('value');

        const totalInput = document.getElementById('total-input');
        totalInput.style.display = 'none';
    }
}

function empty(data) {
    if (typeof (data) == 'number' || typeof (data) == 'boolean') {
        return false
    }

    if (typeof (data) == 'undefined' || data === null) {
        return true
    }

    if (typeof (data.length) != 'undefined') {
        return data.length == 0
    }

    var count = 0
    for (var i in data) {
        if (data.hasOwnProperty(i)) {
            count++
        }
    }

    return count == 0
}

document.getElementById("min_price")?.addEventListener("keyup", () => {
    const price = parseFloat(document.getElementById("price").value);
    const minPriceInput = document.getElementById("min_price");
    const minPrice = parseFloat(minPriceInput.value);

    if (minPrice > price) {
        toast.info('El precio mínimo es mayor al valor de la habitación'); // TODO: Add translation
        minPriceInput.value = '';
    }
})

document.getElementById("tax_status")?.addEventListener("change", () => {
    const taxInput = document.getElementById("tax-input");
    const taxField = document.getElementById("tax");

    if (parseInt(this.value) > 0) {
        if (taxInput.style.display === "none" || taxInput.style.display === "") {
            taxInput.style.display = "block";
            taxField.setAttribute("required", "required");
        }
    } else {
        if (taxInput.style.display === "block") {
            taxInput.style.display = "none";
            taxField.value = "";
            taxField.removeAttribute("required");
        }
    }
});

function listRoomsByHotel(hotel) {
    axios.get(route('api.web.rooms.index', hotel), {
        params: { hotel: hotel }
    })
    .then(response => {
        const rooms = response.data.rooms;
        const roomSelect = document.getElementById("room");
        roomSelect.innerHTML = ""; // Clear current options

        if (rooms.length) {
            const roomList = document.getElementById("room-list");

            // Show the room list and make it required if hidden
            if (roomList.style.display === "none" || roomList.style.display === "") {
                roomList.style.display = "block";
                roomSelect.setAttribute("required", "required");
            }

            // Populate new options
            rooms.forEach(room => {
                const option = document.createElement("option");
                option.value = room.hash;
                option.textContent = room.number;
                roomSelect.appendChild(option);
            });
        } else {
            toast.info('El hotel seleccionado no tiene habitaciones');
            roomSelect.value = ""; // Clear the selection

            const roomList = document.getElementById("room-list");

            // Hide the room list and remove required attribute if visible
            if (roomList.style.display === "block") {
                roomList.style.display = "none";
                roomSelect.removeAttribute("required");
            }
        }
    })
    .catch(() => {
        toast.error('Ha ocurrido un error');
    });
}

document.getElementById('remove-room')?.addEventListener('click', () => {
    const options = [];
    const roomSelect = document.getElementById("room");

    Array.from(roomSelect.children).forEach((item, index) => {
        item.removeAttribute('selected');

        if (index > 0) {
            options.push(item);
        }
    });

    roomSelect.innerHTML = ""; // Clear the current options
    options.forEach(option => {
        roomSelect.appendChild(option); // Append options back excluding the first one
    });
});

document.getElementById('hotel')?.addEventListener('change', () => {
    const roomList = document.getElementById('room-list');
    const anyPlace = document.getElementById('any-place');
    const assignSelect = document.getElementById('assign');

    // Fade out the 'room-list' and 'any-place' elements
    roomList.style.transition = "opacity 0.35s";
    roomList.style.opacity = "0";
    setTimeout(() => { roomList.style.display = "none"; }, 350);

    anyPlace.style.transition = "opacity 0.35s";
    anyPlace.style.opacity = "0";
    setTimeout(() => { anyPlace.style.display = "none"; }, 350);

    // Update the 'assign' dropdown options
    assignSelect.innerHTML = `
        <option value="room">${wTrans('rooms.room')}</option>
        <option value="any">${wTrans('assets.anyPlace')}</option>
    `;
});

document.getElementById('assign')?.addEventListener('change', () => {
    const hotelValue = document.getElementById('hotel').value;
    const roomList = document.getElementById('room-list');
    const anyPlace = document.getElementById('any-place');
    const locationInput = document.getElementById('location');
    const roomSelect = document.getElementById('room');

    if (this.value === 'room') {
        // Call listRoomsByHotel fn with the hotel value
        listRoomsByHotel(hotelValue);

        // Fade out 'any-place' element
        anyPlace.style.transition = "opacity 0.35s";
        anyPlace.style.opacity = "0";
        setTimeout(() => { anyPlace.style.display = "none"; }, 350);

        // Remove 'required' attribute from 'location'
        locationInput.removeAttribute('required');
    } else {
        if (anyPlace.style.display === "none" || anyPlace.style.opacity === "0") {
            // Fade out 'room-list' element
            roomList.style.transition = "opacity 0.35s";
            roomList.style.opacity = "0";
            setTimeout(() => { roomList.style.display = "none"; }, 350);

            // Remove 'required' attribute from 'room'
            roomSelect.removeAttribute('required');

            // Fade in 'any-place' element
            anyPlace.style.display = "block";
            setTimeout(() => { anyPlace.style.opacity = "1"; }, 10); // Small delay to trigger transition

            // Add 'required' attribute to 'location'
            locationInput.setAttribute('required', 'required');
        }
    }
});

function confirmRedirect(e, url) {
    e.preventDefault()

    Swal.fire({
        title: wTrans('common.attention'),
        text: wTrans('common.confirmAction'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: wTrans('common.continue'),
        cancelButtonText: wTrans('common.cancel')
    }).then(result => {
        if (result.value) {
            window.location.href = url
        }
    })
}

function getRoomPriceByNumber(hotel, number) {
    axios.post('/rooms/price', {
        hotel: hotel,
        number: number
    })
    .then(response => {
        const result = response.data;

        document.getElementById('price').setAttribute('value', Math.round(parseInt(result.price)));
        document.getElementById('price').setAttribute('min', Math.round(parseInt(result.min_price)));
        document.getElementById('price').setAttribute('max', Math.round(parseInt(result.price)));

        document.getElementById('tax-value').textContent = (parseFloat(result.tax) * 100).toFixed(2);
    })
    .catch(() => {
        toast.error('Ha ocurrido un error');
    });
}


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

    const listElement = document.getElementById(params.list_id);
    const itemContainer = document.getElementById(params.item_container);

    if (query.length === 0) {
        listElement.style.display = 'none';
        itemContainer.innerHTML = '';
        return;
    }

    if (query.length >= 3) {
        axios.get(`${params.url}?query=${query}`)
            .then(response => {
                const data = JSON.parse(response.data.data);

                if (data.length) {
                    itemContainer.innerHTML = ''; // Clear previous results

                    data.forEach(item => {
                        itemContainer.insertAdjacentHTML('beforeend', params.render(item));
                    });

                    listElement.style.display = 'block';
                } else {
                    toast.info(wTrans('common.noRecords'));
                }
            })
            .catch(() => {
                toast.error(wTrans('common.error'))
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
export function generate_chart(id, datasets) {
    let ctx = document.getElementById(id)

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                wTrans('months.january'),
                wTrans('months.february'),
                wTrans('months.march'),
                wTrans('months.april'),
                wTrans('months.may'),
                wTrans('months.june'),
                wTrans('months.july'),
                wTrans('months.august'),
                wTrans('months.september'),
                wTrans('months.october'),
                wTrans('months.november'),
                wTrans('months.december')
            ],
            datasets: datasets
        }
    })
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
