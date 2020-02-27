<div class="gratitude text-center my-4">
    <p class="text-muted">
        <small>
            @lang('vouchers.note') @lang('vouchers.questions'):
            {{ $voucher->hotel->email ? $voucher->hotel->email . ', ' : '' }} {{ $voucher->hotel->phone ?? '' }}.
            ¡@lang('common.thanks')!.
        </small>
    </p>
</div>