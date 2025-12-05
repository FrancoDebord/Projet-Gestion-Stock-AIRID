@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                    <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Back
                    </a>
                </div>

                <div class="mb-8 p-4 bg-gray-50 rounded">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="text-lg font-semibold">{{ $user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Joined</p>
                            <p class="text-lg font-semibold">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <h3 class="text-xl font-bold mb-4">Assigned Roles</h3>
                @if($roles->count())
                    <div class="mb-6">
                        @foreach($roles as $role)
                            <div class="inline-block bg-blue-100 text-blue-800 px-3 py-2 rounded mr-2 mb-2">
                                <p class="font-semibold">{{ ucfirst($role->name) }}</p>
                                <p class="text-sm">{{ $role->description }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 mb-6">No roles assigned.</p>
                @endif

                <h3 class="text-xl font-bold mb-4">Effective Permissions</h3>
                @if($permissions->count())
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($permissions as $permission)
                            <div class="p-3 border border-green-300 bg-green-50 rounded">
                                <p class="font-semibold text-green-800">{{ $permission->name }}</p>
                                <p class="text-sm text-green-700">{{ $permission->description }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No permissions assigned through roles.</p>
                @endif

                @if(auth()->user()->hasPermission('assign_roles'))
                    <div class="mt-8">
                        <a href="{{ route('users.edit-roles', $user) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Manage Roles
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
