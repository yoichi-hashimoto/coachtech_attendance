<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationRequest extends FormRequest
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
            'requested_clock_in_time' => ['required','date_format:H:i','before:requested_clock_out_time'],
            'requested_break_start_time' => ['nullable','date_format:H:i','required_with:requested_break_end_time','after:requested_clock_in_time','before:requested_clock_out_time'],
            'requested_break_end_time' => ['nullable','date_format:H:i','required_with:requested_break_start_time','after:requested_break_start_time','before:requested_clock_out_time'],
            'requested_clock_out_time' => ['required','date_format:H:i','after:requested_clock_in_time'],
            'notes' => ['required','string','max:500'],
        ];
    }

    public function messages()
    {
        return [
            'requested_clock_in_time.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'requested_clock_out_time.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'requested_break_start_time.before' => '休憩時間が不適切な値です',
            'requested_break_start_time.after' => '休憩時間が不適切な値です',
            'requested_break_end_time.before' => '休憩時間もしくは退勤時間が不適切な値です',
            'notes.required' => '備考を記入してください',
        ];
    }
}
