@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-6">Users Management</h2>

                @if($users->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Name</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Email</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Roles</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Joined</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-2 font-semibold">{{ $user->name }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $user->email }}</td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            @if($user->roles->count())
                                                @foreach($user->roles as $role)
                                                    <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm mr-1">{{ ucfirst($role->name) }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-gray-500 italic">No roles assigned</span>
                                            @endif
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $user->created_at->format('M d, Y') }}</td>
                                        <td class="border border-gray-300 px-4 py-2 text-center">
                                            <a href="{{ route('users.show', $user) }}" class="text-blue-500 hover:underline text-sm">View</a>
                                            @if(auth()->user()->hasPermission('assign_roles'))
                                                | <a href="{{ route('users.edit-roles', $user) }}" class="text-green-500 hover:underline text-sm">Manage Roles</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No users found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
