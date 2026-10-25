<?php

namespace App\Http\Requests;

use App\Enums\HandoffMethod;
use App\Models\Donation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canPostDonations();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:120'],
            'description' => ['required', 'string', 'min:20', 'max:2000'],
            'category' => ['required', Rule::in(array_keys(Donation::CATEGORIES))],
            'condition' => ['required', Rule::in(array_keys(Donation::CONDITIONS))],
            'weight_kg' => ['required', 'numeric', 'min:0.1', 'max:500'],
            'item_count' => ['required', 'integer', 'min:1', 'max:500'],
            'barangay' => ['required', 'string', Rule::in(Donation::BARANGAYS)],
            'handoff_method' => ['required', Rule::in(array_keys(HandoffMethod::options()))],
            'pickup_address' => ['nullable', 'required_if:handoff_method,pickup', 'string', 'max:255'],
            'available_from' => ['nullable', 'date', 'after_or_equal:today'],
            'images' => ['nullable', 'array', 'max:4'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'description.min' => 'Describe the fabric a little more — condition, fibre, what it could become.',
            'pickup_address.required_if' => 'Tell the upcycler where to collect the items.',
            'images.*.max' => 'Each photo must be under 4 MB.',
        ];
    }
}
