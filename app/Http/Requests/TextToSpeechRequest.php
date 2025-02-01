<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TextToSpeechRequest extends FormRequest
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
            'model' => ['required', 'string', 'in:tts-1,tts-1-hd'],
            'input' => ['required', 'string', 'min:3', 'max:4096'],
            'voice' => ['required', 'string', 'in:alloy,ash,coral,echo,fable,onyx,nova,sage,shimmer'],
            'response_format' => ['string', 'in:mp3,opus,aac,flac,wav,pcm'],
            'speed' => ['numeric', 'min:0.25', 'max:4.0'],
            'language' => ['string', 'in:af,ar,hy,az,be,bs,bg,ca,zh,hr,cs,da,nl,en,et,fi,fr,gl,de,el,he,hi,hu,is,id,it,ja,kn,kk,ko,lv,lt,mk,ms,mr,mi,ne,no,fa,pl,pt,ro,ru,sr,sk,sl,es,sw,sv,tl,ta,th,tr,uk,ur,vi,cy'],
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
            'model.required' => 'The model is required.',
            'model.string' => 'The model must be a string.',
            'model.in' => 'The selected model is invalid.',
            'input.required' => 'The input is required.',
            'input.string' => 'The input must be a string.',
            'input.min' => 'The input must be at least :min characters.',
            'input.max' => 'The input may not be greater than :max characters.',
            'voice.required' => 'The voice is required.',
            'voice.string' => 'The voice must be a string.',
            'voice.in' => 'The selected voice must be one of the following options: :in.',
            'response_format.string' => 'The response format must be a string.',
            'response_format.in' => 'The selected response format must be one of the following options: :in',
            'language.string' => 'The language must be a string.',
            'speed.numeric' => 'The speed must be a number.',
            'speed.min' => 'The speed must be at least :min.',
            'speed.max' => 'The speed may not be greater than :max.',
            'language.in' => 'The selected language must be one of the following options: :in',
        ];
    }
}
