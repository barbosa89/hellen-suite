export default {
    "en": {
        "app": {
            "actions": "Actions",
            "back": "Back",
            "cancel": "Cancel",
            "deleting": "Deleting…",
            "not_provided": "Not provided",
            "save": "Save",
            "saving": "Saving…",
            "searching": "Searching…",
            "remove": "Remove",
            "language": "Language",
            "theme": {
                "light": "Light mode",
                "dark": "Dark mode"
            },
            "languages": {
                "en": "English",
                "es": "Spanish"
            },
            "navigation": {
                "all_hotels": "All hotels",
                "overview": "Overview",
                "modules": "Modules",
                "hotel_navigation": "Hotel navigation",
                "open_menu": "Open menu",
                "close_menu": "Close menu",
                "collapse_menu": "Collapse menu",
                "expand_menu": "Expand menu"
            },
            "pagination": {
                "showing": "Showing",
                "to": "to",
                "of": "of",
                "pagination": "Pagination"
            },
            "upload": {
                "select": "Select a file",
                "drop_title": "Add a hotel image",
                "drop_hint": "PNG, JPG, or WebP. You can also drag the file here.",
                "files_selected": "One file selected|{count} files selected",
                "remove": "Remove {name}",
                "uploading": "Uploading image"
            }
        },
        "auth": {
            "failed": "These credentials do not match our records.",
            "password": "The provided password is incorrect.",
            "throttle": "Too many login attempts. Please try again in {seconds} seconds."
        },
        "guests": {
            "title": "Guests",
            "actions": {
                "create": "Register guest",
                "edit": "Edit guest",
                "view": "View profile"
            },
            "fields": {
                "first_name": {
                    "label": "First names"
                },
                "last_name": {
                    "label": "Last names"
                },
                "identification_type": {
                    "label": "Document type"
                },
                "identification_number": {
                    "label": "Document number"
                },
                "mobile": {
                    "label": "Mobile"
                },
                "email": {
                    "label": "Email"
                }
            },
            "pages": {
                "index": {
                    "heading": "Guests",
                    "description": "Review and update the administrative profiles for {hotel}.",
                    "search": "Search by name, document, or mobile",
                    "empty_title": "No guests have been registered yet",
                    "empty": "Guests created during check-in will appear here for administration."
                },
                "create": {
                    "heading": "Register guest",
                    "description": "Create an administrative profile for a future stay."
                },
                "edit": {
                    "heading": "Edit guest",
                    "description": "Correct this profile’s administrative details."
                },
                "show": {
                    "heading": "Guest profile",
                    "stays": "Stays",
                    "empty_stays": "This guest has no registered stays yet."
                }
            },
            "form": {
                "identity": {
                    "title": "Identification",
                    "description": "Identification prevents duplicate guests within this hotel."
                },
                "contact": {
                    "title": "Contact",
                    "description": "These optional details support guest service."
                },
                "select_identification_type": "Select a document type"
            },
            "messages": {
                "created": "Guest registered successfully.",
                "updated": "Guest profile updated successfully."
            }
        },
        "hotels": {
            "title": "Hotels",
            "directory": "Hotel directory",
            "selected_hotel": "Selected hotel",
            "actions": {
                "view": "View hotel",
                "edit": "Edit hotel",
                "delete": "Delete hotel",
                "create": "Create hotel",
                "manage": "Manage",
                "open": "Open hotel"
            },
            "fields": {
                "id": {
                    "label": "ID"
                },
                "business_name": {
                    "label": "Business name"
                },
                "tin": {
                    "label": "TIN"
                },
                "address": {
                    "label": "Address"
                },
                "phone": {
                    "label": "Phone"
                },
                "mobile": {
                    "label": "Mobile"
                },
                "email": {
                    "label": "Email"
                },
                "image": {
                    "label": "Image"
                }
            },
            "pages": {
                "index": {
                    "description": "Manage your properties and enter each hotel workspace.",
                    "directory_label": "Registered hotel directory",
                    "registered": "registered hotel|registered hotels",
                    "contact": "Contact",
                    "hotel_identifier": "Hotel #{id}",
                    "open_actions": "Open actions for {name}",
                    "no_hotels": "No hotels registered yet",
                    "no_hotels_hint": "Add your first hotel to keep the registry up to date.",
                    "delete_title": "Delete hotel",
                    "delete_confirm": "Are you sure you want to delete \"{name}\"?"
                },
                "create": {
                    "heading": "Create hotel",
                    "description": "Register the identity, location, and contact details of the new property."
                },
                "edit": {
                    "heading": "Edit hotel",
                    "description": "Update the information registered for {name}."
                },
                "show": {
                    "description": "Review the legal and contact information registered for this property.",
                    "no_image": "This hotel does not have an image yet."
                }
            },
            "form": {
                "identity": {
                    "title": "Hotel identity",
                    "description": "These details identify the property legally and commercially."
                },
                "contact": {
                    "title": "Location and contact",
                    "description": "Keep the channels used by the team for daily operations available."
                },
                "image": {
                    "title": "Property image",
                    "description": "Use a recognizable photo to find the hotel quickly in the directory."
                }
            },
            "management": {
                "heading": "Hotel overview",
                "description": "Information and available modules for {name}.",
                "contact_phone": "Contact phone",
                "profile_status": "Profile status",
                "profile_progress": "{completed} of {total} main details registered.",
                "complete_profile": "Complete information",
                "modules": "Hotel modules",
                "modules_description": "Open the tools that work exclusively with this property.",
                "open_module": "Open module"
            },
            "messages": {
                "created": "Hotel created successfully.",
                "updated": "Hotel updated successfully.",
                "deleted": "Hotel deleted successfully."
            }
        },
        "identification_types": {
            "cc": "Citizenship ID card",
            "ce": "Foreigner ID card",
            "ti": "Identity card",
            "rc": "Civil birth registration",
            "passport": "Passport",
            "national_id": "Foreign national ID"
        },
        "pagination": {
            "previous": "&laquo; Previous",
            "next": "Next &raquo;"
        },
        "passwords": {
            "reset": "Your password has been reset.",
            "sent": "We have emailed your password reset link.",
            "throttled": "Please wait before retrying.",
            "token": "This password reset token is invalid.",
            "user": "We can't find a user with that email address."
        },
        "reservations": {
            "title": "Reservations",
            "statuses": {
                "draft": "Draft",
                "confirmed": "Confirmed",
                "checked_in": "Checked in",
                "cancelled": "Cancelled",
                "no_show": "No-show"
            },
            "actions": {
                "create": "New reservation",
                "view": "View reservation",
                "edit": "Edit",
                "confirm": "Confirm reservation",
                "cancel": "Cancel reservation",
                "no_show": "Mark no-show",
                "check_in": "Check in",
                "save_draft": "Save draft",
                "continue": "Continue",
                "back": "Back"
            },
            "fields": {
                "check_in": "Planned arrival",
                "check_out": "Planned departure",
                "rate": "Nightly rate",
                "nights": "Nights",
                "guests": "Guests",
                "rooms": "Rooms",
                "quote": "Agreed quote"
            },
            "pages": {
                "index": {
                    "description": "Future commitments, arrivals, and reservation history for {hotel}.",
                    "search": "Search by guest or identification",
                    "all_statuses": "All statuses",
                    "empty_title": "No reservations yet",
                    "empty": "Create a draft, assign the party, and confirm only after the terms are agreed."
                },
                "create": {
                    "heading": "Create reservation",
                    "description": "Prepare dates, party, concrete rooms, and quote before confirming the commitment."
                },
                "edit": {
                    "heading": "Edit reservation",
                    "description": "Changes are recorded and availability is checked again."
                },
                "show": {
                    "heading": "Reservation #{id}",
                    "plan": "Reserved plan",
                    "quote_note": "This quote remains as historical context even if reference prices change.",
                    "reserved_plan": "Original reservation plan",
                    "current_stay": "Current stay",
                    "open_stay": "Open stay",
                    "rooms": "Rooms and guests",
                    "history": "Reservation history"
                }
            },
            "form": {
                "steps": {
                    "label": "Reservation steps",
                    "dates": "Dates",
                    "guests": "Party",
                    "rooms": "Rooms",
                    "review": "Review"
                },
                "dates": {
                    "title": "Define the interval",
                    "description": "The departure date does not consume a night and may match another arrival."
                },
                "guests": {
                    "title": "Register the party",
                    "description": "Search existing guests before creating a new record.",
                    "responsible": "Responsible guest",
                    "companion": "Companion",
                    "unnamed": "Unnamed guest",
                    "new": "Register another",
                    "add": "Add companion"
                },
                "rooms": {
                    "title": "Reserve concrete rooms",
                    "description": "Only sellable rooms free for the entire interval are shown.",
                    "loading": "Checking availability...",
                    "empty": "No rooms are available for these dates.",
                    "number": "Room {number}",
                    "capacity": "capacity {count}",
                    "night": "night",
                    "assigned": "assigned",
                    "assign": "Assign every guest",
                    "choose": "Choose room"
                },
                "review": {
                    "title": "Review the commitment",
                    "description": "Confirm dates, assignments, and rates before saving.",
                    "draft_note": "The reservation will be saved as a draft and will not block inventory until confirmed."
                },
                "summary": {
                    "title": "Reservation summary"
                },
                "errors": {
                    "dates": "Select a valid interval.",
                    "guests": "Complete every guest’s required information.",
                    "rooms": "Select rooms and assign every guest exactly once."
                }
            },
            "events": {
                "created": "Reservation created",
                "updated": "Reservation updated",
                "confirmed": "Reservation confirmed",
                "cancelled": "Reservation cancelled",
                "no_show": "Marked as no-show",
                "checked_in": "Check-in recorded"
            },
            "dialogs": {
                "confirm": {
                    "title": "Confirm reservation",
                    "description": "Confirmation will block these rooms for the planned interval.",
                    "action": "Confirm"
                },
                "cancel": {
                    "title": "Cancel reservation",
                    "description": "The rooms will be released while history remains available.",
                    "action": "Cancel reservation"
                },
                "no-show": {
                    "title": "Mark no-show",
                    "description": "Record that the party did not arrive and release rooms without creating a stay.",
                    "action": "Mark no-show"
                },
                "check-in": {
                    "title": "Check in",
                    "description": "A stay will be created with the agreed party, rooms, and rates.",
                    "action": "Create stay"
                }
            },
            "validation": {
                "room_unavailable": "One or more rooms are no longer available for the selected interval.",
                "room_unavailable_at_check_in": "One or more rooms are not clean or available for check-in.",
                "room_capacity": "The assignment exceeds room capacity.",
                "responsible_required": "Select a valid responsible guest.",
                "unknown_guest": "The assignment contains an unknown guest.",
                "assign_every_guest_once": "Every guest must be assigned to exactly one room.",
                "duplicate_guest": "A guest with this identification already exists. Search and select that guest.",
                "immutable": "This reservation can no longer be edited.",
                "cannot_confirm": "Only a current draft can be confirmed.",
                "cannot_cancel": "This reservation can no longer be cancelled.",
                "cannot_mark_no_show": "Only a due confirmed reservation can be marked no-show.",
                "cannot_check_in": "This reservation is not eligible for check-in or was already processed."
            },
            "messages": {
                "created": "Reservation saved as draft.",
                "updated": "Reservation updated.",
                "confirmed": "Reservation confirmed and rooms protected.",
                "cancelled": "Reservation cancelled.",
                "no_show": "Reservation marked as no-show.",
                "checked_in": "Check-in created from reservation."
            }
        },
        "room_types": {
            "title": "Room types",
            "capacity": "{count} guest|{count} guests",
            "rooms_count": "{count} room|{count} rooms",
            "actions": {
                "create": "Create type",
                "edit": "Edit type",
                "delete": "Delete type"
            },
            "fields": {
                "name": {
                    "label": "Name"
                },
                "capacity": {
                    "label": "Capacity"
                },
                "rooms_count": {
                    "label": "Linked rooms"
                }
            },
            "pages": {
                "index": {
                    "heading": "Room types for {hotel}",
                    "description": "Define the configurations that can be assigned to the inventory for {hotel}.",
                    "empty_title": "There are no room types yet",
                    "empty": "Create the first type to start registering physical rooms.",
                    "delete_title": "Delete room type",
                    "delete_confirm": "Are you sure you want to delete “{name}”?",
                    "delete_disabled": "This type cannot be deleted while it has linked rooms."
                },
                "create": {
                    "heading": "Create room type",
                    "description": "Define the name and capacity that distinguish this configuration."
                },
                "edit": {
                    "heading": "Edit {name}",
                    "description": "Update this configuration’s name or capacity."
                }
            },
            "form": {
                "title": "Type configuration",
                "description": "These details describe an inventory category, not a physical room."
            },
            "messages": {
                "capacity_blocked": "Capacity cannot be lower than an active or confirmed guest assignment.",
                "created": "Room type created successfully.",
                "updated": "Room type updated successfully.",
                "deleted": "Room type deleted successfully.",
                "delete_blocked": "A type with linked rooms cannot be deleted."
            }
        },
        "rooms": {
            "title": "Rooms",
            "module_navigation": "Inventory navigation",
            "actions": {
                "create": "Create room",
                "edit": "Edit room",
                "delete": "Delete room",
                "activate": "Activate room",
                "deactivate": "Deactivate room"
            },
            "fields": {
                "number": {
                    "label": "Number"
                },
                "room_type": {
                    "label": "Type"
                },
                "floor": {
                    "label": "Floor"
                },
                "reference_price": {
                    "label": "Reference rate"
                },
                "housekeeping_status": {
                    "label": "Housekeeping status"
                },
                "is_active": {
                    "label": "Active room"
                },
                "operation": {
                    "label": "Operation"
                }
            },
            "housekeeping": {
                "clean": "Clean",
                "dirty": "Needs cleaning"
            },
            "activity": {
                "active": "Active",
                "inactive": "Inactive"
            },
            "pages": {
                "index": {
                    "heading": "Rooms for {hotel}",
                    "description": "Review and update the operational inventory for {hotel}.",
                    "inventory_label": "Room inventory",
                    "empty_title": "No rooms have been registered yet",
                    "empty": "Create the room types used by this property, then register each room.",
                    "currency_required_title": "Set the currency before registering rates",
                    "currency_required": "The reference rate needs a global currency. Set it now and return to the inventory.",
                    "delete_title": "Delete room",
                    "delete_confirm": "Are you sure you want to delete room {number}?",
                    "open_actions": "Open actions for room {number}"
                },
                "create": {
                    "heading": "Create room",
                    "description": "Register its type, location, rate, and initial operating status."
                },
                "edit": {
                    "heading": "Edit room {number}",
                    "description": "Update this room’s administrative and operational details."
                }
            },
            "form": {
                "assignment": {
                    "title": "Identification and location",
                    "description": "Associate the room with the type that matches its physical configuration."
                },
                "operation": {
                    "title": "Rate and operation",
                    "description": "The rate is for reference, and housekeeping can be updated directly from inventory."
                },
                "select_type": "Select a room type",
                "reference_price_hint": "Stores up to two decimals in the configured currency.",
                "activity_hint": "Inactive rooms remain in the inventory."
            },
            "messages": {
                "created": "Room created successfully.",
                "updated": "Room updated successfully.",
                "deleted": "Room deleted successfully.",
                "activity_updated": "Room activity was updated.",
                "housekeeping_updated": "Housekeeping status was updated.",
                "delete_blocked": "This room has stay or reservation history and cannot be deleted.",
                "inventory_blocked": "This room has an active stay or confirmed reservation and cannot be deactivated."
            }
        },
        "settings": {
            "title": "Settings",
            "actions": {
                "configure_currency": "Configure currency"
            },
            "pages": {
                "edit": {
                    "heading": "Application settings",
                    "description": "Define the global preferences used by Hellen Suite."
                }
            },
            "currency": {
                "title": "Reference currency",
                "description": "Select the currency that identifies reference rates for every room.",
                "label": "Currency",
                "placeholder": "Select a currency",
                "no_results": "No currencies match your search.",
                "hint": "All ISO 4217 codes are available and rates are stored with two decimals.",
                "reference_title": "Reference use",
                "reference_description": "Changing currency does not convert or alter existing rates. The selected code only indicates how they are interpreted and displayed."
            },
            "messages": {
                "updated": "Currency settings were updated.",
                "configuration_required": "Complete the required settings to continue. Pending fields are marked below."
            }
        },
        "stays": {
            "title": "Stays",
            "actions": {
                "create": "New check-in",
                "check_in": "Confirm check-in",
                "check_out": "Check out",
                "transfer": "Transfer room",
                "extend": "Change expected checkout",
                "add_guest": "Add guest",
                "view": "View stay"
            },
            "fields": {
                "expected_check_out_on": {
                    "label": "Expected checkout"
                },
                "nightly_rate": {
                    "label": "Nightly rate"
                },
                "room": {
                    "label": "Room"
                },
                "guests": {
                    "label": "Guests"
                }
            },
            "pages": {
                "index": {
                    "heading": "Stays",
                    "description": "Manage arrivals, in-house guests, and departures for {hotel}.",
                    "empty_title": "No stays have been registered",
                    "empty": "Start a check-in to register the first stay."
                },
                "create": {
                    "heading": "New check-in",
                    "description": "Register the group, assign available rooms, and confirm the stay."
                },
                "show": {
                    "heading": "Stay",
                    "occupancies": "Assigned rooms",
                    "group": "Registered group",
                    "active": "Active",
                    "checked_out": "Checked out",
                    "costs": {
                        "heading": "Stay costs",
                        "estimate_description": "Projected lodging charges using the expected checkout date.",
                        "final_description": "Final lodging charges from the actual occupied nights.",
                        "estimated_total": "Estimated lodging total",
                        "total": "Lodging total",
                        "billable_nights": "Billable nights",
                        "currency": "Currency",
                        "room_period": "Room and period",
                        "subtotal": "Subtotal",
                        "per_night": "per night",
                        "nights_count": "{count} night|{count} nights"
                    },
                    "add_guest_description": "Find or register a companion and assign them to a room with available capacity.",
                    "no_room_capacity": "There is no capacity available to add another guest."
                }
            },
            "form": {
                "steps": {
                    "label": "Check-in progress",
                    "guests": "Guests",
                    "rooms": "Rooms",
                    "review": "Confirmation"
                },
                "actions": {
                    "back": "Back",
                    "continue": "Continue"
                },
                "stay": {
                    "check_in_today": "Check-in: today",
                    "check_in_hint": "Check-in time is recorded when the stay is confirmed."
                },
                "guests": {
                    "title": "Guest group",
                    "description": "Find an existing profile or register every person without leaving check-in.",
                    "responsible": "Stay responsible",
                    "companion": "Companion",
                    "add_companion": "Add companion",
                    "new_guest": "Register new guest",
                    "search": "Find existing guest"
                },
                "rooms": {
                    "title": "Select rooms",
                    "description": "Choose clean, available rooms and explicitly assign every guest.",
                    "available_title": "Available rooms",
                    "available_description": "Select one or more rooms for this stay.",
                    "available_count": "{count} available",
                    "room_number": "Room {number}",
                    "capacity": "{count} guests",
                    "night": "night",
                    "empty": "There are no clean, available rooms at this time.",
                    "selected_title": "Selected rooms",
                    "selected_description": "Capture the rate now to preserve this stay’s historical value.",
                    "assigned": "assigned",
                    "remove_room": "Remove room {number}",
                    "assign_title": "Assign every guest",
                    "assign_description": "Each person must occupy exactly one room.",
                    "assign_label": "Assigned room",
                    "assign_placeholder": "Select a room"
                },
                "review": {
                    "title": "Final review",
                    "description": "Confirm the group and rooms before recording the check-in.",
                    "guests": "Guests",
                    "rooms": "Rooms"
                },
                "summary": {
                    "title": "Stay summary",
                    "guests": "Guests",
                    "rooms": "Rooms",
                    "no_rooms": "No rooms have been selected yet."
                },
                "messages": {
                    "complete_guests": "Complete each guest’s required information before continuing.",
                    "select_check_out": "Set the expected checkout date.",
                    "select_room": "Select at least one available room.",
                    "assign_guests": "Assign every guest to a room without exceeding capacity."
                }
            },
            "messages": {
                "checked_in": "Check-in registered successfully.",
                "checked_out": "The stay was closed and rooms now need cleaning.",
                "expected_check_out_updated": "Expected checkout was updated.",
                "room_transferred": "Room transferred successfully.",
                "guest_added": "Guest added to the stay successfully."
            },
            "validation": {
                "responsible_required": "Select a valid responsible guest.",
                "unknown_guest": "The assignment includes a guest not in the group.",
                "assign_every_guest_once": "Every guest must be assigned to exactly one room.",
                "duplicate_guest": "A guest with this document already exists; find and select them.",
                "room_unavailable": "The room is no longer available for this stay.",
                "room_capacity": "The assignment exceeds the room capacity.",
                "stay_closed": "The stay is already closed.",
                "occupancy_closed": "The selected occupancy is already closed.",
                "occupancy_invalid": "The selected room is not assigned to this stay.",
                "guest_already_in_stay": "This guest is already registered in the stay."
            }
        },
        "validation": {
            "accepted": "The {attribute} field must be accepted.",
            "accepted_if": "The {attribute} field must be accepted when {other} is {value}.",
            "active_url": "The {attribute} field must be a valid URL.",
            "after": "The {attribute} field must be a date after {date}.",
            "after_or_equal": "The {attribute} field must be a date after or equal to {date}.",
            "alpha": "The {attribute} field must only contain letters.",
            "alpha_dash": "The {attribute} field must only contain letters, numbers, dashes, and underscores.",
            "alpha_num": "The {attribute} field must only contain letters and numbers.",
            "any_of": "The {attribute} field is invalid.",
            "array": "The {attribute} field must be an array.",
            "array_keys": "The {attribute} field must only contain the following keys: {values}.",
            "ascii": "The {attribute} field must only contain single-byte alphanumeric characters and symbols.",
            "base64": "The {attribute} field must be a valid Base64 string.",
            "before": "The {attribute} field must be a date before {date}.",
            "before_or_equal": "The {attribute} field must be a date before or equal to {date}.",
            "between": {
                "array": "The {attribute} field must have between {min} and {max} items.",
                "file": "The {attribute} field must be between {min} and {max} kilobytes.",
                "numeric": "The {attribute} field must be between {min} and {max}.",
                "string": "The {attribute} field must be between {min} and {max} characters."
            },
            "boolean": "The {attribute} field must be true or false.",
            "can": "The {attribute} field contains an unauthorized value.",
            "confirmed": "The {attribute} field confirmation does not match.",
            "contains": "The {attribute} field is missing a required value.",
            "current_password": "The password is incorrect.",
            "date": "The {attribute} field must be a valid date.",
            "date_equals": "The {attribute} field must be a date equal to {date}.",
            "date_format": "The {attribute} field must match the format {format}.",
            "decimal": "The {attribute} field must have {decimal} decimal places.",
            "declined": "The {attribute} field must be declined.",
            "declined_if": "The {attribute} field must be declined when {other} is {value}.",
            "different": "The {attribute} field and {other} must be different.",
            "digits": "The {attribute} field must be {digits} digits.",
            "digits_between": "The {attribute} field must be between {min} and {max} digits.",
            "dimensions": "The {attribute} field has invalid image dimensions.",
            "distinct": "The {attribute} field has a duplicate value.",
            "doesnt_contain": "The {attribute} field must not contain any of the following: {values}.",
            "doesnt_end_with": "The {attribute} field must not end with one of the following: {values}.",
            "doesnt_start_with": "The {attribute} field must not start with one of the following: {values}.",
            "email": "The {attribute} field must be a valid email address.",
            "encoding": "The {attribute} field must be encoded in {encoding}.",
            "ends_with": "The {attribute} field must end with one of the following: {values}.",
            "enum": "The selected {attribute} is invalid.",
            "exists": "The selected {attribute} is invalid.",
            "extensions": "The {attribute} field must have one of the following extensions: {values}.",
            "file": "The {attribute} field must be a file.",
            "filled": "The {attribute} field must have a value.",
            "gt": {
                "array": "The {attribute} field must have more than {value} items.",
                "file": "The {attribute} field must be greater than {value} kilobytes.",
                "numeric": "The {attribute} field must be greater than {value}.",
                "string": "The {attribute} field must be greater than {value} characters."
            },
            "gte": {
                "array": "The {attribute} field must have {value} items or more.",
                "file": "The {attribute} field must be greater than or equal to {value} kilobytes.",
                "numeric": "The {attribute} field must be greater than or equal to {value}.",
                "string": "The {attribute} field must be greater than or equal to {value} characters."
            },
            "hex_color": "The {attribute} field must be a valid hexadecimal color.",
            "image": "The {attribute} field must be an image.",
            "in": "The selected {attribute} is invalid.",
            "in_array": "The {attribute} field must exist in {other}.",
            "in_array_keys": "The {attribute} field must contain at least one of the following keys: {values}.",
            "integer": "The {attribute} field must be an integer.",
            "ip": "The {attribute} field must be a valid IP address.",
            "ipv4": "The {attribute} field must be a valid IPv4 address.",
            "ipv6": "The {attribute} field must be a valid IPv6 address.",
            "json": "The {attribute} field must be a valid JSON string.",
            "list": "The {attribute} field must be a list.",
            "lowercase": "The {attribute} field must be lowercase.",
            "lt": {
                "array": "The {attribute} field must have less than {value} items.",
                "file": "The {attribute} field must be less than {value} kilobytes.",
                "numeric": "The {attribute} field must be less than {value}.",
                "string": "The {attribute} field must be less than {value} characters."
            },
            "lte": {
                "array": "The {attribute} field must not have more than {value} items.",
                "file": "The {attribute} field must be less than or equal to {value} kilobytes.",
                "numeric": "The {attribute} field must be less than or equal to {value}.",
                "string": "The {attribute} field must be less than or equal to {value} characters."
            },
            "mac_address": "The {attribute} field must be a valid MAC address.",
            "max": {
                "array": "The {attribute} field must not have more than {max} items.",
                "file": "The {attribute} field must not be greater than {max} kilobytes.",
                "numeric": "The {attribute} field must not be greater than {max}.",
                "string": "The {attribute} field must not be greater than {max} characters."
            },
            "max_digits": "The {attribute} field must not have more than {max} digits.",
            "mimes": "The {attribute} field must be a file of type: {values}.",
            "mimetypes": "The {attribute} field must be a file of type: {values}.",
            "min": {
                "array": "The {attribute} field must have at least {min} items.",
                "file": "The {attribute} field must be at least {min} kilobytes.",
                "numeric": "The {attribute} field must be at least {min}.",
                "string": "The {attribute} field must be at least {min} characters."
            },
            "min_digits": "The {attribute} field must have at least {min} digits.",
            "missing": "The {attribute} field must be missing.",
            "missing_if": "The {attribute} field must be missing when {other} is {value}.",
            "missing_unless": "The {attribute} field must be missing unless {other} is {value}.",
            "missing_with": "The {attribute} field must be missing when {values} is present.",
            "missing_with_all": "The {attribute} field must be missing when {values} are present.",
            "multiple_of": "The {attribute} field must be a multiple of {value}.",
            "not_in": "The selected {attribute} is invalid.",
            "not_regex": "The {attribute} field format is invalid.",
            "numeric": "The {attribute} field must be a number.",
            "password": {
                "letters": "The {attribute} field must contain at least one letter.",
                "mixed": "The {attribute} field must contain at least one uppercase and one lowercase letter.",
                "numbers": "The {attribute} field must contain at least one number.",
                "symbols": "The {attribute} field must contain at least one symbol.",
                "uncompromised": "The given {attribute} has appeared in a data leak. Please choose a different {attribute}."
            },
            "present": "The {attribute} field must be present.",
            "present_if": "The {attribute} field must be present when {other} is {value}.",
            "present_unless": "The {attribute} field must be present unless {other} is {value}.",
            "present_with": "The {attribute} field must be present when {values} is present.",
            "present_with_all": "The {attribute} field must be present when {values} are present.",
            "prohibited": "The {attribute} field is prohibited.",
            "prohibited_if": "The {attribute} field is prohibited when {other} is {value}.",
            "prohibited_if_accepted": "The {attribute} field is prohibited when {other} is accepted.",
            "prohibited_if_declined": "The {attribute} field is prohibited when {other} is declined.",
            "prohibited_unless": "The {attribute} field is prohibited unless {other} is in {values}.",
            "prohibits": "The {attribute} field prohibits {other} from being present.",
            "regex": "The {attribute} field format is invalid.",
            "required": "The {attribute} field is required.",
            "required_array_keys": "The {attribute} field must contain entries for: {values}.",
            "required_if": "The {attribute} field is required when {other} is {value}.",
            "required_if_accepted": "The {attribute} field is required when {other} is accepted.",
            "required_if_declined": "The {attribute} field is required when {other} is declined.",
            "required_unless": "The {attribute} field is required unless {other} is in {values}.",
            "required_with": "The {attribute} field is required when {values} is present.",
            "required_with_all": "The {attribute} field is required when {values} are present.",
            "required_without": "The {attribute} field is required when {values} is not present.",
            "required_without_all": "The {attribute} field is required when none of {values} are present.",
            "same": "The {attribute} field must match {other}.",
            "size": {
                "array": "The {attribute} field must contain {size} items.",
                "file": "The {attribute} field must be {size} kilobytes.",
                "numeric": "The {attribute} field must be {size}.",
                "string": "The {attribute} field must be {size} characters."
            },
            "starts_with": "The {attribute} field must start with one of the following: {values}.",
            "string": "The {attribute} field must be a string.",
            "timezone": "The {attribute} field must be a valid timezone.",
            "unique": "The {attribute} has already been taken.",
            "uploaded": "The {attribute} failed to upload.",
            "uppercase": "The {attribute} field must be uppercase.",
            "url": "The {attribute} field must be a valid URL.",
            "ulid": "The {attribute} field must be a valid ULID.",
            "uuid": "The {attribute} field must be a valid UUID.",
            "custom": {
                "attribute-name": {
                    "rule-name": "custom-message"
                }
            },
            "attributes": []
        }
    },
    "es": {
        "app": {
            "actions": "Acciones",
            "back": "Atrás",
            "cancel": "Cancelar",
            "deleting": "Eliminando…",
            "not_provided": "Sin registrar",
            "save": "Guardar",
            "saving": "Guardando…",
            "searching": "Buscando…",
            "remove": "Quitar",
            "language": "Idioma",
            "theme": {
                "light": "Modo claro",
                "dark": "Modo oscuro"
            },
            "languages": {
                "en": "Inglés",
                "es": "Español"
            },
            "navigation": {
                "all_hotels": "Todos los hoteles",
                "overview": "Resumen",
                "modules": "Módulos",
                "hotel_navigation": "Navegación del hotel",
                "open_menu": "Abrir menú",
                "close_menu": "Cerrar menú",
                "collapse_menu": "Contraer menú",
                "expand_menu": "Expandir menú"
            },
            "pagination": {
                "showing": "Mostrando",
                "to": "a",
                "of": "de",
                "pagination": "Paginación"
            },
            "upload": {
                "select": "Seleccionar un archivo",
                "drop_title": "Agrega una imagen del hotel",
                "drop_hint": "PNG, JPG o WebP. También puedes arrastrar el archivo aquí.",
                "files_selected": "Un archivo seleccionado|{count} archivos seleccionados",
                "remove": "Quitar {name}",
                "uploading": "Subiendo imagen"
            }
        },
        "auth": {
            "failed": "Estas credenciales no coinciden con nuestros registros.",
            "password": "La contraseña proporcionada es incorrecta.",
            "throttle": "Demasiados intentos de acceso. Por favor, intente nuevamente en {seconds} segundos."
        },
        "guests": {
            "title": "Huéspedes",
            "actions": {
                "create": "Registrar huésped",
                "edit": "Editar huésped",
                "view": "Ver ficha"
            },
            "fields": {
                "first_name": {
                    "label": "Nombres"
                },
                "last_name": {
                    "label": "Apellidos"
                },
                "identification_type": {
                    "label": "Tipo de documento"
                },
                "identification_number": {
                    "label": "Número de documento"
                },
                "mobile": {
                    "label": "Celular"
                },
                "email": {
                    "label": "Correo electrónico"
                }
            },
            "pages": {
                "index": {
                    "heading": "Huéspedes",
                    "description": "Consulta y actualiza las fichas administrativas de {hotel}.",
                    "search": "Buscar por nombre, documento o celular",
                    "empty_title": "Aún no hay huéspedes registrados",
                    "empty": "Los huéspedes creados durante un check-in aparecerán aquí para su administración."
                },
                "create": {
                    "heading": "Registrar huésped",
                    "description": "Crea una ficha administrativa para una futura estancia."
                },
                "edit": {
                    "heading": "Editar huésped",
                    "description": "Corrige los datos administrativos de esta ficha."
                },
                "show": {
                    "heading": "Ficha de huésped",
                    "stays": "Estancias",
                    "empty_stays": "Este huésped aún no tiene estancias registradas."
                }
            },
            "form": {
                "identity": {
                    "title": "Identificación",
                    "description": "La identificación evita duplicados en este hotel."
                },
                "contact": {
                    "title": "Contacto",
                    "description": "Estos datos son opcionales y se usan para la atención del huésped."
                },
                "select_identification_type": "Selecciona un tipo de documento"
            },
            "messages": {
                "created": "Huésped registrado con éxito.",
                "updated": "Ficha de huésped actualizada con éxito."
            }
        },
        "hotels": {
            "title": "Hoteles",
            "directory": "Directorio de hoteles",
            "selected_hotel": "Hotel seleccionado",
            "actions": {
                "view": "Ver hotel",
                "edit": "Editar hotel",
                "delete": "Eliminar hotel",
                "create": "Crear hotel",
                "manage": "Administrar",
                "open": "Abrir hotel"
            },
            "fields": {
                "id": {
                    "label": "ID"
                },
                "business_name": {
                    "label": "Nombre comercial"
                },
                "tin": {
                    "label": "NIT"
                },
                "address": {
                    "label": "Dirección"
                },
                "phone": {
                    "label": "Teléfono"
                },
                "mobile": {
                    "label": "Móvil"
                },
                "email": {
                    "label": "Correo electrónico"
                },
                "image": {
                    "label": "Imagen"
                }
            },
            "pages": {
                "index": {
                    "description": "Administra tus propiedades y entra al espacio de trabajo de cada hotel.",
                    "directory_label": "Directorio de hoteles registrados",
                    "registered": "hotel registrado|hoteles registrados",
                    "contact": "Contacto",
                    "hotel_identifier": "Hotel #{id}",
                    "open_actions": "Abrir acciones para {name}",
                    "no_hotels": "No hay hoteles registrados aún",
                    "no_hotels_hint": "Agrega tu primer hotel para mantener el registro actualizado.",
                    "delete_title": "Eliminar hotel",
                    "delete_confirm": "¿Estás seguro de que deseas eliminar \"{name}\"?"
                },
                "create": {
                    "heading": "Crear hotel",
                    "description": "Registra la identidad, ubicación y datos de contacto de la nueva propiedad."
                },
                "edit": {
                    "heading": "Editar hotel",
                    "description": "Actualiza la información registrada para {name}."
                },
                "show": {
                    "description": "Consulta la información legal y de contacto registrada para esta propiedad.",
                    "no_image": "Este hotel aún no tiene una imagen registrada."
                }
            },
            "form": {
                "identity": {
                    "title": "Identidad del hotel",
                    "description": "Estos datos identifican legal y comercialmente la propiedad."
                },
                "contact": {
                    "title": "Ubicación y contacto",
                    "description": "Mantén disponibles los canales que utiliza el equipo para atender la operación."
                },
                "image": {
                    "title": "Imagen de la propiedad",
                    "description": "Usa una fotografía reconocible para encontrar el hotel rápidamente en el directorio."
                }
            },
            "management": {
                "heading": "Resumen del hotel",
                "description": "Información y módulos disponibles para {name}.",
                "contact_phone": "Teléfono de contacto",
                "profile_status": "Estado del perfil",
                "profile_progress": "{completed} de {total} datos principales registrados.",
                "complete_profile": "Completar información",
                "modules": "Módulos del hotel",
                "modules_description": "Entra a las herramientas que trabajan exclusivamente con esta propiedad.",
                "open_module": "Abrir módulo"
            },
            "messages": {
                "created": "Hotel creado con éxito.",
                "updated": "Hotel actualizado con éxito.",
                "deleted": "Hotel eliminado con éxito."
            }
        },
        "identification_types": {
            "cc": "Cédula de ciudadanía",
            "ce": "Cédula de extranjería",
            "ti": "Tarjeta de identidad",
            "rc": "Registro civil de nacimiento",
            "passport": "Pasaporte",
            "national_id": "Documento nacional de identidad extranjero"
        },
        "pagination": {
            "previous": "&laquo; Anterior",
            "next": "Siguiente &raquo;"
        },
        "passwords": {
            "reset": "¡Su contraseña ha sido restablecida!",
            "sent": "¡Le hemos enviado por correo electrónico el enlace para restablecer su contraseña!",
            "throttled": "Por favor espere antes de intentar de nuevo.",
            "token": "El token de restablecimiento de contraseña es inválido.",
            "user": "No encontramos ningún usuario con ese correo electrónico."
        },
        "reservations": {
            "title": "Reservas",
            "statuses": {
                "draft": "Borrador",
                "confirmed": "Confirmada",
                "checked_in": "Ingresada",
                "cancelled": "Cancelada",
                "no_show": "No-show"
            },
            "actions": {
                "create": "Nueva reserva",
                "view": "Ver reserva",
                "edit": "Modificar",
                "confirm": "Confirmar reserva",
                "cancel": "Cancelar reserva",
                "no_show": "Marcar no-show",
                "check_in": "Hacer check-in",
                "save_draft": "Guardar borrador",
                "continue": "Continuar",
                "back": "Atrás"
            },
            "fields": {
                "check_in": "Llegada planeada",
                "check_out": "Salida planeada",
                "rate": "Tarifa por noche",
                "nights": "Noches",
                "guests": "Huéspedes",
                "rooms": "Habitaciones",
                "quote": "Cotización acordada"
            },
            "pages": {
                "index": {
                    "description": "Compromisos futuros, llegadas y reservas históricas de {hotel}.",
                    "search": "Buscar por huésped o identificación",
                    "all_statuses": "Todos los estados",
                    "empty_title": "Todavía no hay reservas",
                    "empty": "Crea un borrador, asigna el grupo y confirma sólo cuando las condiciones estén acordadas."
                },
                "create": {
                    "heading": "Crear reserva",
                    "description": "Prepara fechas, grupo, habitaciones concretas y cotización antes de confirmar el compromiso."
                },
                "edit": {
                    "heading": "Modificar reserva",
                    "description": "Los cambios se registran y vuelven a comprobar la disponibilidad completa."
                },
                "show": {
                    "heading": "Reserva #{id}",
                    "plan": "Plan reservado",
                    "quote_note": "Esta cotización permanece como contexto histórico aunque cambie el precio de referencia.",
                    "reserved_plan": "Plan original de la reserva",
                    "current_stay": "Estancia vigente",
                    "open_stay": "Abrir estancia",
                    "rooms": "Habitaciones y huéspedes",
                    "history": "Historial de reserva"
                }
            },
            "form": {
                "steps": {
                    "label": "Pasos de la reserva",
                    "dates": "Fechas",
                    "guests": "Grupo",
                    "rooms": "Habitaciones",
                    "review": "Revisión"
                },
                "dates": {
                    "title": "Define el intervalo",
                    "description": "La salida no consume noche y puede coincidir con la llegada de otro grupo."
                },
                "guests": {
                    "title": "Registra el grupo",
                    "description": "Busca huéspedes existentes antes de crear un registro nuevo.",
                    "responsible": "Huésped responsable",
                    "companion": "Acompañante",
                    "unnamed": "Huésped sin nombre",
                    "new": "Registrar otro",
                    "add": "Agregar acompañante"
                },
                "rooms": {
                    "title": "Reserva habitaciones concretas",
                    "description": "Sólo se muestran habitaciones vendibles y libres durante todo el intervalo.",
                    "loading": "Comprobando disponibilidad...",
                    "empty": "No hay habitaciones disponibles para estas fechas.",
                    "number": "Habitación {number}",
                    "capacity": "capacidad {count}",
                    "night": "noche",
                    "assigned": "asignados",
                    "assign": "Distribuye cada huésped",
                    "choose": "Elegir habitación"
                },
                "review": {
                    "title": "Revisa el compromiso",
                    "description": "Confirma fechas, distribución y tarifa antes de guardar.",
                    "draft_note": "La reserva se guardará como borrador y no bloqueará inventario hasta que la confirmes."
                },
                "summary": {
                    "title": "Resumen de reserva"
                },
                "errors": {
                    "dates": "Selecciona un intervalo válido.",
                    "guests": "Completa los datos obligatorios de cada huésped.",
                    "rooms": "Selecciona habitaciones y asigna cada huésped exactamente una vez."
                }
            },
            "events": {
                "created": "Reserva creada",
                "updated": "Reserva modificada",
                "confirmed": "Reserva confirmada",
                "cancelled": "Reserva cancelada",
                "no_show": "Marcada como no-show",
                "checked_in": "Check-in registrado"
            },
            "dialogs": {
                "confirm": {
                    "title": "Confirmar reserva",
                    "description": "La confirmación bloqueará las habitaciones durante el intervalo planeado.",
                    "action": "Confirmar"
                },
                "cancel": {
                    "title": "Cancelar reserva",
                    "description": "Las habitaciones dejarán de estar comprometidas. El historial se conservará.",
                    "action": "Cancelar reserva"
                },
                "no-show": {
                    "title": "Marcar no-show",
                    "description": "Registra que el grupo no llegó y libera las habitaciones sin crear una estancia.",
                    "action": "Marcar no-show"
                },
                "check-in": {
                    "title": "Hacer check-in",
                    "description": "Se creará una estancia con el grupo, las habitaciones y las tarifas acordadas.",
                    "action": "Crear estancia"
                }
            },
            "validation": {
                "room_unavailable": "Una o más habitaciones ya no están disponibles para el intervalo seleccionado.",
                "room_unavailable_at_check_in": "Una o más habitaciones no están limpias o disponibles para hacer check-in.",
                "room_capacity": "La asignación supera la capacidad de la habitación.",
                "responsible_required": "Selecciona un huésped responsable válido.",
                "unknown_guest": "La asignación contiene un huésped desconocido.",
                "assign_every_guest_once": "Cada huésped debe estar asignado exactamente a una habitación.",
                "duplicate_guest": "Ya existe un huésped con esta identificación. Búscalo y selecciónalo.",
                "immutable": "Esta reserva ya no se puede modificar.",
                "cannot_confirm": "Sólo un borrador con llegada vigente puede confirmarse.",
                "cannot_cancel": "Esta reserva ya no se puede cancelar.",
                "cannot_mark_no_show": "Sólo una reserva confirmada cuya llegada ya venció puede marcarse no-show.",
                "cannot_check_in": "La reserva todavía no admite check-in o ya fue procesada."
            },
            "messages": {
                "created": "Reserva guardada como borrador.",
                "updated": "Reserva actualizada.",
                "confirmed": "Reserva confirmada; las habitaciones quedaron protegidas.",
                "cancelled": "Reserva cancelada.",
                "no_show": "Reserva marcada como no-show.",
                "checked_in": "Check-in creado desde la reserva."
            }
        },
        "room_types": {
            "title": "Tipos de habitación",
            "capacity": "{count} huésped|{count} huéspedes",
            "rooms_count": "{count} habitación|{count} habitaciones",
            "actions": {
                "create": "Crear tipo",
                "edit": "Editar tipo",
                "delete": "Eliminar tipo"
            },
            "fields": {
                "name": {
                    "label": "Nombre"
                },
                "capacity": {
                    "label": "Capacidad"
                },
                "rooms_count": {
                    "label": "Habitaciones asociadas"
                }
            },
            "pages": {
                "index": {
                    "heading": "Tipos de habitación de {hotel}",
                    "description": "Define las configuraciones que se podrán asignar al inventario de {hotel}.",
                    "empty_title": "Aún no hay tipos de habitación",
                    "empty": "Crea el primer tipo para empezar a registrar las habitaciones físicas.",
                    "delete_title": "Eliminar tipo de habitación",
                    "delete_confirm": "¿Estás seguro de que deseas eliminar “{name}”?",
                    "delete_disabled": "No puedes eliminar este tipo mientras tenga habitaciones asociadas."
                },
                "create": {
                    "heading": "Crear tipo de habitación",
                    "description": "Define el nombre y la capacidad que distinguirán esta configuración."
                },
                "edit": {
                    "heading": "Editar {name}",
                    "description": "Actualiza el nombre o capacidad de esta configuración."
                }
            },
            "form": {
                "title": "Configuración del tipo",
                "description": "Estos datos describen una categoría del inventario, no una habitación física."
            },
            "messages": {
                "capacity_blocked": "La capacidad no puede ser menor que una asignación activa o confirmada.",
                "created": "Tipo de habitación creado con éxito.",
                "updated": "Tipo de habitación actualizado con éxito.",
                "deleted": "Tipo de habitación eliminado con éxito.",
                "delete_blocked": "No puedes eliminar un tipo que todavía tiene habitaciones asociadas."
            }
        },
        "rooms": {
            "title": "Habitaciones",
            "module_navigation": "Navegación del inventario",
            "actions": {
                "create": "Crear habitación",
                "edit": "Editar habitación",
                "delete": "Eliminar habitación",
                "activate": "Activar habitación",
                "deactivate": "Desactivar habitación"
            },
            "fields": {
                "number": {
                    "label": "Número"
                },
                "room_type": {
                    "label": "Tipo"
                },
                "floor": {
                    "label": "Piso"
                },
                "reference_price": {
                    "label": "Tarifa de referencia"
                },
                "housekeeping_status": {
                    "label": "Estado de limpieza"
                },
                "is_active": {
                    "label": "Habitación activa"
                },
                "operation": {
                    "label": "Operación"
                }
            },
            "housekeeping": {
                "clean": "Limpia",
                "dirty": "Pendiente de limpieza"
            },
            "activity": {
                "active": "Activa",
                "inactive": "Inactiva"
            },
            "pages": {
                "index": {
                    "heading": "Habitaciones de {hotel}",
                    "description": "Consulta y actualiza el inventario operativo de {hotel}.",
                    "inventory_label": "Inventario de habitaciones",
                    "empty_title": "Aún no hay habitaciones registradas",
                    "empty": "Primero crea los tipos que utilizará la propiedad y después registra cada habitación.",
                    "currency_required_title": "Configura la moneda antes de registrar tarifas",
                    "currency_required": "La tarifa de referencia necesita una moneda global. Puedes configurarla ahora y regresar al inventario.",
                    "delete_title": "Eliminar habitación",
                    "delete_confirm": "¿Estás seguro de que deseas eliminar la habitación {number}?",
                    "open_actions": "Abrir acciones de la habitación {number}"
                },
                "create": {
                    "heading": "Crear habitación",
                    "description": "Registra su tipo, ubicación, tarifa y estado operativo inicial."
                },
                "edit": {
                    "heading": "Editar habitación {number}",
                    "description": "Actualiza los datos administrativos y operativos de esta habitación."
                }
            },
            "form": {
                "assignment": {
                    "title": "Identificación y ubicación",
                    "description": "Relaciona la habitación con el tipo que corresponde a su configuración física."
                },
                "operation": {
                    "title": "Tarifa y operación",
                    "description": "La tarifa es referencial y el estado de limpieza puede actualizarse rápidamente desde el inventario."
                },
                "select_type": "Selecciona un tipo de habitación",
                "reference_price_hint": "Guarda hasta dos decimales en la moneda configurada.",
                "activity_hint": "Las habitaciones inactivas se conservan en el inventario."
            },
            "messages": {
                "created": "Habitación creada con éxito.",
                "updated": "Habitación actualizada con éxito.",
                "deleted": "Habitación eliminada con éxito.",
                "activity_updated": "El estado de actividad fue actualizado.",
                "housekeeping_updated": "El estado de limpieza fue actualizado.",
                "delete_blocked": "Esta habitación tiene historial de estancias o reservas y no se puede eliminar.",
                "inventory_blocked": "Esta habitación tiene una estancia activa o reserva confirmada y no se puede desactivar."
            }
        },
        "settings": {
            "title": "Configuración",
            "actions": {
                "configure_currency": "Configurar moneda"
            },
            "pages": {
                "edit": {
                    "heading": "Configuración de la aplicación",
                    "description": "Define las preferencias globales que utiliza Hellen Suite."
                }
            },
            "currency": {
                "title": "Moneda de referencia",
                "description": "Selecciona la moneda que identifica las tarifas de referencia de todas las habitaciones.",
                "label": "Moneda",
                "placeholder": "Selecciona una moneda",
                "no_results": "No hay monedas que coincidan con la búsqueda.",
                "hint": "La selección admite todos los códigos ISO 4217 y las tarifas se guardan con dos decimales.",
                "reference_title": "Uso referencial",
                "reference_description": "Cambiar la moneda no convierte ni modifica las tarifas existentes. El código seleccionado solo indica cómo deben interpretarse y mostrarse."
            },
            "messages": {
                "updated": "La configuración de moneda fue actualizada.",
                "configuration_required": "Completa las configuraciones requeridas para continuar. Los campos pendientes están marcados abajo."
            }
        },
        "stays": {
            "title": "Estancias",
            "actions": {
                "create": "Nuevo check-in",
                "check_in": "Confirmar check-in",
                "check_out": "Dar salida",
                "transfer": "Trasladar habitación",
                "extend": "Cambiar salida esperada",
                "add_guest": "Agregar huésped",
                "view": "Ver estancia"
            },
            "fields": {
                "expected_check_out_on": {
                    "label": "Salida esperada"
                },
                "nightly_rate": {
                    "label": "Tarifa nocturna"
                },
                "room": {
                    "label": "Habitación"
                },
                "guests": {
                    "label": "Huéspedes"
                }
            },
            "pages": {
                "index": {
                    "heading": "Estancias",
                    "description": "Gestiona llegadas, huéspedes alojados y salidas de {hotel}.",
                    "empty_title": "No hay estancias registradas",
                    "empty": "Inicia un check-in para registrar la primera estancia."
                },
                "create": {
                    "heading": "Nuevo check-in",
                    "description": "Registra el grupo, asigna habitaciones disponibles y confirma la estancia."
                },
                "show": {
                    "heading": "Estancia",
                    "occupancies": "Habitaciones asignadas",
                    "group": "Grupo registrado",
                    "active": "Activa",
                    "checked_out": "Finalizada",
                    "costs": {
                        "heading": "Costos de la estancia",
                        "estimate_description": "Cargos de hospedaje proyectados con la salida esperada.",
                        "final_description": "Cargos finales de hospedaje según las noches ocupadas.",
                        "estimated_total": "Total estimado de hospedaje",
                        "total": "Total de hospedaje",
                        "billable_nights": "Noches cobrables",
                        "currency": "Moneda",
                        "room_period": "Habitación y periodo",
                        "subtotal": "Subtotal",
                        "per_night": "por noche",
                        "nights_count": "{count} noche|{count} noches"
                    },
                    "add_guest_description": "Busca o registra un acompañante y asígnalo a una habitación con cupo disponible.",
                    "no_room_capacity": "No hay cupo disponible para agregar otro huésped."
                }
            },
            "form": {
                "steps": {
                    "label": "Progreso del check-in",
                    "guests": "Huéspedes",
                    "rooms": "Habitaciones",
                    "review": "Confirmación"
                },
                "actions": {
                    "back": "Atrás",
                    "continue": "Continuar"
                },
                "stay": {
                    "check_in_today": "Entrada: hoy",
                    "check_in_hint": "La hora de entrada se registra al confirmar el check-in."
                },
                "guests": {
                    "title": "Grupo de huéspedes",
                    "description": "Busca una ficha existente o registra a cada persona sin salir del check-in.",
                    "responsible": "Responsable de la estancia",
                    "companion": "Acompañante",
                    "add_companion": "Agregar acompañante",
                    "new_guest": "Registrar nuevo huésped",
                    "search": "Buscar huésped existente"
                },
                "rooms": {
                    "title": "Selecciona habitaciones",
                    "description": "Elige habitaciones limpias disponibles y asigna a cada huésped de forma explícita.",
                    "available_title": "Habitaciones disponibles",
                    "available_description": "Selecciona una o varias habitaciones para esta estancia.",
                    "available_count": "{count} disponibles",
                    "room_number": "Habitación {number}",
                    "capacity": "{count} huéspedes",
                    "night": "noche",
                    "empty": "No hay habitaciones limpias y disponibles en este momento.",
                    "selected_title": "Habitaciones seleccionadas",
                    "selected_description": "La tarifa se captura ahora para conservar el valor histórico de esta estancia.",
                    "assigned": "asignados",
                    "remove_room": "Quitar habitación {number}",
                    "assign_title": "Asigna cada huésped",
                    "assign_description": "Cada persona debe ocupar una sola habitación.",
                    "assign_label": "Habitación asignada",
                    "assign_placeholder": "Selecciona una habitación"
                },
                "review": {
                    "title": "Revisión final",
                    "description": "Confirma el grupo y las habitaciones antes de registrar el check-in.",
                    "guests": "Huéspedes",
                    "rooms": "Habitaciones"
                },
                "summary": {
                    "title": "Resumen de estancia",
                    "guests": "Huéspedes",
                    "rooms": "Habitaciones",
                    "no_rooms": "Aún no hay habitaciones seleccionadas."
                },
                "messages": {
                    "complete_guests": "Completa los datos obligatorios de cada huésped antes de continuar.",
                    "select_check_out": "Indica la fecha esperada de salida.",
                    "select_room": "Selecciona al menos una habitación disponible.",
                    "assign_guests": "Asigna cada huésped a una habitación sin exceder su capacidad."
                }
            },
            "messages": {
                "checked_in": "Check-in registrado con éxito.",
                "checked_out": "La estancia fue cerrada y las habitaciones quedaron pendientes de limpieza.",
                "expected_check_out_updated": "La salida esperada fue actualizada.",
                "room_transferred": "La habitación fue trasladada con éxito.",
                "guest_added": "El huésped fue agregado a la estancia con éxito."
            },
            "validation": {
                "responsible_required": "Selecciona un huésped responsable válido.",
                "unknown_guest": "La asignación contiene un huésped que no pertenece al grupo.",
                "assign_every_guest_once": "Cada huésped debe estar asignado a exactamente una habitación.",
                "duplicate_guest": "Ya existe un huésped con este documento; búscalo y selecciónalo.",
                "room_unavailable": "La habitación ya no está disponible para esta estancia.",
                "room_capacity": "La asignación supera la capacidad de la habitación.",
                "stay_closed": "La estancia ya se encuentra cerrada.",
                "occupancy_closed": "La ocupación seleccionada ya se encuentra cerrada.",
                "occupancy_invalid": "La habitación seleccionada no pertenece a esta estancia.",
                "guest_already_in_stay": "Este huésped ya está registrado en la estancia."
            }
        },
        "validation": {
            "accepted": "El campo {attribute} debe ser aceptado.",
            "accepted_if": "El campo {attribute} debe ser aceptado cuando {other} es {value}.",
            "active_url": "El campo {attribute} debe ser una URL válida.",
            "after": "El campo {attribute} debe ser una fecha posterior a {date}.",
            "after_or_equal": "El campo {attribute} debe ser una fecha posterior o igual a {date}.",
            "alpha": "El campo {attribute} solo debe contener letras.",
            "alpha_dash": "El campo {attribute} solo debe contener letras, números, guiones y guiones bajos.",
            "alpha_num": "El campo {attribute} solo debe contener letras y números.",
            "any_of": "El campo {attribute} no es válido.",
            "array": "El campo {attribute} debe ser un arreglo.",
            "array_keys": "El campo {attribute} solo debe contener las siguientes claves: {values}.",
            "ascii": "El campo {attribute} solo debe contener caracteres y símbolos alfanuméricos de un solo byte.",
            "base64": "El campo {attribute} debe ser una cadena Base64 válida.",
            "before": "El campo {attribute} debe ser una fecha anterior a {date}.",
            "before_or_equal": "El campo {attribute} debe ser una fecha anterior o igual a {date}.",
            "between": {
                "array": "El campo {attribute} debe tener entre {min} y {max} elementos.",
                "file": "El campo {attribute} debe estar entre {min} y {max} kilobytes.",
                "numeric": "El campo {attribute} debe estar entre {min} y {max}.",
                "string": "El campo {attribute} debe tener entre {min} y {max} caracteres."
            },
            "boolean": "El campo {attribute} debe ser verdadero o falso.",
            "can": "El campo {attribute} contiene un valor no autorizado.",
            "confirmed": "La confirmación del campo {attribute} no coincide.",
            "contains": "Al campo {attribute} le falta un valor requerido.",
            "current_password": "La contraseña es incorrecta.",
            "date": "El campo {attribute} debe ser una fecha válida.",
            "date_equals": "El campo {attribute} debe ser una fecha igual a {date}.",
            "date_format": "El campo {attribute} debe coincidir con el formato {format}.",
            "decimal": "El campo {attribute} debe tener {decimal} decimales.",
            "declined": "El campo {attribute} debe ser rechazado.",
            "declined_if": "El campo {attribute} debe ser rechazado cuando {other} es {value}.",
            "different": "Los campos {attribute} y {other} deben ser diferentes.",
            "digits": "El campo {attribute} debe tener {digits} dígitos.",
            "digits_between": "El campo {attribute} debe tener entre {min} y {max} dígitos.",
            "dimensions": "El campo {attribute} tiene dimensiones de imagen inválidas.",
            "distinct": "El campo {attribute} tiene un valor duplicado.",
            "doesnt_contain": "El campo {attribute} no debe contener ninguno de los siguientes: {values}.",
            "doesnt_end_with": "El campo {attribute} no debe terminar con ninguno de los siguientes: {values}.",
            "doesnt_start_with": "El campo {attribute} no debe comenzar con ninguno de los siguientes: {values}.",
            "email": "El campo {attribute} debe ser una dirección de correo válida.",
            "encoding": "El campo {attribute} debe estar codificado en {encoding}.",
            "ends_with": "El campo {attribute} debe terminar con uno de los siguientes: {values}.",
            "enum": "El {attribute} seleccionado es inválido.",
            "exists": "El {attribute} seleccionado es inválido.",
            "extensions": "El campo {attribute} debe tener una de las siguientes extensiones: {values}.",
            "file": "El campo {attribute} debe ser un archivo.",
            "filled": "El campo {attribute} debe tener un valor.",
            "gt": {
                "array": "El campo {attribute} debe tener más de {value} elementos.",
                "file": "El campo {attribute} debe ser mayor que {value} kilobytes.",
                "numeric": "El campo {attribute} debe ser mayor que {value}.",
                "string": "El campo {attribute} debe ser mayor que {value} caracteres."
            },
            "gte": {
                "array": "El campo {attribute} debe tener {value} elementos o más.",
                "file": "El campo {attribute} debe ser mayor o igual que {value} kilobytes.",
                "numeric": "El campo {attribute} debe ser mayor o igual que {value}.",
                "string": "El campo {attribute} debe ser mayor o igual que {value} caracteres."
            },
            "hex_color": "El campo {attribute} debe ser un color hexadecimal válido.",
            "image": "El campo {attribute} debe ser una imagen.",
            "in": "El {attribute} seleccionado es inválido.",
            "in_array": "El campo {attribute} debe existir en {other}.",
            "in_array_keys": "El campo {attribute} debe contener al menos una de las siguientes claves: {values}.",
            "integer": "El campo {attribute} debe ser un entero.",
            "ip": "El campo {attribute} debe ser una dirección IP válida.",
            "ipv4": "El campo {attribute} debe ser una dirección IPv4 válida.",
            "ipv6": "El campo {attribute} debe ser una dirección IPv6 válida.",
            "json": "El campo {attribute} debe ser una cadena JSON válida.",
            "list": "El campo {attribute} debe ser una lista.",
            "lowercase": "El campo {attribute} debe estar en minúsculas.",
            "lt": {
                "array": "El campo {attribute} debe tener menos de {value} elementos.",
                "file": "El campo {attribute} debe ser menor que {value} kilobytes.",
                "numeric": "El campo {attribute} debe ser menor que {value}.",
                "string": "El campo {attribute} debe ser menor que {value} caracteres."
            },
            "lte": {
                "array": "El campo {attribute} no debe tener más de {value} elementos.",
                "file": "El campo {attribute} debe ser menor o igual que {value} kilobytes.",
                "numeric": "El campo {attribute} debe ser menor o igual que {value}.",
                "string": "El campo {attribute} debe ser menor o igual que {value} caracteres."
            },
            "mac_address": "El campo {attribute} debe ser una dirección MAC válida.",
            "max": {
                "array": "El campo {attribute} no debe tener más de {max} elementos.",
                "file": "El campo {attribute} no debe ser mayor que {max} kilobytes.",
                "numeric": "El campo {attribute} no debe ser mayor que {max}.",
                "string": "El campo {attribute} no debe ser mayor que {max} caracteres."
            },
            "max_digits": "El campo {attribute} no debe tener más de {max} dígitos.",
            "mimes": "El campo {attribute} debe ser un archivo de tipo: {values}.",
            "mimetypes": "El campo {attribute} debe ser un archivo de tipo: {values}.",
            "min": {
                "array": "El campo {attribute} debe tener al menos {min} elementos.",
                "file": "El campo {attribute} debe ser al menos {min} kilobytes.",
                "numeric": "El campo {attribute} debe ser al menos {min}.",
                "string": "El campo {attribute} debe tener al menos {min} caracteres."
            },
            "min_digits": "El campo {attribute} debe tener al menos {min} dígitos.",
            "missing": "El campo {attribute} debe faltar.",
            "missing_if": "El campo {attribute} debe faltar cuando {other} es {value}.",
            "missing_unless": "El campo {attribute} debe faltar a menos que {other} sea {value}.",
            "missing_with": "El campo {attribute} debe faltar cuando {values} esté presente.",
            "missing_with_all": "El campo {attribute} debe faltar cuando {values} estén presentes.",
            "multiple_of": "El campo {attribute} debe ser múltiplo de {value}.",
            "not_in": "El {attribute} seleccionado es inválido.",
            "not_regex": "El formato del campo {attribute} es inválido.",
            "numeric": "El campo {attribute} debe ser un número.",
            "password": {
                "letters": "El campo {attribute} debe contener al menos una letra.",
                "mixed": "El campo {attribute} debe contener al menos una letra mayúscula y una minúscula.",
                "numbers": "El campo {attribute} debe contener al menos un número.",
                "symbols": "El campo {attribute} debe contener al menos un símbolo.",
                "uncompromised": "El {attribute} proporcionado ha aparecido en una fuga de datos. Elige un {attribute} diferente."
            },
            "present": "El campo {attribute} debe estar presente.",
            "present_if": "El campo {attribute} debe estar presente cuando {other} es {value}.",
            "present_unless": "El campo {attribute} debe estar presente a menos que {other} sea {value}.",
            "present_with": "El campo {attribute} debe estar presente cuando {values} esté presente.",
            "present_with_all": "El campo {attribute} debe estar presente cuando {values} estén presentes.",
            "prohibited": "El campo {attribute} está prohibido.",
            "prohibited_if": "El campo {attribute} está prohibido cuando {other} es {value}.",
            "prohibited_if_accepted": "El campo {attribute} está prohibido cuando {other} es aceptado.",
            "prohibited_if_declined": "El campo {attribute} está prohibido cuando {other} es rechazado.",
            "prohibited_unless": "El campo {attribute} está prohibido a menos que {other} esté en {values}.",
            "prohibits": "El campo {attribute} prohíbe que {other} esté presente.",
            "regex": "El formato del campo {attribute} es inválido.",
            "required": "El campo {attribute} es obligatorio.",
            "required_array_keys": "El campo {attribute} debe contener entradas para: {values}.",
            "required_if": "El campo {attribute} es obligatorio cuando {other} es {value}.",
            "required_if_accepted": "El campo {attribute} es obligatorio cuando {other} es aceptado.",
            "required_if_declined": "El campo {attribute} es obligatorio cuando {other} es rechazado.",
            "required_unless": "El campo {attribute} es obligatorio a menos que {other} esté en {values}.",
            "required_with": "El campo {attribute} es obligatorio cuando {values} esté presente.",
            "required_with_all": "El campo {attribute} es obligatorio cuando {values} estén presentes.",
            "required_without": "El campo {attribute} es obligatorio cuando {values} no esté presente.",
            "required_without_all": "El campo {attribute} es obligatorio cuando ninguno de {values} esté presente.",
            "same": "El campo {attribute} debe coincidir con {other}.",
            "size": {
                "array": "El campo {attribute} debe contener {size} elementos.",
                "file": "El campo {attribute} debe ser de {size} kilobytes.",
                "numeric": "El campo {attribute} debe ser {size}.",
                "string": "El campo {attribute} debe ser de {size} caracteres."
            },
            "starts_with": "El campo {attribute} debe comenzar con uno de los siguientes: {values}.",
            "string": "El campo {attribute} debe ser una cadena.",
            "timezone": "El campo {attribute} debe ser una zona horaria válida.",
            "unique": "El campo {attribute} ya ha sido tomado.",
            "uploaded": "El campo {attribute} falló al subir.",
            "uppercase": "El campo {attribute} debe estar en mayúsculas.",
            "url": "El campo {attribute} debe ser una URL válida.",
            "ulid": "El campo {attribute} debe ser un ULID válido.",
            "uuid": "El campo {attribute} debe ser un UUID válido.",
            "custom": {
                "attribute-name": {
                    "rule-name": "custom-message"
                }
            },
            "attributes": []
        }
    }
}
