<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'ID' => $this->id,
            'Transaction ID' => $this->transaction->id,
            'Amount' => $this->amount,
            'Payment Method' => $this->method,
            'Paid at' => $this->paid_date,
            'Details' => $this->details,
        ];
    }
}
