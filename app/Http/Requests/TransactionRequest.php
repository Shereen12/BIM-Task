<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Category;
use App\Models\User;


class TransactionRequest extends MainRequest
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
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => ['integer',
                function ($attribute, $value, $fail) {
                    if (Category::where('id', $value)->where('parent_id', $this->category_id)->count() == 0) {
                        $fail('wrong subcategory');
                    }
                },'nullable',
            ],
            'amount' => 'required|numeric|gt:-1',
            'customer_id' => ['required', function ($attribute, $value, $fail) {
                if (User::where('id', $value)->where('is_admin', true)->count() > 0) {
                    $fail('wrong customer');
                }
            },'nullable',],
            'due' => 'required|date|date_format:Y-m-d',
            'vat' => 'required|numeric|gt:-1',
            'is_vat_inclusive' => 'required|in:true,false',
        ];
    }
}
