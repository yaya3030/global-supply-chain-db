<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Port;
use App\Models\Article;
use App\Models\Country;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Exception;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalPorts = Port::count();
        $totalArticles = Article::count();
        $recentUsers = User::latest()->take(5)->get();

        return view('admin_dashboard', compact(
            'totalUsers',
            'totalPorts',
            'totalArticles',
            'recentUsers'
        ));
    }

    // API: Get Overview Stats & Recent Users
    public function getOverview()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_users' => User::count(),
                'total_ports' => Port::count(),
                'total_articles' => Article::count(),
                'recent_users' => User::latest()->take(5)->get()->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'created_at' => $user->created_at ? $user->created_at->format('d M Y') : '-'
                    ];
                })
            ]
        ]);
    }

    // === USERS MANAGEMENT ===
    public function getUsers()
    {
        $users = User::latest()->get()->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at ? $user->created_at->format('d M Y H:i') : '-'
            ];
        });
        return response()->json(['status' => 'success', 'data' => $users]);
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:user,admin',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return response()->json(['status' => 'success', 'message' => 'Pengguna berhasil ditambahkan!', 'data' => $user]);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'role' => 'required|in:user,admin',
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return response()->json(['status' => 'success', 'message' => 'Pengguna berhasil diperbarui!', 'data' => $user]);
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak dapat menghapus akun Anda sendiri!'], 400);
        }
        $user->delete();
        return response()->json(['status' => 'success', 'message' => 'Pengguna berhasil dihapus!']);
    }

    // === PORTS MANAGEMENT ===
    public function getPorts()
    {
        $ports = Port::with('country')->latest()->get()->map(function($port) {
            return [
                'id' => $port->id,
                'port_name' => $port->port_name,
                'port_code' => $port->port_code,
                'country_id' => $port->country_id,
                'country_name' => $port->country ? $port->country->name : 'N/A',
                'latitude' => $port->latitude,
                'longitude' => $port->longitude,
                'created_at' => $port->created_at ? $port->created_at->format('d M Y') : '-'
            ];
        });
        return response()->json(['status' => 'success', 'data' => $ports]);
    }

    public function storePort(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'port_name' => 'required|string|max:150',
            'port_code' => 'nullable|string|max:10',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $port = Port::create($validated);
        return response()->json(['status' => 'success', 'message' => 'Pelabuhan berhasil ditambahkan!', 'data' => $port]);
    }

    public function updatePort(Request $request, $id)
    {
        $port = Port::findOrFail($id);

        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'port_name' => 'required|string|max:150',
            'port_code' => 'nullable|string|max:10',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $port->update($validated);
        return response()->json(['status' => 'success', 'message' => 'Pelabuhan berhasil diperbarui!', 'data' => $port]);
    }

    public function destroyPort($id)
    {
        $port = Port::findOrFail($id);
        $port->delete();
        return response()->json(['status' => 'success', 'message' => 'Pelabuhan berhasil dihapus!']);
    }

    // === ARTICLES MANAGEMENT ===
    public function getArticles()
    {
        $articles = Article::with('user')->latest()->get()->map(function($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'content' => $article->content,
                'author_name' => $article->user ? $article->user->name : 'Admin',
                'created_at' => $article->created_at ? $article->created_at->format('d M Y') : '-'
            ];
        });
        return response()->json(['status' => 'success', 'data' => $articles]);
    }

    public function storeArticle(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'slug' => 'nullable|string|max:255',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        
        $originalSlug = $slug;
        $count = 1;
        while (Article::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $article = Article::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
        ]);

        return response()->json(['status' => 'success', 'message' => 'Artikel berhasil diterbitkan!', 'data' => $article]);
    }

    public function updateArticle(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'slug' => 'nullable|string|max:255',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        
        if (Article::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $slug . '-' . time();
        }

        $article->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
        ]);

        return response()->json(['status' => 'success', 'message' => 'Artikel berhasil diperbarui!', 'data' => $article]);
    }

    public function destroyArticle($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();
        return response()->json(['status' => 'success', 'message' => 'Artikel berhasil dihapus!']);
    }

    public function getCountries()
    {
        $countries = Country::orderBy('name', 'asc')->get(['id', 'name', 'iso2']);
        return response()->json(['status' => 'success', 'data' => $countries]);
    }
}