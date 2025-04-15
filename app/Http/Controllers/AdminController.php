<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Tag;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\HttpService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    protected $httpService;

    public function __construct(HttpService $httpService)
    {
        $this->httpService = $httpService;
    } 

    public function dashboard(){
        Log::debug('[AdminController] Inizio metodo dashboard');
        $adminRequests = User::where('is_admin', NULL)->get();
        $revisorRequests = User::where('is_revisor', NULL)->get();
        $writerRequests = User::where('is_writer', NULL)->get();
        
        // Inizializza $financialData con una struttura vuota ma valida
        $financialData = ['users' => []];
        
        Log::debug('[AdminController] Richieste utenti recuperate', [
            'admin_requests' => count($adminRequests),
            'revisor_requests' => count($revisorRequests),
            'writer_requests' => count($writerRequests),
            'user_id' => Auth::id()
        ]);

        //$financialData = json_decode($this->httpService->getRequest('http://localhost:8001/financialApp/user-data.php'));
        
        try {
            Log::debug('[AdminController] Tentativo di richiesta HTTP a internal.finance');
            // Effettua la richiesta HTTP
            $response = $this->httpService->getRequest('http://internal.finance:8001/user-data.php');
            Log::debug('[AdminController] Risposta HTTP ricevuta', ['response_length' => strlen($response)]);
            
            // Controlla se la risposta è vuota o non valida
            if (empty($response) || is_string($response) && strpos($response, 'Error') === 0) {
                Log::error('[AdminController] Risposta HTTP vuota o errore');
                throw new Exception('La risposta dalla richiesta HTTP è vuota o contiene un errore.');
            }
           
            // Decodifica il JSON
            $decodedData = json_decode($response, true);

            // Controlla se ci sono errori nella decodifica del JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('[AdminController] Errore decodifica JSON', ['error' => json_last_error_msg()]);
                throw new Exception('Errore nella decodifica del JSON: ' . json_last_error_msg());
            }
            
            // Verifica che i dati decodificati abbiano la struttura attesa
            if (is_array($decodedData) && isset($decodedData['users'])) {
                $financialData = $decodedData;
                Log::debug('[AdminController] Dati finanziari decodificati con successo', [
                    'users_count' => count($financialData['users'])
                ]);
            } else {
                Log::error('[AdminController] Struttura dati non valida');
                throw new Exception('La struttura dei dati finanziari non è valida.');
            }
        } catch (Exception $e) {
            // Gestisci l'eccezione
            Log::error('[AdminController] Eccezione durante il recupero dei dati finanziari', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Non mostriamo l'errore direttamente all'utente, ma lo registriamo solo nei log
            // $financialData mantiene la struttura vuota ma valida inizializzata all'inizio
        }
        
        return view('admin.dashboard', compact('adminRequests', 'revisorRequests', 'writerRequests','financialData'));
    }

    public function setAdmin(User $user){
        $oldRole = $user->is_admin ? 'admin' : 'user';
        $user->is_admin = true;
        $user->save();
        
        // Utilizzo dell'helper AuditLogger per registrare la modifica del ruolo
        \App\Helpers\AuditLogger::roleLog('promote_admin', $user, $oldRole, 'admin');

        return redirect(route('admin.dashboard'))->with('message', "$user->name is now administrator");
    }

    public function setRevisor(User $user){
        $oldRole = $user->is_revisor ? 'revisor' : 'user';
        $user->is_revisor = true;
        $user->save();
        
        // Utilizzo dell'helper AuditLogger per registrare la modifica del ruolo
        \App\Helpers\AuditLogger::roleLog('promote_revisor', $user, $oldRole, 'revisor');

        return redirect(route('admin.dashboard'))->with('message', "$user->name is now revisor");
    }

    public function setWriter(User $user){
        $oldRole = $user->is_writer ? 'writer' : 'user';
        $user->is_writer = true;
        $user->save();
        
        // Utilizzo dell'helper AuditLogger per registrare la modifica del ruolo
        \App\Helpers\AuditLogger::roleLog('promote_writer', $user, $oldRole, 'writer');

        return redirect(route('admin.dashboard'))->with('message', "$user->name is now writer");
    }

    public function editTag(Request $request, Tag $tag){
        $request->validate([
            'name' => 'required|unique:tags',
        ]);
        $tag->update([
            'name' => strtolower($request->name),
        ]);
        return redirect()->back()->with('message', 'Tag successfully updated');
    }

    public function deleteTag(Tag $tag){
        foreach($tag->articles as $article){
            $article->tags()->detach($tag);
        }
        $tag->delete();

        return redirect()->back()->with('message', 'Tag successfully deleted');
    }

    public function editCategory(Request $request, Category $category){
        $request->validate([
            'name' => 'required|unique:categories',
        ]);
        $category->update([
            'name' => strtolower($request->name),
        ]);

        return redirect()->back()->with('message', 'Category successfully updated');
    }

    public function deleteCategory(Category $category){
        $category->delete();

        return redirect()->back()->with('message', 'Category successfully deleted');
    }

    public function storeCategory(Request $request){
        $category = Category::create([
            'name' => strtolower($request->name),
        ]);
        
        return redirect()->back()->with('message', 'Category successfully created');
    }

    public function storeTag(Request $request){
        $tag = Tag::create([
            'name' => strtolower($request->name),
        ]);
        
        return redirect()->back()->with('message', 'Tag successfully created');
    }
}
