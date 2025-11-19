<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Voucher::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $vouchers = $query->paginate(15);

        return view('admin.vouchers.index', compact('vouchers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.vouchers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Convert checkbox values before validation
        $request->merge([
            'is_active' => $request->has('is_active') ? true : false,
            'is_public' => $request->has('is_public') ? true : false,
        ]);

        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'name' => 'nullable|string|max:255',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_order' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0|required_if:type,percentage',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'is_active' => 'required|boolean',
            'is_public' => 'required|boolean',
            'description' => 'nullable|string',
        ]);

        $data = $request->only([
            'code', 'name', 'type', 'value', 'min_order', 'max_discount',
            'usage_limit', 'valid_from', 'valid_until', 'description',
            'is_active', 'is_public'
        ]);
        $data['used_count'] = 0;
        $data['min_order'] = $data['min_order'] ?? 0;

        Voucher::create($data);

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Tạo voucher thành công!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $voucher = Voucher::findOrFail($id);
        return view('admin.vouchers.show', compact('voucher'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $voucher = Voucher::findOrFail($id);
        return view('admin.vouchers.edit', compact('voucher'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);

        // Convert checkbox values before validation
        $request->merge([
            'is_active' => $request->has('is_active') ? true : false,
            'is_public' => $request->has('is_public') ? true : false,
        ]);

        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $id,
            'name' => 'nullable|string|max:255',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_order' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0|required_if:type,percentage',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'is_active' => 'required|boolean',
            'is_public' => 'required|boolean',
            'description' => 'nullable|string',
        ]);

        $data = $request->only([
            'code', 'name', 'type', 'value', 'min_order', 'max_discount',
            'usage_limit', 'valid_from', 'valid_until', 'description',
            'is_active', 'is_public'
        ]);
        $data['min_order'] = $data['min_order'] ?? 0;

        $voucher->update($data);

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Cập nhật voucher thành công!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Xóa voucher thành công!');
    }
}
