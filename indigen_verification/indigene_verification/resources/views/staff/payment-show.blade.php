@extends('layouts.dashboard')
@section('header', 'Service Payment Detail')
@section('content')
<div class="mb-6">
    <a href="{{ route('staff.users') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition font-semibold">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to User List
    </a>
</div>
<div class="max-h-[calc(100vh-100px)] overflow-y-auto p-2 md:p-4 bg-transparent">
    <div class="max-w-2xl mx-auto py-4">
        <h1 class="text-2xl font-bold text-green-900 mb-6">Service Payment Detail</h1>
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
        </div>
        <form method="POST" action="{{ route('staff.certificates.approve', $payment->id) }}" class="bg-white rounded shadow p-8" enctype="multipart/form-data" id="approval-form">
            @csrf
            <div class="mb-4">
                <label class="font-semibold">Approval Action:</label>
                <select name="approved" id="staff-approval-select" class="form-select mt-1 block w-full border-gray-300 rounded" required>
                    <option value="" disabled {{ $payment->approved === 'awaiting_review' ? 'selected' : '' }}>Select Status</option>
                    <option value="approved" {{ $payment->approved === 'approved' ? 'selected' : '' }}>Approve</option>
                    <option value="not_approved" {{ $payment->approved === 'not_approved' ? 'selected' : '' }}>Not Approve</option>
                </select>
            </div>
            <div id="staff-disapprove-reason-area" class="mb-4 hidden">
                <label for="description" class="block font-semibold mb-1">Reason for Not Approving <span class="text-red-600">*</span>:</label>
                <textarea name="description" id="staff-description" rows="4" class="w-full border rounded px-3 py-2"></textarea>
                <span id="staff-disapprove-error" class="text-red-600 text-sm hidden">Please provide a reason for not approving.</span>
            </div>
            <div class="mb-4">
                <label class="font-semibold">Upload Certificate/File <span class="text-red-600">*</span>:</label>
                <div class="text-xs text-blue-700 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Maximum file size: 5MB. PDF, JPG, PNG, DOC, DOCX allowed. <span class="text-red-600 font-semibold">File upload is required before updating approval status.</span>
                </div>
                <input type="file" name="file" id="staff-file-upload" class="block mt-1 w-full border-gray-300 rounded" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
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
        const staffApprovalSelect = document.getElementById('staff-approval-select');
        const staffDisapproveArea = document.getElementById('staff-disapprove-reason-area');
        const staffDescriptionInput = document.getElementById('staff-description');
        const staffForm = document.getElementById('approval-form');
        const staffErrorMsg = document.getElementById('staff-disapprove-error');
        const staffFileUpload = document.getElementById('staff-file-upload');
        function toggleStaffDisapproveArea() {
            if (staffApprovalSelect.value === 'not_approved') {
                staffDisapproveArea.classList.remove('hidden');
                staffDescriptionInput.required = true;
                staffFileUpload.required = false;
            } else if (staffApprovalSelect.value === 'approved') {
                staffDisapproveArea.classList.add('hidden');
                staffDescriptionInput.required = false;
                staffFileUpload.required = true;
                staffErrorMsg.classList.add('hidden');
            } else {
                staffDisapproveArea.classList.add('hidden');
                staffDescriptionInput.required = false;
                staffFileUpload.required = false;
                staffErrorMsg.classList.add('hidden');
            }
        }
        staffApprovalSelect.addEventListener('change', toggleStaffDisapproveArea);
        staffForm.addEventListener('submit', function(e) {
            if (staffApprovalSelect.value === 'not_approved' && !staffDescriptionInput.value.trim()) {
                staffErrorMsg.classList.remove('hidden');
                staffDescriptionInput.focus();
                e.preventDefault();
            }
        });
        // Initialize on page load
        toggleStaffDisapproveArea();
        </script>
    </div>
</div>
@endsection 