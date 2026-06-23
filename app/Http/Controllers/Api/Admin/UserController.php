<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
//sshow all user
public function index(Request $request)
    {
        $users = User::query()
            ->when($request->role, fn ($q, $r) => $q->where('role', $r))
            ->when($request->search, fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return UserResource::collection($users);
    }

    public function deactivate(User $user)
    {
        abort_unless(auth()->user()->canManage($user), 403);

        $user->update(['status' => 'deactivated']);

        return (new UserResource($user->fresh()))->additional([
            'message' => 'User deactivated successfully.',
        ]);
    }

    public function activate(User $user)
    {
        abort_unless(auth()->user()->canManage($user), 403);

        $user->update(['status' => 'active']);

        return (new UserResource($user->fresh()))->additional([
            'message' => 'User activated successfully.',
        ]);
    }
}
