<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function validationData(): array
    {
        $data = parent::validationData();
        if (!empty($data)) {
            return $data;
        }
        $raw = $this->getContent();
        $arr = is_string($raw) && $raw !== '' ? json_decode($raw, true) : null;
        return is_array($arr) ? $arr : [];
    }

    public function rules(): array
    {
        return [
            'login'    => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }
}
