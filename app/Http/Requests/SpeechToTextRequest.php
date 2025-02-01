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
            'model' => ['required', 'string', 'in:whisper-1'],
            'language' => ['string', 'in:af,ar,hy,az,be,bs,bg,ca,zh,hr,cs,da,nl,en,et,fi,fr,gl,de,el,he,hi,hu,is,id,it,ja,kn,kk,ko,lv,lt,mk,ms,mr,mi,ne,no,fa,pl,pt,ro,ru,sr,sk,sl,es,sw,sv,tl,ta,th,tr,uk,ur,vi,cy'],
            'response_format' => ['string', 'in:json,text,srt,verbose_json,vtt'],
            'temperature' => ['numeric', 'min:0', 'max:1'],
            'timestamp_granularities' => ['string', 'in:word,segment'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.required' => 'The file is required.',
            'file.file' => 'The file must be a file.',
            'file.mimes' => 'The file must be a file of type: :values.',
            'file.max' => 'The file may not be greater than :max kilobytes.',
            'model.required' => 'The model is required.',
            'model.string' => 'The model must be a string.',
            'model.in' => 'The selected model is invalid.',
            'language.string' => 'The language must be a string.',
            'language.in' => 'The selected language must be one of the following options: :in',
            'response_format.string' => 'The response format must be a string.',
            'response_format.in' => 'The selected response format must be one of the following options: :in',
            'temperature.numeric' => 'The temperature must be a number.',
            'temperature.min' => 'The temperature must be at least :min.',
            'temperature.max' => 'The temperature may not be greater than :max.',
            'timestamp_granularities.string' => 'The timestamp granularities must be a string.',
            'timestamp_granularities.in' => 'The selected timestamp granularities must be one of the following options: :in',
        ];
    }
}
