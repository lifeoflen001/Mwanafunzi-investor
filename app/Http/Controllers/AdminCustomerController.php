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
            ->with('socialIdentities')
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

    public function create()
    {
        return view('admin.customers.form', [
            'customer' => new User(['status' => 'active']),
            'isCreate' => true,
        ]);
    }

    public function store(Request $request)
    {
        $customer = User::create($this->validated($request));
        AdminAudit::record('customer.created', 'Created customer '.$customer->email, $customer);

        return to_route('admin.customers.show', $customer)->with('success', 'Customer account created.');
    }

    public function show(User $customer)
    {
        abort_unless(! $customer->is_admin, 404);

        $customer->load([
            'socialIdentities',
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

    public function edit(User $customer)
    {
        abort_unless(! $customer->is_admin, 404);

        return view('admin.customers.form', [
            'customer' => $customer,
            'isCreate' => false,
        ]);
    }

    public function update(Request $request, User $customer)
    {
        abort_unless(! $customer->is_admin, 404);

        $customer->update($this->validated($request, $customer));
        AdminAudit::record('customer.updated', 'Updated customer '.$customer->email, $customer);

        return to_route('admin.customers.show', $customer)->with('success', 'Customer account updated.');
    }

    public function updateStatus(Request $request, User $customer)
    {
        abort_unless(! $customer->is_admin, 404);

        $data = $request->validate(['status' => ['required', Rule::in(['active', 'suspended'])]]);
        $customer->update(['status' => $data['status']]);
        AdminAudit::record('customer.status_updated', 'Updated customer status for '.$customer->email, $customer, ['status' => $data['status']]);

        return back()->with('success', 'Customer account status updated.');
    }

    public function destroy(User $customer)
    {
        abort_unless(! $customer->is_admin, 404);

        if ($customer->orders()->exists() || $customer->enrollments()->exists() || $customer->entitlements()->exists()) {
            return back()->withErrors(['customer' => 'This customer has purchase or learning records. Suspend the account instead of deleting it so those records remain intact.']);
        }

        $email = $customer->email;
        AdminAudit::record('customer.deleted', 'Deleted customer '.$email, $customer);
        $customer->delete();

        return to_route('admin.customers')->with('success', 'Customer account deleted.');
    }

    private function validated(Request $request, ?User $customer = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($customer?->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'country' => ['nullable', 'string', 'size:2'],
            'status' => ['required', Rule::in(['active', 'suspended'])],
            'password' => [$customer ? 'nullable' : 'required', 'string', 'min:10', 'confirmed'],
        ]);

        $data['country'] = filled($data['country'] ?? null) ? strtoupper($data['country']) : null;

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $data['is_admin'] = false;

        return $data;
    }
}
