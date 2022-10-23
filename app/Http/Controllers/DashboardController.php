<?php

namespace App\Http\Controllers;

use App\Actions\FetchProductSummariesAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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
            $request->get('from_date'),
            $request->get('to_date')
        );

        return Response::view('welcome', $productSummaries);
    }
}
