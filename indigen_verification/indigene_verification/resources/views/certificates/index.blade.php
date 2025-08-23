@extends('layouts.dashboard')

@section('title', 'Downloaded Certificates')
@section('header', 'Downloaded Certificates')

@section('content')
<div class="max-w-5xl mx-auto py-10">
    <h1 class="text-2xl font-bold text-green-900 mb-6">Downloaded Certificates</h1>
    <div class="bg-white rounded shadow p-6">
        <table class="w-full min-w-[900px] text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <th class="py-3 px-4">Certificate</th>
                    <th class="py-3 px-4 text-left">Price (₦)</th>
                    <th class="py-3 px-4">Payment Ref</th>
                    <th class="py-3 px-4">Date</th>
                    <th class="py-3 px-4">Preview</th>
                    <th class="py-3 px-4">Download</th>
                </tr>
            </thead>
            <tbody>
                @forelse($certificates as $cert)
                <tr class="border-b last:border-b-0">
                    <td class="py-3 px-4 font-medium text-gray-900">{{ $cert->service_name }}</td>
                    <td class="py-3 px-4 text-left text-gray-700">₦{{ number_format($cert->price) }}</td>
                    <td class="py-3 px-4">{{ $cert->payment_ref }}</td>
                    <td class="py-3 px-4">{{ $cert->created_at->format('Y-m-d H:i') }}</td>
                    <td class="py-3 px-4 text-center">
                        @if($cert->approved && $cert->file)
                            @php $ext = strtolower(pathinfo($cert->file, PATHINFO_EXTENSION)); @endphp
                            @if(in_array($ext, ['pdf']))
                                <a href="{{ route('certificates.download', $cert->id) }}" target="_blank" class="text-blue-700 underline">Preview</a>
                            @elseif(in_array($ext, ['jpg','jpeg','png','gif']))
                                <a href="{{ route('certificates.download', $cert->id) }}" target="_blank">
                                    <img src="{{ route('certificates.download', $cert->id) }}" alt="Preview" class="h-10 inline-block rounded shadow" />
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">N/A</span>
                            @endif
                        @else
                            <span class="text-gray-400 text-xs">N/A</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-center">
                        @if($cert->approved && $cert->file)
                            <a href="{{ route('certificates.download', $cert->id) }}" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 transition">Download</a>
                        @elseif($cert->approved)
                            <span class="text-yellow-600 text-xs">No file uploaded</span>
                        @else
                            <button class="bg-gray-300 text-gray-500 px-4 py-2 rounded cursor-not-allowed" disabled>Pending Approval</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-6 px-4 text-center text-gray-500">No certificates downloaded yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection 