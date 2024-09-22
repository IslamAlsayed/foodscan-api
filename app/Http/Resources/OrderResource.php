<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'total' => $this->total,
            'payment_type' => $this->payment_type,
            'order_status' => $this->order_status,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'employee' => [
                'id' => $this->employee->id,
                'name' => $this->employee->name,
                'role' => 'casher'
            ],
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'dining_table' => [
                'id' => $this->dining_table->id,
                'floor' => $this->dining_table->floor,
                'size' => $this->dining_table->size,
                'status' => $this->dining_table->status
            ],
        ];
    }
}
