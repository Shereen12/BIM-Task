<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class TransactionResource extends JsonResource
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
            'Payer' => $this->customer->name,
            'Category' => $this->category->name,
            'Subcategory' => $this->category->name,
            'Amount' => $this->amount,
            'Status' => $this->getStatus(),
            'Due On' => $this->due,
        ];
    }
}
