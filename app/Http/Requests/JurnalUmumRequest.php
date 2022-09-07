<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JurnalUmumRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'akun_id'           => ['required','array'],
            'akun_id.*'           => ['required','numeric'],
            'tanggal'           => ['required','date'],
            'keterangan'        => ['required'],
            'bukti'             => ['nullable','file','max:2048'],
            'debit_atau_kredit' => ['required', 'array'],
            'debit_atau_kredit.*' => ['required'],
            'nilai'             => ['required','numeric'],
            // 'status'            => ['required']
        ];
    }

    /**
     * Get the validation message that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'akun_id.*.required'  => __('akun wajib diisi.'),
            'akun_id.*.numeric'   => __('akun harus berupa angka.'),
            'debit_atau_kredit.*.required'  => __('akun wajib diisi.'),
        ];
    }
}
