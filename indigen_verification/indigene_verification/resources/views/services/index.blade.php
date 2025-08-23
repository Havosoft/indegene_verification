@extends('layouts.dashboard')

@section('content')
<div class="max-w-4xl mx-auto py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-green-900">Service Management</h1>
        <button id="addServiceBtn" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 transition">Add Service</button>
    </div>
    <div id="serviceTableWrapper">
        <table class="min-w-full bg-white rounded shadow">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-2 px-4">Name</th>
                    <th class="py-2 px-4">Price (₦)</th>
                    <th class="py-2 px-4">Active</th>
                    <th class="py-2 px-4">Required</th>
                    <th class="py-2 px-4">Actions</th>
                </tr>
            </thead>
            <tbody id="serviceTableBody">
                @foreach ($services as $service)
                <tr data-id="{{ $service->id }}" class="border-b hover:bg-gray-50">
                    <td class="py-2 px-4">{{ $service->name }}</td>
                    <td class="py-2 px-4">{{ number_format($service->price) }}</td>
                    <td class="py-2 px-4 text-center">
                        <input type="checkbox" class="toggle-active" {{ $service->is_active ? 'checked' : '' }}>
                    </td>
                    <td class="py-2 px-4 text-center">
                        <input type="checkbox" class="toggle-required" {{ $service->is_required ? 'checked' : '' }}>
                    </td>
                    <td class="py-2 px-4 flex gap-2">
                        <button class="editServiceBtn text-blue-600 hover:underline">Edit</button>
                        <button class="deleteServiceBtn text-red-600 hover:underline">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Modal for Add/Edit Service -->
    <div id="serviceModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md relative">
            <button id="closeModalBtn" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">&times;</button>
            <h2 id="modalTitle" class="text-xl font-bold mb-4">Add Service</h2>
            <form id="serviceForm">
                <input type="hidden" name="id" id="serviceId">
                <div class="mb-4">
                    <label class="block text-gray-700">Name</label>
                    <input type="text" name="name" id="serviceName" class="w-full border rounded px-3 py-2 mt-1" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Price (₦)</label>
                    <input type="number" name="price" id="servicePrice" class="w-full border rounded px-3 py-2 mt-1" required min="0">
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="serviceActive" class="form-checkbox">
                        <span>Active</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_required" id="serviceRequired" class="form-checkbox">
                        <span>Required</span>
                    </label>
                </div>
                <div id="formError" class="text-red-600 text-sm mb-2 hidden"></div>
                <button type="submit" class="w-full bg-green-700 text-white py-2 rounded hover:bg-green-800 transition">Save</button>
            </form>
        </div>
    </div>
</div>
<script>
// Helper to show/hide modal
function showModal(edit = false, data = {}) {
    document.getElementById('serviceModal').classList.remove('hidden');
    document.getElementById('modalTitle').textContent = edit ? 'Edit Service' : 'Add Service';
    document.getElementById('serviceId').value = data.id || '';
    document.getElementById('serviceName').value = data.name || '';
    document.getElementById('servicePrice').value = data.price || '';
    document.getElementById('serviceActive').checked = data.is_active ?? true;
    document.getElementById('serviceRequired').checked = data.is_required ?? false;
    document.getElementById('formError').classList.add('hidden');
}
function hideModal() {
    document.getElementById('serviceModal').classList.add('hidden');
}
// Open Add Service Modal
addServiceBtn.onclick = () => showModal(false);
closeModalBtn.onclick = hideModal;
// Edit Service
Array.from(document.getElementsByClassName('editServiceBtn')).forEach(btn => {
    btn.onclick = function() {
        const tr = this.closest('tr');
        showModal(true, {
            id: tr.dataset.id,
            name: tr.children[0].textContent.trim(),
            price: tr.children[1].textContent.replace(/[^\d]/g, ''),
            is_active: tr.querySelector('.toggle-active').checked,
            is_required: tr.querySelector('.toggle-required').checked
        });
    };
});
// AJAX Save (Create/Update)
document.getElementById('serviceForm').onsubmit = async function(e) {
    e.preventDefault();
    const id = serviceId.value;
    const url = id ? `/services/${id}` : '/services';
    const method = id ? 'PUT' : 'POST';
    const payload = {
        name: serviceName.value,
        price: servicePrice.value,
        is_active: serviceActive.checked ? 1 : 0,
        is_required: serviceRequired.checked ? 1 : 0
    };
    try {
        const res = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        });
        if (!res.ok) {
            let errMsg = 'Error saving service.';
            let text = await res.text();
            try {
                const data = JSON.parse(text);
                errMsg = data.message || JSON.stringify(data);
            } catch {
                // If the response is HTML, show a generic message and log the details
                if (text.startsWith('<!DOCTYPE html') || text.startsWith('<html')) {
                    errMsg = 'A server error occurred. Please try again or contact support.';
                    console.error('Server error:', text);
                } else {
                    errMsg = text;
                }
            }
            document.getElementById('formError').textContent = errMsg;
            document.getElementById('formError').classList.remove('hidden');
            return;
        }
        location.reload();
    } catch (err) {
        document.getElementById('formError').textContent = err.message || 'Error saving service.';
        document.getElementById('formError').classList.remove('hidden');
    }
};
// Delete Service
Array.from(document.getElementsByClassName('deleteServiceBtn')).forEach(btn => {
    btn.onclick = async function() {
        if (!confirm('Delete this service?')) return;
        const tr = this.closest('tr');
        const id = tr.dataset.id;
        try {
            const res = await fetch(`/services/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            if (!res.ok) throw await res.json();
            location.reload();
        } catch (err) {
            alert(err.message || 'Error deleting service.');
        }
    };
});
// Toggle Active/Required
Array.from(document.getElementsByClassName('toggle-active')).forEach(cb => {
    cb.onchange = async function() {
        const tr = this.closest('tr');
        const id = tr.dataset.id;
        try {
            await fetch(`/services/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ is_active: this.checked ? 1 : 0 })
            });
        } catch {}
    };
});
Array.from(document.getElementsByClassName('toggle-required')).forEach(cb => {
    cb.onchange = async function() {
        const tr = this.closest('tr');
        const id = tr.dataset.id;
        try {
            await fetch(`/services/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ is_required: this.checked ? 1 : 0 })
            });
        } catch {}
    };
});
</script>
@endsection 