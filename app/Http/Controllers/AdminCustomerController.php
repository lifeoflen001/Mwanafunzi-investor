<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminCustomerController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['active', 'suspended'])],
        ]);
        $term = trim((string) ($data['q'] ?? ''));

        $customers = User::query()
            ->where('is_admin', false)
            ->withCount(['orders', 'enrollments', 'entitlements'])
            ->when($term !== '', fn ($query) => $query->where(fn ($search) => $search
                ->where('name', 'like', '%'.$term.'%')
                ->orWhere('email', 'like', '%'.$term.'%')
                ->orWhere('phone', 'like', '%'.$term.'%')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $data['status']))
            ->latest('updated_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.customers.index', compact('customers', 'term'));
    }

    public function show(User $customer)
    {
        abort_unless(! $customer->is_admin, 404);

        $customer->load([
            'orders' => fn ($query) => $query->withCount('items')->latest()->limit(10),
            'enrollments.course',
            'entitlements.product',
            'entitlements.course',
            'entitlements.downloads',
            'waitlists.course',
        ]);
        $customer->loadCount(['orders', 'enrollments', 'entitlements', 'waitlists']);
        $downloadCount = $customer->entitlements->sum(fn ($entitlement) => $entitlement->downloads->count());

        return view('admin.customers.show', compact('customer', 'downloadCount'));
    }

    public function updateStatus(Request $request, User $customer)
    {
        abort_unless(! $customer->is_admin, 404);

        $data = $request->validate(['status' => ['required', Rule::in(['active', 'suspended'])]]);
        $customer->update(['status' => $data['status']]);
        AdminAudit::record('customer.status_updated', 'Updated customer status for '.$customer->email, $customer, ['status' => $data['status']]);

        return back()->with('success', 'Customer account status updated.');
    }
}
