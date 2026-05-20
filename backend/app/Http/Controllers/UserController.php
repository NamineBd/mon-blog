<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Lister tous les utilisateurs (admin uniquement)
     */
    public function index(Request $request)
    {
        // Seul un admin peut lister tous les utilisateurs
        if (!$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $users = User::select('id', 'name', 'email', 'is_admin', 'created_at')
            ->latest()
            ->paginate(20);

        return response()->json($users);
    }

    /**
     * Créer un nouvel utilisateur (admin uniquement)
     * Peut créer un admin ou un utilisateur standard
     */
    public function store(Request $request)
    {
        // Seul un admin peut créer des utilisateurs
        if (!$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'sometimes|boolean', // si absent, false par défaut
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->boolean('is_admin', false),
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $user->only(['id', 'name', 'email', 'is_admin'])
        ], 201);
    }

    /**
     * Voir un utilisateur spécifique
     * - Admin : voit n'importe quel utilisateur
     * - Standard : voit uniquement son propre profil
     */
    public function show(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Vérification des droits
        if (!$request->user()->is_admin && $request->user()->id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($user->only(['id', 'name', 'email', 'is_admin', 'created_at']));
    }

    /**
     * Mettre à jour un utilisateur
     * - Admin : peut modifier tout (name, email, password, is_admin)
     * - Standard : peut modifier son propre name, email et password
     *   (avec vérification du mot de passe actuel, et ne peut pas changer is_admin)
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $currentUser = $request->user();

        // Vérification des droits
        $isAdmin = $currentUser->is_admin;
        $isSelf = $currentUser->id == $user->id;

        if (!$isAdmin && !$isSelf) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Règles de validation de base
        $rules = [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ];

        // Si c'est un admin qui modifie (y compris lui-même en tant qu'admin)
        if ($isAdmin) {
            $rules['is_admin'] = 'sometimes|boolean';
            $rules['password'] = 'sometimes|string|min:8|confirmed';
        }

        // Si c'est l'utilisateur lui-même (standard ou admin) qui modifie son propre mot de passe
        if ($isSelf && $request->has('password')) {
            $rules['current_password'] = 'required|string';
            $rules['password'] = 'required|string|min:8|confirmed';
        } elseif ($isSelf && !$isAdmin && !$request->has('password')) {
            // Un utilisateur standard qui modifie ses infos (sans changer mot de passe)
            // Pas de règle supplémentaire
        }

        $request->validate($rules);

        // Préparer les données à mettre à jour
        $updateData = [];

        if ($request->has('name')) {
            $updateData['name'] = $request->name;
        }

        if ($request->has('email')) {
            $updateData['email'] = $request->email;
        }

        // Gestion du mot de passe (pour l'utilisateur lui-même ou un admin)
        if ($request->has('password')) {
            // Si c'est l'utilisateur lui-même, vérifier l'ancien mot de passe
            if ($isSelf && !$isAdmin) {
                if (!Hash::check($request->current_password, $user->password)) {
                    throw ValidationException::withMessages([
                        'current_password' => ['Le mot de passe actuel est incorrect.'],
                    ]);
                }
            }
            $updateData['password'] = Hash::make($request->password);
        }

        // Mise à jour du rôle is_admin (seulement par un admin)
        if ($isAdmin && $request->has('is_admin')) {
            // Empêcher un admin de se retirer le droit admin s'il est le dernier admin ? (optionnel)
            // Ici on autorise, mais on peut ajouter une vérification
            $updateData['is_admin'] = $request->boolean('is_admin');
        }

        // Appliquer la mise à jour
        $user->update($updateData);

        // Retourner les données (sans mot de passe)
        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès',
            'user' => $user->only(['id', 'name', 'email', 'is_admin'])
        ]);
    }

    /**
     * Supprimer un utilisateur (admin uniquement)
     * Empêche la suppression de son propre compte (sécurité)
     */
    public function destroy(Request $request, $id)
    {
        $currentUser = $request->user();

        // Seul un admin peut supprimer un utilisateur
        if (!$currentUser->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Empêcher un admin de supprimer son propre compte
        if ($currentUser->id == $id) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer votre propre compte'], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }
}