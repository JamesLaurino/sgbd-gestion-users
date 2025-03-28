<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy("created_at","DESC")->paginate(10);
        return view('users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:4',
            'role' => 'required|in:user,admin',
        ]);

        User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'role' => $validatedData['role'],
        ]);

        return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return view('users.show',["user" => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('users.edit',["user" => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required',
            'password' => 'required|string|min:4',
            'role' => 'required|in:user,admin',
        ]);

        $user = User::where('email', $validatedData['email'])->first();
        $user->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'role' => $validatedData['role'],
        ]);

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')->with('danger', 'Utilisateur supprimé avec succés.');
    }

    public function search(Request $request) {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $users = [];
        $user = User::where('email',$validatedData['name'])
            ->orWhere('name',$validatedData['name'])
            ->first();

        if ($user) {
            array_push($users, $user);
        }

        // Convertir le tableau en collection Laravel
        $usersCollection = new Collection($users);


        $perPage = 5; // Nombre d'éléments par page
        $page = request()->get('page', 1); // Récupérer le numéro de page, par défaut 1
        $offset = ($page - 1) * $perPage;


        $paginatedUsers = new LengthAwarePaginator(
            $usersCollection->slice($offset, $perPage)->values(),
            $usersCollection->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('users.index', ['users' => $paginatedUsers]);

    }
}
