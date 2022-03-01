<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PaymentRequest;
use App\Http\Resources\PaymentResource;

use App\Models\Payment;
use DB;
    

class PaymentController extends Controller
{
    public function create(PaymentRequest $request){
        $validated = $request->validated();
        if($request['details']){
            $details = $validated['details'];
        }
        else{
            $details = null;
        }
        DB::beginTransaction();
       try {
            $payment = Payment::create([
                'transaction_id' => $validated['transaction_id'],
                'amount' => $validated['amount'],
                'paid_date' => $validated['paid_date'],
                'method' => $validated['method'],
                'details' => $details,
            ]);
            DB::commit();
            if ($payment) {
                return $this->apiResponse("Record Inserted", [], new PaymentResource($payment), [], 201);
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
            $payments = Payment::all();
            if(count($payments) > 0){
                return $this->apiResponse("Payments retrieved successfully", [], PaymentResource::collection($payments), [], 201);
            }
            return $this->apiResponse("No payments found", [], [], [], 422);
        }catch (\Throwable $th) {
        return $this->apiResponse("Error while retrieving records", [], [], [], 422);
     }
    }
}
