<?php

namespace App\Http\Requests;

class CheckClockSettingCompleteCreateRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            // NOTE: Due to lack of context (likely because it hasn't been implemented yet) in frontend user auth, hence id_company become unrelevant (no source to take).
            // 'id_company' => 'required|uuid',
            'type' => 'required|in:WFA,WFO,Hybrid',
            'location_lat' => 'nullable|numeric',
            'location_lng' => 'nullable|numeric',
            'radius' => 'nullable|integer|min:0',
            'check_clock_setting_time' => 'required|array',
            'check_clock_setting_time.*.day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'check_clock_setting_time.*.clock_in' => 'required|date_format:H:i',
            'check_clock_setting_time.*.clock_out' => 'required|date_format:H:i',
            'check_clock_setting_time.*.break_start' => 'required|date_format:H:i',
            'check_clock_setting_time.*.break_end' => 'required|date_format:H:i',
        ];
    }
}
