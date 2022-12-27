<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFacade;
use Maatwebsite\Excel\Concerns\FromView;

class ProductSummaryExport implements FromView
{
    /**
     * @var array<string, mixed>
     */
    protected $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        return ViewFacade::make('product-summary-export', $this->data);
    }
}
