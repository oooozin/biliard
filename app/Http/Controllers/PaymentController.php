<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentStoreRequest;
use App\Http\Requests\PaymentUpdateRequest;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportPayment;
use App\Exports\ExportPaymentParams;
use App\Imports\ImportPayment;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        DB::beginTransaction();

        try {
            $payments = Payment::searchQuery()
                ->sortingQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $payments->transform(function ($payment) {
                $payment->created_by = $payment->created_by ? User::find($payment->created_by)->name : "Unknown";
                $payment->updated_by = $payment->updated_by ? User::find($payment->updated_by)->name : "Unknown";
                $payment->deleted_by = $payment->deleted_by ? User::find($payment->deleted_by)->name : "Unknown";
                
                return $payment;
            });

            DB::commit();

            return $this->success('Payments retrieved successfully', $payments);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function store(PaymentStoreRequest $request)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());

        try {

            $payment = Payment::create($payload->toArray());

            DB::commit();

            return $this->success('payment created successfully', $payment);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();

        try {

            $payment = Payment::findOrFail($id);
            DB::commit();

            return $this->success('payment retrived successfully by id', $payment);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function update(PaymentUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        try {

            $payment = Payment::findOrFail($id);
            $payment->update($payload->toArray());
            DB::commit();

            return $this->success('payment updated successfully by id', $payment);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $payment = Payment::findOrFail($id);
            $payment->forceDelete();

            DB::commit();

            return $this->success('payment deleted successfully by id', []);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function exportexcel()
    {
        return Excel::download(new ExportPayment, 'Payments.xlsx');
    }

    public function exportparams(Request $request)
    {
        $filters = [
            'page' => $request->input('page'),
            'per_page' => $request->input('per_page'),
            'columns' => $request->input('columns'),
            'search' => $request->input('search'),
            'order' => $request->input('order'),
            'sort' => $request->input('sort'),
            'value' => $request->input('value'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];
        return Excel::download(new ExportPaymentParams($filters), 'Payments.xlsx');
    }

    public function exportpdf()
    {
        $data = Payment::all();
        $pdf = Pdf::loadView('paymentexport', ['data' => $data]);
        return $pdf->download();
    }

    public function exportpdfparams()
    {
        $data = Payment::searchQuery()
        ->sortingQuery()
        ->filterQuery()
        ->filterDateQuery()
        ->paginationQuery();
        
        $pdf = Pdf::loadView('paymentexport', ['data' => $data]);
        return $pdf->download();
    }

    public function import()
    {
        Excel::import(new ImportPayment, request()->file('file'));

        return $this->success('Payment is imported successfully');
    }
}
