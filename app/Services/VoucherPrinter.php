<?php

namespace App\Services;

use App\Contracts\VoucherPrinter as VoucherPrinterContract;
use App\Models\Voucher;
use App\Helpers\Customer;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use LaravelDaily\Invoices\Invoice;
use Illuminate\Support\Facades\Storage;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use ReflectionClass;

class VoucherPrinter implements VoucherPrinterContract
{
    protected string $template = 'voucher';

    protected Voucher $voucher;

    protected Party $customer;

    protected Party $seller;

    protected Collection $items;

    protected string $notes;

    protected Invoice $document;

    public function __construct()
    {
        $this->items = collect();
    }

    public function setVoucher(Voucher $voucher): self
    {
        $this->voucher = $voucher;

        return $this;
    }

    public function setCustomer(): self
    {
        $customer = $this->getCustomer();

        $this->customer = new Party([
            'name'          => $customer->name,
            'address'       => $customer->address,
            'code'          => $customer->tin,
            'phone' => $customer->phone,
            'custom_fields' => [
                trans('common.email') => $customer->email,
            ],
        ]);

        return $this;
    }

    public function getCustomer(): object
    {
        return (object) Customer::get($this->voucher);
    }

    public function setSeller(): self
    {
        $this->seller = new Party([
            'name' => $this->voucher->hotel->business_name,
            'address' => $this->voucher->hotel->address,
            'code' => $this->voucher->hotel->tin,
            'phone' => $this->voucher->hotel->phone,
            'custom_fields' => [
                trans('common.email') => $this->voucher->hotel->email,
            ]
        ]);

        return $this;
    }

    public function setRoomItems(): self
    {
        $this->voucher->rooms->each(function ($room) {
            $item = new InvoiceItem();

            $item->title(trans('rooms.number', ['number' => $room->number]))
                ->pricePerUnit($room->pivot->subvalue)
                ->quantity($room->pivot->quantity)
                ->discount($room->pivot->discount);

            $this->items->push($item);
        });

        return $this;
    }

    public function setProductItems(): self
    {
        $this->voucher->products->each(function ($product) {
            $item = new InvoiceItem();

            $item->title($product->description)
                ->pricePerUnit($product->price)
                ->quantity($product->pivot->quantity)
                ->discount(0);

            $this->items->push($item);
        });

        return $this;
    }

    public function setServiceItems(): self
    {
        $this->voucher->services->each(function ($service) {
            $item = new InvoiceItem();

            $item->title($service->description)
                ->pricePerUnit($service->price)
                ->quantity($service->pivot->quantity)
                ->discount(0);

            $this->items->push($item);
        });

        return $this;
    }

    public function setAditionalItems(): self
    {
        $this->voucher->additionals->each(function ($aditional) {
            $item = new InvoiceItem();

            $item->title($aditional->description)
                ->pricePerUnit($aditional->value)
                ->quantity(1)
                ->discount(0);

            $this->items->push($item);
        });

        return $this;
    }

    public function setNotes(): self
    {
        $this->notes = trans('vouchers.note');

        return $this;
    }

    public function makeDocument(): self
    {
        $type = strtoupper(trans('vouchers.voucher'));

        $this->document = Invoice::make($type);

        return $this;
    }

    public function getLogo(): string
    {
        if (empty($this->voucher->hotel->image)) {
            return public_path('images/hotel.png');
        }

        return public_path(Storage::url($this->voucher->hotel->image));
    }

    public function setData()
    {
        $this->setCustomer()
            ->setSeller()
            ->setRoomItems()
            ->setProductItems()
            ->setServiceItems()
            ->setAditionalItems()
            ->setNotes()
            ->makeDocument();
    }

    public function buildDocument(): self
    {
        $this->setData();

        $this->document->series(Voucher::PREFIX)
            ->sequence((int) $this->voucher->number)
            ->serialNumberFormat('{SERIES}-{SEQUENCE}')
            ->seller($this->seller)
            ->buyer($this->customer)
            ->date($this->voucher->created_at)
            ->dateFormat('m/d/Y')
            ->payUntilDays(1)
            ->currencySymbol('$')
            ->currencyCode('COP')
            ->currencyFormat('{SYMBOL} {VALUE}')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->filename($this->voucher->number)
            ->addItems($this->items->toArray())
            ->notes($this->notes)
            ->logo($this->getLogo())
            ->template($this->template);

        return $this;
    }

    public function stream(): Response
    {
        return $this->document->stream();
    }
}
