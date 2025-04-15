<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    // Il middleware auth è già applicato nelle rotte in web.php
    public function __construct()
    {
        // Non è necessario applicare il middleware qui
    }

    /**
     * Mostra la pagina del profilo utente
     */
    public function show()
    {
        return view('profile.show', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Aggiorna le informazioni del profilo utente
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validazione dei dati
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        // Aggiornamento dei dati dell'utente
        // Utilizziamo solo i campi validati per evitare mass assignment
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Profilo aggiornato con successo');
    }

    /**
     * Aggiorna la password dell'utente
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        
        // Aggiornamento della password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Password aggiornata con successo');
    }
}