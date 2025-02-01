<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpeechToTextRequest extends FormRequest
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
        return [
            'file' => ['required', 'file', 'mimes:mp3,mp4,mpeg,mpga,m4a,wav,webm', 'max:25000'],
            'language' => ['string', 'in:af,ar,hy,az,be,bs,bg,ca,zh,hr,cs,da,nl,en,et,fi,fr,gl,de,el,he,hi,hu,is,id,it,ja,kn,kk,ko,lv,lt,mk,ms,mr,mi,ne,no,fa,pl,pt,ro,ru,sr,sk,sl,es,sw,sv,tl,ta,th,tr,uk,ur,vi,cy'],
        ];
    }
}
