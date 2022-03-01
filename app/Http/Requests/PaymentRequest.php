<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Transaction;

class PaymentRequest extends MainRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'transaction_id' => 'required|exists:transactions,id',
            'amount' => 'required|numeric|gt:0',
            'paid_date' => ['required', 'date', 'date_format:Y-m-d',function ($attribute, $value, $fail) {
                if (Transaction::where('id', $this->transaction_id)->whereDate('created_at', '>', $value)->count()) {
                    $fail('Paid date cannot be earlier than transaction creation date');
                }}],
            'method' => 'required|in:Cash,CreditCard,PayPal',
            'details' => 'sometimes',
        ];
    }
}
