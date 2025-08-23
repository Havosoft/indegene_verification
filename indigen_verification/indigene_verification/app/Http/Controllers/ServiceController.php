<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Validation\ValidationException;
use Throwable;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $services = Service::orderBy('name')->get();
        if ($request->wantsJson()) {
            return response()->json($services);
        }
        return view('services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:services,name',
                'price' => 'required|integer|min:0',
                'is_active' => 'boolean',
                'is_required' => 'boolean',
            ]);
            $service = Service::create([
                'name' => $validated['name'],
                'price' => $validated['price'],
                'is_active' => $request->boolean('is_active'),
                'is_required' => $request->boolean('is_required'),
            ]);
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'service' => $service], 201);
            }
            return redirect()->route('services.index');
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:services,name,' . $service->id,
                'price' => 'sometimes|required|integer|min:0',
                'is_active' => 'sometimes|boolean',
                'is_required' => 'sometimes|boolean',
            ]);
            $service->fill($validated);
            if ($request->has('is_active')) $service->is_active = $request->boolean('is_active');
            if ($request->has('is_required')) $service->is_required = $request->boolean('is_required');
            $service->save();
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'service' => $service]);
            }
            return redirect()->route('services.index');
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Service $service)
    {
        $service->delete();
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Deleted']);
        }
        return redirect()->route('services.index');
    }
}
