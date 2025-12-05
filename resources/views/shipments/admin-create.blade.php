@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Record Shipment (Administration)') }}</h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('shipments.admin.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 gap-4">
                            <label class="block">
                                <span class="text-gray-700 dark:text-gray-200">Shipment number (optional)</span>
                                <input type="text" name="shipment_number" class="mt-1 block w-full" value="{{ old('shipment_number') }}">
                            </label>

                            <label class="block">
                                <span class="text-gray-700 dark:text-gray-200">Received at</span>
                                <input type="datetime-local" name="received_at" required class="mt-1 block w-full" value="{{ old('received_at') }}">
                            </label>

                            <label class="block">
                                <span class="text-gray-700 dark:text-gray-200">Number of colis received</span>
                                <input type="number" name="colis_count" min="0" required class="mt-1 block w-full" value="{{ old('colis_count', 1) }}">
                            </label>

                            <label class="block">
                                <span class="text-gray-700 dark:text-gray-200">Sender (optional)</span>
                                <input type="text" name="sender" class="mt-1 block w-full" value="{{ old('sender') }}">
                            </label>

                            <label class="block">
                                <span class="text-gray-700 dark:text-gray-200">Assign to location (optional)</span>
                                <select name="to_location_id" class="mt-1 block w-full">
                                    <option value="">-- Select location --</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->id }}" {{ old('to_location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="block">
                                <span class="text-gray-700 dark:text-gray-200">Project (optional)</span>
                                <select name="project_id" class="mt-1 block w-full">
                                    <option value="">-- None --</option>
                                    @foreach($projects as $p)
                                        <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="block">
                                <span class="text-gray-700 dark:text-gray-200">Administration notes</span>
                                <textarea name="admin_notes" rows="4" class="mt-1 block w-full">{{ old('admin_notes') }}</textarea>
                            </label>

                            <label class="block">
                                <span class="text-gray-700 dark:text-gray-200">Upload documents (invoices, packing list) — multiple allowed</span>
                                <input type="file" name="documents[]" multiple class="mt-1 block w-full">
                            </label>

                            <div class="pt-4">
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Record Reception</button>
                                <a href="{{ route('shipments.index') }}" class="ml-3 text-sm text-gray-600">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
