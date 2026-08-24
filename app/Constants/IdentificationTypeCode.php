<?php

declare(strict_types=1);

namespace App\Constants;

use App\Concerns\EnumArrayable;

enum IdentificationTypeCode: string
{
    use EnumArrayable;

    case CitizenshipId = 'CC';

    case ForeignerId = 'CE';

    case IdentityCard = 'TI';

    case CivilBirthRegistration = 'RC';

    case Passport = 'PASSPORT';

    case ForeignNationalId = 'NATIONAL_ID';

    public function text(): string
    {
        return match ($this) {
            self::CitizenshipId => trans('identification_types.cc'),
            self::ForeignerId => trans('identification_types.ce'),
            self::IdentityCard => trans('identification_types.ti'),
            self::CivilBirthRegistration => trans('identification_types.rc'),
            self::Passport => trans('identification_types.passport'),
            self::ForeignNationalId => trans('identification_types.national_id'),
        };
    }
}
