<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserService
{
    public function getAllUsers(Request $request)
    {
        if (auth()->user()->is_admin !== 'admin') {
            throw new Exception('Unauthorized: You do not have permission to view all users.', 403);
        }

        $email = $request->query('email');
        $itemsPerPage = $request->query('items_per_page', 15);

        return User::filterByEmail($email)
            ->orderBy('email', 'DESC')
            ->paginate($itemsPerPage);
    }

    public function createUser(array $data)
    {
        return User::create($data);
    }

    public function getUser(User $user)
    {
        $currentUser = auth()->user();
        if ($currentUser->is_admin !== 'admin' && $currentUser->id !== $user->id) {
            throw new Exception('Unauthorized: You do not have permission to view this user.', 403);
        }

        return $user;
    }

    public function updateUser(User $user, array $data)
    {
        $user->update($data);
        return $user;
    }

    public function deleteUser(User $user)
    {
        $currentUser = auth()->user();
        if ($currentUser->is_admin !== 'admin' && $currentUser->id !== $user->id) {
            throw new Exception('Unauthorized: You do not have permission to delete this user.', 403);
        }

        $user->delete();
    }
}
