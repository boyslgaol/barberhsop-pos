<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function __construct()
{
    $this->middleware(function ($request, $next) {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'barber'])) {
            abort(403, 'Hanya admin dan barber yang dapat mengakses halaman ini.');
        }
        return $next($request);
    });
}
    /**
     * Display a listing of services
     */
    public function index()
    {
        $services = Service::with('category')
            ->orderBy('category_id')
            ->orderBy('name')
            ->paginate(15);
        
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        return view('services.index', compact('services', 'categories'));
    }
    
    /**
     * Show form for creating new service
     */
    public function create()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        return view('services.create', compact('categories'));
    }
    
    /**
     * Store a newly created service
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:5',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        
        $service = Service::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'code' => Service::generateCode(),
            'price' => $request->price,
            'duration' => $request->duration,
            'description' => $request->description,
            'is_active' => $request->has('is_active')
        ]);
        
        return redirect()->route('services.index')
            ->with('success', 'Layanan "' . $service->name . '" berhasil ditambahkan');
    }
    
    /**
     * Display specified service
     */
    public function show(Service $service)
    {
        return view('services.show', compact('service'));
    }
    
    /**
     * Show form for editing service
     */
    public function edit(Service $service)
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        return view('services.edit', compact('service', 'categories'));
    }
    
    /**
     * Update specified service
     */
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:5',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        
        $service->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'duration' => $request->duration,
            'description' => $request->description,
            'is_active' => $request->has('is_active')
        ]);
        
        return redirect()->route('services.index')
            ->with('success', 'Layanan "' . $service->name . '" berhasil diperbarui');
    }
    
    /**
     * Remove specified service
     */
public function destroy(Service $service)
{
    // Check if service can be deleted
    if (!$service->canDelete()) {
        if (request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan tidak dapat dihapus karena sudah digunakan dalam transaksi'
            ], 400);
        }
        return redirect()->route('services.index')
            ->with('error', 'Layanan tidak dapat dihapus karena sudah digunakan dalam transaksi');
    }
    
    $serviceName = $service->name;
    $service->delete();
    
    if (request()->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Layanan "' . $serviceName . '" berhasil dihapus'
        ]);
    }
    
    return redirect()->route('services.index')
        ->with('success', 'Layanan "' . $serviceName . '" berhasil dihapus');
}
    
    /**
     * Toggle service status
     */
    public function toggleStatus(Service $service)
{
    $service->update(['is_active' => !$service->is_active]);
    
    $status = $service->is_active ? 'diaktifkan' : 'dinonaktifkan';
    
    if (request()->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Layanan "' . $service->name . '" berhasil ' . $status,
            'is_active' => $service->is_active
        ]);
    }
    
    return redirect()->back()
        ->with('success', 'Layanan "' . $service->name . '" berhasil ' . $status);
}

}