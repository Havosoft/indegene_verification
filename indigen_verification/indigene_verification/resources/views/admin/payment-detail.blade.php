@extends('layouts.dashboard')
@section('title', 'Payment Detail')
@section('header', 'Payment Detail')
@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <a href="{{ $backUrl ?? route('admin.user-payments') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition font-semibold mb-6">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        {{ $backUrl ? 'Back to Activity Detail' : 'Back to User Management' }}
    </a>
    <h1 class="text-2xl font-bold text-green-900 mb-4">Service Payment Detail</h1>
    <div class="bg-white rounded shadow p-8 mb-6">
        <h2 class="text-lg font-semibold mb-4">User Information</h2>
        <div class="mb-2"><span class="font-semibold">Name:</span> {{ $user->name }}</div>
        <div class="mb-2"><span class="font-semibold">Email:</span> {{ $user->email }}</div>
        <div class="mb-2"><span class="font-semibold">Phone:</span> {{ $user->phone }}</div>
        <div class="mb-2"><span class="font-semibold">Date of Birth:</span> {{ $user->date_of_birth }}</div>
        <div class="mb-2"><span class="font-semibold">Gender:</span> {{ ucfirst($user->gender) }}</div>
        <div class="mb-2"><span class="font-semibold">LGA:</span> {{ $user->lga }}</div>
        <div class="mb-2"><span class="font-semibold">Address:</span> {{ $user->address }}</div>
        <div class="mb-2"><span class="font-semibold">NIN:</span> {{ $user->nin }}</div>
    </div>
    <div class="bg-white rounded shadow p-8 mb-6">
        <h2 class="text-lg font-semibold mb-4">Service Payment</h2>
        <div class="mb-2"><span class="font-semibold">Service:</span> {{ $payment->service_name }}</div>
        <div class="mb-2"><span class="font-semibold">Amount Paid:</span> ₦{{ number_format($payment->price) }}</div>
        <div class="mb-2"><span class="font-semibold">Payment Ref:</span> {{ $payment->payment_ref }}</div>
        <div class="mb-2"><span class="font-semibold">Date:</span> {{ $payment->created_at->format('Y-m-d H:i') }}</div>
        <div class="mb-2"><span class="font-semibold">Status:</span> 
            @if($payment->approved === 'approved')
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded">Approved</span>
            @elseif($payment->approved === 'not_approved')
                <span class="px-3 py-1 bg-red-100 text-red-800 rounded">Not Approved</span>
            @else
                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded">Awaiting Review</span>
            @endif
        </div>
        <div class="mb-2"><span class="font-semibold">Certificate/File:</span>
            @if($payment->file)
                <a href="{{ asset('storage/' . $payment->file) }}" target="_blank" class="text-green-700 underline">Download/View</a>
            @else
                <span class="text-gray-500">No file uploaded</span>
            @endif
        </div>
    </div>
    @if(isset($editable) && $editable)
    <div class="bg-white rounded shadow p-8 mb-6">
        <form method="POST" action="{{ route('admin.user.payment.approval', ['user' => $user->id, 'payment' => $payment->id]) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="font-semibold">Approval Action:</label>
                <select name="approved" id="admin-approval-select" class="form-select mt-1 block w-full border-gray-300 rounded" required>
                    <option value="" disabled {{ $payment->approved === 'awaiting_review' ? 'selected' : '' }}>Select Status</option>
                    <option value="approved" {{ $payment->approved === 'approved' ? 'selected' : '' }}>Approve</option>
                    <option value="not_approved" {{ $payment->approved === 'not_approved' ? 'selected' : '' }}>Not Approve</option>
                </select>
            </div>
            <div id="admin-disapprove-reason-area" class="mb-4 hidden">
                <label for="description" class="block font-semibold mb-1">Reason for Not Approving <span class="text-red-600">*</span>:</label>
                <textarea name="description" id="admin-description" rows="4" class="w-full border rounded px-3 py-2"></textarea>
                <span id="admin-disapprove-error" class="text-red-600 text-sm hidden">Please provide a reason for not approving.</span>
            </div>
            <div class="mb-4">
                <label class="font-semibold">Upload Certificate/File <span class="text-red-600">*</span>:</label>
                <div class="text-xs text-blue-700 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Maximum file size: 5MB. PDF, JPG, PNG, DOC, DOCX allowed. <span class="text-red-600 font-semibold">File upload is required before updating approval status.</span>
                </div>
                <input type="file" name="file" id="admin-file-upload" class="block mt-1 w-full border-gray-300 rounded" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                @if($payment->file)
                    <div class="mt-2">
                        <span class="text-xs text-gray-600">Current file:</span>
                        <a href="{{ asset('storage/' . $payment->file) }}" target="_blank" class="text-green-700 underline">Download/View</a>
                    </div>
                @endif
            </div>
            <button type="submit" class="bg-green-700 text-white px-6 py-2 rounded hover:bg-green-800 transition">Update Approval</button>
            @if(session('status'))
                <div class="mt-4 text-green-700 font-semibold">{{ session('status') }}</div>
            @endif
        </form>
        <script>
        const adminApprovalSelect = document.getElementById('admin-approval-select');
        const adminDisapproveArea = document.getElementById('admin-disapprove-reason-area');
        const adminDescriptionInput = document.getElementById('admin-description');
        const adminForm = document.querySelector('form[action*="user.payment.approval"]');
        const adminErrorMsg = document.getElementById('admin-disapprove-error');
        const adminFileUpload = document.getElementById('admin-file-upload');
        function toggleAdminDisapproveArea() {
            if (adminApprovalSelect.value === 'not_approved') {
                adminDisapproveArea.classList.remove('hidden');
                adminDescriptionInput.required = true;
                adminFileUpload.required = false;
            } else if (adminApprovalSelect.value === 'approved') {
                adminDisapproveArea.classList.add('hidden');
                adminDescriptionInput.required = false;
                adminFileUpload.required = true;
                adminErrorMsg.classList.add('hidden');
            } else {
                adminDisapproveArea.classList.add('hidden');
                adminDescriptionInput.required = false;
                adminFileUpload.required = false;
                adminErrorMsg.classList.add('hidden');
            }
        }
        adminApprovalSelect.addEventListener('change', toggleAdminDisapproveArea);
        adminForm.addEventListener('submit', function(e) {
            if (adminApprovalSelect.value === 'not_approved' && !adminDescriptionInput.value.trim()) {
                adminErrorMsg.classList.remove('hidden');
                adminDescriptionInput.focus();
                e.preventDefault();
            }
        });
        // Initialize on page load
        toggleAdminDisapproveArea();
        </script>
    </div>
    @endif
</div>
@endsection 