<?php

declare(strict_types=1);

return [
    'title' => 'Reservations',
    'statuses' => ['draft' => 'Draft', 'confirmed' => 'Confirmed', 'checked_in' => 'Checked in', 'cancelled' => 'Cancelled', 'no_show' => 'No-show'],
    'actions' => ['create' => 'New reservation', 'view' => 'View reservation', 'edit' => 'Edit', 'confirm' => 'Confirm reservation', 'cancel' => 'Cancel reservation', 'no_show' => 'Mark no-show', 'check_in' => 'Check in', 'save_draft' => 'Save draft', 'continue' => 'Continue', 'back' => 'Back'],
    'fields' => ['check_in' => 'Planned arrival', 'check_out' => 'Planned departure', 'rate' => 'Nightly rate', 'nights' => 'Nights', 'guests' => 'Guests', 'rooms' => 'Rooms', 'quote' => 'Agreed quote'],
    'pages' => [
        'index' => ['description' => 'Future commitments, arrivals, and reservation history for :hotel.', 'search' => 'Search by guest or identification', 'all_statuses' => 'All statuses', 'empty_title' => 'No reservations yet', 'empty' => 'Create a draft, assign the party, and confirm only after the terms are agreed.'],
        'create' => ['heading' => 'Create reservation', 'description' => 'Prepare dates, party, concrete rooms, and quote before confirming the commitment.'],
        'edit' => ['heading' => 'Edit reservation', 'description' => 'Changes are recorded and availability is checked again.'],
        'show' => ['heading' => 'Reservation #:id', 'plan' => 'Reserved plan', 'quote_note' => 'This quote remains as historical context even if reference prices change.', 'reserved_plan' => 'Original reservation plan', 'current_stay' => 'Current stay', 'open_stay' => 'Open stay', 'rooms' => 'Rooms and guests', 'history' => 'Reservation history'],
    ],
    'form' => [
        'steps' => ['label' => 'Reservation steps', 'dates' => 'Dates', 'guests' => 'Party', 'rooms' => 'Rooms', 'review' => 'Review'],
        'dates' => ['title' => 'Define the interval', 'description' => 'The departure date does not consume a night and may match another arrival.'],
        'guests' => ['title' => 'Register the party', 'description' => 'Search existing guests before creating a new record.', 'responsible' => 'Responsible guest', 'companion' => 'Companion', 'unnamed' => 'Unnamed guest', 'new' => 'Register another', 'add' => 'Add companion'],
        'rooms' => ['title' => 'Reserve concrete rooms', 'description' => 'Only sellable rooms free for the entire interval are shown.', 'loading' => 'Checking availability...', 'empty' => 'No rooms are available for these dates.', 'number' => 'Room :number', 'capacity' => 'capacity :count', 'night' => 'night', 'assigned' => 'assigned', 'assign' => 'Assign every guest', 'choose' => 'Choose room'],
        'review' => ['title' => 'Review the commitment', 'description' => 'Confirm dates, assignments, and rates before saving.', 'draft_note' => 'The reservation will be saved as a draft and will not block inventory until confirmed.'],
        'summary' => ['title' => 'Reservation summary'],
        'errors' => ['dates' => 'Select a valid interval.', 'guests' => 'Complete every guest’s required information.', 'rooms' => 'Select rooms and assign every guest exactly once.'],
    ],
    'events' => ['created' => 'Reservation created', 'updated' => 'Reservation updated', 'confirmed' => 'Reservation confirmed', 'cancelled' => 'Reservation cancelled', 'no_show' => 'Marked as no-show', 'checked_in' => 'Check-in recorded'],
    'dialogs' => [
        'confirm' => ['title' => 'Confirm reservation', 'description' => 'Confirmation will block these rooms for the planned interval.', 'action' => 'Confirm'],
        'cancel' => ['title' => 'Cancel reservation', 'description' => 'The rooms will be released while history remains available.', 'action' => 'Cancel reservation'],
        'no-show' => ['title' => 'Mark no-show', 'description' => 'Record that the party did not arrive and release rooms without creating a stay.', 'action' => 'Mark no-show'],
        'check-in' => ['title' => 'Check in', 'description' => 'A stay will be created with the agreed party, rooms, and rates.', 'action' => 'Create stay'],
    ],
    'validation' => ['room_unavailable' => 'One or more rooms are no longer available for the selected interval.', 'room_unavailable_at_check_in' => 'One or more rooms are not clean or available for check-in.', 'room_capacity' => 'The assignment exceeds room capacity.', 'responsible_required' => 'Select a valid responsible guest.', 'unknown_guest' => 'The assignment contains an unknown guest.', 'assign_every_guest_once' => 'Every guest must be assigned to exactly one room.', 'duplicate_guest' => 'A guest with this identification already exists. Search and select that guest.', 'immutable' => 'This reservation can no longer be edited.', 'cannot_confirm' => 'Only a current draft can be confirmed.', 'cannot_cancel' => 'This reservation can no longer be cancelled.', 'cannot_mark_no_show' => 'Only a due confirmed reservation can be marked no-show.', 'cannot_check_in' => 'This reservation is not eligible for check-in or was already processed.'],
    'messages' => ['created' => 'Reservation saved as draft.', 'updated' => 'Reservation updated.', 'confirmed' => 'Reservation confirmed and rooms protected.', 'cancelled' => 'Reservation cancelled.', 'no_show' => 'Reservation marked as no-show.', 'checked_in' => 'Check-in created from reservation.'],
];
