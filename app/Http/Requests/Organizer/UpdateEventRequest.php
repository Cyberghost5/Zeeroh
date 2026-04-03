<?php

namespace App\Http\Requests\Organizer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $event = $this->route('event');
        return auth()->check()
            && auth()->user()->hasRole('organizer')
            && $event->organizer_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['required', 'string', 'min:20'],
            'category_id'    => ['nullable', 'exists:categories,id'],
            'start_date'     => ['required', 'date'],
            'end_date'       => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time'     => ['required', 'date_format:H:i'],
            'end_time'       => ['nullable', 'date_format:H:i'],
            'is_virtual'     => ['boolean'],
            'venue_name'     => ['required_if:is_virtual,0', 'nullable', 'string', 'max:255'],
            'venue_address'  => ['required_if:is_virtual,0', 'nullable', 'string', 'max:500'],
            'city'           => ['required_if:is_virtual,0', 'nullable', 'string', 'max:100'],
            'state'          => ['required_if:is_virtual,0', 'nullable', 'string', 'max:100'],
            'virtual_link'   => ['required_if:is_virtual,1', 'nullable', 'url', 'max:500'],
            'banner'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_banner'  => ['boolean'],
            'ticket_types'              => ['required', 'array', 'min:1'],
            'ticket_types.*.id'         => ['nullable', 'integer'],
            'ticket_types.*.name'       => ['required', 'string', 'max:100'],
            'ticket_types.*.price'      => ['required', 'numeric', 'min:0'],
            'ticket_types.*.quantity'   => ['required', 'integer', 'min:1'],
            'ticket_types.*.description'  => ['nullable', 'string', 'max:255'],
            'ticket_types.*.max_per_order' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
