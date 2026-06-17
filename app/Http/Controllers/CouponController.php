<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:coupon list')->only(['index']);
        $this->middleware('can:coupon create')->only(['create', 'store']);
        $this->middleware('can:coupon edit')->only(['edit', 'update']);
        $this->middleware('can:coupon delete')->only(['destroy']);
    }

    public function index()
    {
        $coupons = Coupon::latest()->get();

        return view('backend.coupon.index', compact('coupons'));
    }

    public function create()
    {
        return view('backend.coupon.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'type' => ['required', Rule::in(['fixed', 'percent'])],
            'value' => 'required|numeric|min:0',
            'expires_at' => 'nullable|date',
            'max_uses' => 'nullable|integer|min:1',
        ]);

        // Convert code to uppercase for consistency
        $validatedData['code'] = strtoupper($validatedData['code']);

        Coupon::create($validatedData);

        return redirect()->route('coupon.index')->with('success', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon)
    {
        return view('backend.coupon.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|unique:coupons,code,'.$coupon->id,
            'type' => ['required', Rule::in(['fixed', 'percent'])],
            'value' => 'required|numeric|min:0',
            'expires_at' => 'nullable|date',
            'max_uses' => 'nullable|integer|min:1',
        ]);

        $validatedData['code'] = strtoupper($validatedData['code']);

        $coupon->update($validatedData);

        return redirect()->route('coupon.index')->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('coupon.index')->with('success', 'Coupon deleted successfully.');
    }

    public function apply(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string']);

        $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();

        if (! $coupon) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Invalid coupon code. Please try again.']);
            }

            return back()->withErrors(['message' => 'Invalid coupon code. Please try again.']);
        }

        if ($coupon->expires_at && $coupon->expires_at < Carbon::now()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'This coupon has expired.']);
            }

            return back()->withErrors(['message' => 'This coupon has expired.']);
        }

        if ($coupon->max_uses !== null && $coupon->uses >= $coupon->max_uses) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'This coupon has reached its usage limit.']);
            }

            return back()->withErrors(['message' => 'This coupon has reached its usage limit.']);
        }

        session(['coupon' => [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
        ]]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Coupon applied successfully!',
                'coupon' => [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                ],
            ]);
        }

        return back()->with('success', 'Coupon applied successfully!');
    }

    public function remove(Request $request)
    {
        session()->forget('coupon');

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Coupon has been removed.']);
        }

        return back()->with('success', 'Coupon has been removed.');
    }
}
