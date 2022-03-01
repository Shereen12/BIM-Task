<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\TransactionRequest;
use App\Http\Requests\PaymentRequest;
use DB;
use Auth;
use App\Models\Transaction;
use App\Models\Report;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\ReportResource;
use App\Models\Payment;
use App\Http\Requests\ReportRequest;
use App\Http\Requests\MonthlyReportRequest;
use Carbon\CarbonPeriod;


class TransactionController extends Controller
{
    public function create(TransactionRequest $request){
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $subcategory = ($validated['subcategory_id'] ? $validated['subcategory_id'] : null);
            $transaction = Transaction::create([
                'category_id' => $validated['category_id'],
                'subcategory_id' => $subcategory,
                'amount' => $validated['amount'],
                'customer_id' => $validated['customer_id'],
                'due' => $validated['due'],
                'vat' => $validated['vat'],
                'is_vat_inclusive' => $validated['is_vat_inclusive']=='true'?true:false,
            ]);
            DB::commit();
            if ($transaction) {
                return $this->apiResponse("Record Inserted", [], new TransactionResource($transaction), [], 201);
            } else {
                return $this->apiResponse("Error while inserting record", [], [], [], 422);
            }
       } catch (\Throwable $th) {
            DB::rollback();
            return $this->apiResponse("Error while inserting record", [], [], [], 422);
        }
    }

    public function records(){
        try{
            $transactions = Transaction::all();
            if(count($transactions) > 0){
                return $this->apiResponse("Transactions retrieved successfully", [], TransactionResource::collection($transactions), [], 200);
            }
            return $this->apiResponse("No transactions found", [], [], [], 404);
        }catch (\Throwable $th) {
            return $this->apiResponse("Error while retrieving record", [], [], [], 422);
        }
    }

    public function userTransactions(){
        try{
            $transactions = Transaction::where('customer_id', Auth::user()->id)->get();
            if(count($transactions) > 0){
                return $this->apiResponse('Transactions retrieved successfully', [], TransactionResource::collection($transactions), [], 200);
            }
            return $this->apiResponse('No transactions found', [], [], [], 404); 
        }catch (\Throwable $th) {
            return $this->apiResponse("Error while retrieving records", [], [], [], 422);
        }
    }

    public function report(ReportRequest $request){
        $validated = $request->validated();
        $startingDate = date($validated['starting_date']);
        $endingDate = date($validated['ending_date']);
        $startMonth = date("m", strtotime($startingDate));
        $startYear = date("Y", strtotime($startingDate));
        $endMonth = date("m", strtotime($endingDate));
        $endYear = date("Y", strtotime($endingDate));
        try{
            $reportData = ['Period' => $startYear . '-' .  $startMonth  . ' to ' . $endYear . '-' . $endMonth,
                        'Paid amount' => $this->getPaidAmount($startingDate, $endingDate),
                        'Outstanding amount' => $this->getOutstandingAmount($startingDate,$endingDate),
                        'Overdue amount' => $this->getOverdueAmount($startingDate, $endingDate)];
            return $this->apiResponse("Report generated successfully", [], $reportData, [], 200);
        }catch (\Throwable $th) {
            return $th;
            return $this->apiResponse("Error while retrieving records", [], [], [], 422);
        }
    }
 
    private function getPaidAmount($startingDate, $endingDate){
        try{
            $paymentsSum = Payment::whereDate('paid_date', '>=', $startingDate)->whereDate('paid_date', '<=', $endingDate)->sum('amount');
            return $paymentsSum;
        }catch (\Throwable $th) {
            return 'Error while getting paid amount';
        }
    }

    private function getOverdueAmount($startingDate, $endingDate){
        try{
            $transactions = Transaction::whereDate('created_at', '>=', $startingDate)->whereDate('created_at', '<=', $endingDate)->get();
            $overdueTransactions = $transactions->filter(function($model){
                return $model->getStatus() == 'Overdue';
            });
            $overdueAmount = 0;
            foreach($overdueTransactions as $overdueTransaction){
                $overdueAmount += $overdueTransaction->amount - $overdueTransaction->payments->sum('amount');
            }
            return $overdueAmount;
        }catch (\Throwable $th) {
            return 'Error while getting overdue amount';
        }
    }

    private function getOutstandingAmount($startingDate, $endingDate){
        try{
            $transactions = Transaction::whereDate('created_at', '>=', $startingDate)->whereDate('created_at', '<=', $endingDate)->get();
            $outstandingTransactions = $transactions->filter(function($model){
                return $model->getStatus() == 'Outstanding';
            });
            $outstandingAmount = 0;
            foreach($outstandingTransactions as $outstandingTransaction){
                $outstandingAmount += $outstandingTransaction->amount - $outstandingTransaction->payments->sum('amount');
            }
            return $outstandingAmount;
        }catch (\Throwable $th) {
            return 'Error while getting outstanding amount';
        }
    }

    public function monthlyReport(MonthlyReportRequest $request){
        $validated = $request->validated();
        try{
            $months = CarbonPeriod::create($validated['starting_date'], '1 month', $validated['ending_date']);
            $response = [];
            foreach($months as $month){
                $endingDate = date("Y-m-t", strtotime($month));
                $startingDate = date($month);
                array_push($response, ['month' => str($month->month),
                                    'year' => str($month->year),
                                    'Paid' => str($this->getPaidAmount($startingDate, $endingDate)),
                                    'outstanding' => str($this->getOutstandingAmount($startingDate, $endingDate)),
                                    'overdue' => str($this->getOverdueAmount($startingDate, $endingDate))]
            );
        }
            return $this->apiResponse('Monthly report generated successfully', [], $response, [], 200);
    }catch (\Throwable $th) {
        return $this->apiResponse('Error while retrieving report', [], $response, [], 422);
    }
    }
}
