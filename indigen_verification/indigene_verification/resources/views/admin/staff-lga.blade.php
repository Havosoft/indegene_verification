@extends('layouts.dashboard')
@section('header', 'Staff LGA Management')
@section('content')

<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Staff LGA Assignment</h2>
            <p class="text-gray-600">Assign Taraba State Local Government Areas (LGAs) to staff members. Staff can only view and manage users from their assigned LGA.</p>
        </div>

        @if(session('status'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Bulk Assignment Section -->
        <div class="mb-8 bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Bulk Taraba LGA Assignment</h3>
            <form action="{{ route('admin.staff-lga.bulk-update') }}" method="POST" id="bulkForm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="bulk_lga" class="block text-sm font-medium text-gray-700 mb-1">Assign LGA</label>
                        <select id="bulk_lga" name="assigned_lga" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Select LGA</option>
                            <option value="Ardo Kola">Ardo Kola</option>
                            <option value="Bali">Bali</option>
                            <option value="Donga">Donga</option>
                            <option value="Gashaka">Gashaka</option>
                            <option value="Gassol">Gassol</option>
                            <option value="Ibi">Ibi</option>
                            <option value="Jalingo">Jalingo</option>
                            <option value="Karim Lamido">Karim Lamido</option>
                            <option value="Kurmi">Kurmi</option>
                            <option value="Lau">Lau</option>
                            <option value="Sardauna">Sardauna</option>
                            <option value="Takum">Takum</option>
                            <option value="Ussa">Ussa</option>
                            <option value="Wukari">Wukari</option>
                            <option value="Yorro">Yorro</option>
                            <option value="Zing">Zing</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Apply to Selected Staff
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Staff List -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current LGA</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned LGA</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($staff as $member)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="staff_ids[]" value="{{ $member->id }}" form="bulkForm" class="staff-checkbox rounded border-gray-300">
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $member->name }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $member->email }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $member->lga ?? 'Not specified' }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    @if($member->assigned_lga)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $member->assigned_lga }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Not assigned
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <button onclick="openEditModal({{ $member->id }}, '{{ $member->name }}', '{{ $member->assigned_lga }}')" 
                                        class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    Edit LGA
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-center text-gray-500">No staff members found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit LGA Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Taraba LGA Assignment</h3>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="edit_staff_name" class="block text-sm font-medium text-gray-700 mb-1">Staff Member</label>
                    <input type="text" id="edit_staff_name" readonly class="w-full p-2 border border-gray-300 rounded-md bg-gray-50">
                </div>
                <div class="mb-4">
                    <label for="edit_assigned_lga" class="block text-sm font-medium text-gray-700 mb-1">Assign LGA</label>
                    <select id="edit_assigned_lga" name="assigned_lga" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Select LGA</option>
                        <option value="Ardo Kola">Ardo Kola</option>
                        <option value="Bali">Bali</option>
                        <option value="Donga">Donga</option>
                        <option value="Gashaka">Gashaka</option>
                        <option value="Gassol">Gassol</option>
                        <option value="Ibi">Ibi</option>
                        <option value="Jalingo">Jalingo</option>
                        <option value="Karim Lamido">Karim Lamido</option>
                        <option value="Kurmi">Kurmi</option>
                        <option value="Lau">Lau</option>
                        <option value="Sardauna">Sardauna</option>
                        <option value="Takum">Takum</option>
                        <option value="Ussa">Ussa</option>
                        <option value="Wukari">Wukari</option>
                        <option value="Yorro">Yorro</option>
                        <option value="Zing">Zing</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.staff-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Edit modal functionality
function openEditModal(staffId, staffName, currentLga) {
    document.getElementById('edit_staff_name').value = staffName;
    document.getElementById('edit_assigned_lga').value = currentLga;
    document.getElementById('editForm').action = `/admin/staff-lga/${staffId}`;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>

@endsection 