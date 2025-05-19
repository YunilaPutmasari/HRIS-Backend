<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->first_name . ' ' . $this->last_name,
            'position' => $this->position_name(),
            'email' => $this->user ? $this->user->email : null,
        ];
    }

    private function position_name()
    {
        // Cukup cek kalau relasi position sudah ada
        return $this->position ? $this->position->name : null;
    }
}
