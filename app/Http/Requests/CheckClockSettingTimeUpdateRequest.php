<?php

namespace App\Http\Requests;

class CheckClockSettingTimeUpdateRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i',
            'break_start' => 'required|date_format:H:i',
            'break_end' => 'required|date_format:H:i',
        ];
    }
}
