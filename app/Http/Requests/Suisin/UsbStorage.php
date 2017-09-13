<?php

namespace App\Http\Requests\Suisin;

use App\Http\Requests\Request;

class UsbStorage extends Request
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
            'usb_path'=>'required|regex:/^[A-Z]:\/$/',
        ];
    }
    public function attributes() {
        return ['usb_path'=>'USBストレージドライブ名'];
    }
}
