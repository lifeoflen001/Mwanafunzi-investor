<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $term = trim((string) ($data['q'] ?? ''));

        $administrators = User::query()
            ->where('is_admin', true)
            ->when($term !== '', fn ($query) => $query->where(fn ($search) => $search->where('name', 'like', '%'.$term.'%')->orWhere('email', 'like', '%'.$term.'%')))
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.administrators.index', compact('administrators', 'term'));
    }

    public function create()
    {
        return view('admin.administrators.form', ['administrator' => new User(['status' => 'active']), 'isCreate' => true]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['is_admin'] = true;
        $administrator = User::create($data);
        AdminAudit::record('administrator.created', 'Created administrator '.$administrator->email, $administrator);

        return to_route('admin.administrators.edit', $administrator)->with('success', 'Administrator created.');
    }

    public function edit(User $administrator)
    {
        abort_unless($administrator->is_admin, 404);

        return view('admin.administrators.form', ['administrator' => $administrator, 'isCreate' => false]);
    }

    public function update(Request $request, User $administrator)
    {
        abort_unless($administrator->is_admin, 404);
        $data = $this->validated($request, $administrator);

        if (($data['status'] ?? 'active') !== 'active' && $administrator->status === 'active' && User::where('is_admin', true)->where('status', 'active')->count() <= 1) {
            return back()->withErrors(['status' => 'The final active administrator cannot be suspended. Add another active administrator first.'])->withInput();
        }

        $administrator->update($data);
        AdminAudit::record('administrator.updated', 'Updated administrator '.$administrator->email, $administrator);

        return back()->with('success', 'Administrator updated.');
    }

    private function validated(Request $request, ?User $administrator = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($administrator?->id)],
            'status' => ['required', Rule::in(['active', 'suspended'])],
            'password' => [$administrator ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ]);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        return $data;
    }
}
