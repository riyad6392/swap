<?php

namespace App\Http\Requests\Shipment;

use App\Models\Swap;
use Illuminate\Foundation\Http\FormRequest;

class UpdateShipmentRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $swap = Swap::find($this->get('swap_id'));
        if ($swap) {
            $this->merge(['swap' => $swap]);
            if ($swap->requested_user_id == auth()->id()) {
                return [
                    'swap_id' => 'required|string|exists:swaps,id',
                    'requested_address' => 'required|string',
                    'requested_tracking_number' => 'required|string',
                    'requested_carrier_name' => 'required|string',
                    'requested_carrier_contact' => 'required|string',
                    'requested_expected_delivery_date' => 'required|string',
                ];
            }elseif ($swap->exchanged_user_id == auth()->id()) {
                return [
                    'swap_id' => 'required|string|exists:swaps,id',
                    'exchanged_address' => 'required|string',
                    'exchanged_tracking_number' => 'required|numeric',
                    'exchanged_carrier_name' => 'required|numeric',
                    'exchanged_carrier_contact' => 'required|string',
                    'exchanged_expected_delivery_date' => 'required|string',
                ];
            }
        }else{
            return [
                'swap_id' => 'required|string|exists:swaps,id',
            ];
        }
    }

    public function messages(): array
    {
        return [
            'swap_id.required' => 'Swap ID is required',
            'requested_address.required' => 'Requested Address is required',
            'requested_tracking_number.required' => 'Requested Tracking Number is required',
            'requested_carrier_name.required' => 'Requested Carrier Name is required',
            'requested_carrier_contact.required' => 'Requested Carrier Contact is required',
            'requested_expected_delivery_date.required' => 'Requested Expected Delivery Date is required',

            'exchanged_address.required' => 'Exchanged Address is required',
            'exchanged_tracking_number.required' => 'Exchanged Tracking Number is required',
            'exchanged_carrier_name.required' => 'Exchanged Carrier Name is required',
            'exchanged_carrier_contact.required' => 'Exchanged Carrier Contact is required',
            'exchanged_expected_delivery_date.required' => 'Exchanged Expected Delivery Date is required',
        ];
    }
}
