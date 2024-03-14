<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderListingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // You can add authentication logic here if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'search'         => 'nullable|string',
            'status'         => 'nullable|string',
            'sort_by'        => 'nullable|string|in:number,order_key,status,date_created,total',
            'sort_direction' => 'nullable|string|in:asc,desc',
            'per_page'       => 'nullable|integer|min:1',
        ];
    }
}
