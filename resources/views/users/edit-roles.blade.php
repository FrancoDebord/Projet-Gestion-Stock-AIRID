@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-6">Manage Roles for {{ $user->name }}</h2>

                <form action="{{ route('users.update-roles', $user) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-3">
                        @foreach($roles as $role)
                            <label class="flex items-center p-4 border border-gray-300 rounded cursor-pointer hover:bg-gray-50">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                    @if(in_array($role->id, $userRoles)) checked @endif
                                    class="mr-3 w-4 h-4">
                                <div>
                                    <p class="font-semibold">{{ ucfirst($role->name) }}</p>
                                    <p class="text-sm text-gray-600">{{ $role->description }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-8 p-4 bg-blue-50 border border-blue-300 rounded">
                        <h3 class="font-bold text-blue-900 mb-2">Permissions for Selected Roles:</h3>
                        <div id="permissions-list" class="text-sm text-blue-900">
                            <p>Select roles above to see permissions...</p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('users.show', $user) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700">Update Roles</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const rolesData = {!! json_encode($roles->map(fn($r) => [
    'id' => $r->id,
    'name' => $r->name,
    'permissions' => $r->permissions->map(fn($p) => $p->name)->toArray()
])->toArray()) !!};

document.querySelectorAll('input[type="checkbox"][name="roles[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', updatePermissions);
});

function updatePermissions() {
    const checkedRoles = Array.from(document.querySelectorAll('input[type="checkbox"][name="roles[]"]:checked'))
        .map(cb => cb.value);
    
    const allPermissions = new Set();
    rolesData.forEach(role => {
        if (checkedRoles.includes(String(role.id))) {
            role.permissions.forEach(perm => allPermissions.add(perm));
        }
    });

    const permList = document.getElementById('permissions-list');
    if (allPermissions.size === 0) {
        permList.innerHTML = '<p>No permissions assigned.</p>';
    } else {
        permList.innerHTML = '<ul class="list-disc list-inside">' + 
            Array.from(allPermissions).map(p => `<li>${p}</li>`).join('') + 
            '</ul>';
    }
}

updatePermissions();
</script>
@endsection
