<?php

namespace App\Http\Controllers;

use App\Actions\FetchProductSummariesAction;
use App\Exports\ProductSummaryExport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    /**
     * @param Request $request
     * @param FetchProductSummariesAction $fetchProductSummariesAction
     * @return \Illuminate\Http\Response
     */
    public function index(
        Request $request,
        FetchProductSummariesAction $fetchProductSummariesAction
    ) {
        $productSummaries = $fetchProductSummariesAction->execute(
            $request->user(),
            $request->get('from_date'),
            $request->get('to_date')
        );

        if ($request->get('action') === 'export') {
            return Excel::download(
                new ProductSummaryExport($productSummaries),
                'product-summary-' . Carbon::now()->unix() . '.xlsx'
            );
        }

        return Response::view('welcome', $productSummaries);
    }
}
