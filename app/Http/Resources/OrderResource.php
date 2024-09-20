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
            'employee' => ['id' => $this->employee->id, 'name' => $this->employee->name, 'role' => 'casher'],
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'dining_table' => new Dining_TableResource($this->whenLoaded('dining_table')),
        ];
    }
}
