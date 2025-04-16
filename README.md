## 01-Rate limiter mancante

### Scenario:
Creare ed eseguire uno script (es. in bash con curl) che lancia moltissime richieste sulla stessa rotta con il pericolo di un denial of service

### Mitigazione:
- Rate limiter su /careers/submit
- Rate limiter su /article/search
- Rate limiter globale

<!-- Ecco cosa ho fatto:

1. Ho creato un nuovo middleware`RateLimit.php` che implementa tre livelli di protezione:
   
- Un rate limiter specifico per`/articles/search` che limita a 10 richieste al minuto per IP
- Un rate limiter specifico per`/careers/submit` che limita a 3 richieste al minuto per IP
- Un rate limiter globale che limita a 60 richieste al minuto per IP

2. Ho attivato il middleware nel file`bootstrap/app.php` e registrato l'alias`rate_limit` per poterlo utilizzare nelle rotte

3. Ho applicato il middleware alle rotte vulnerabili:

- `/articles/search` con il limitatore specifico "articles.search"
- `/careers/submit` con il limitatore specifico "careers.submit"

4. Ho implementato il logging degli eventi di rate limiting per garantire l'accountability e la non-ripudiazione, registrando:
   
- Indirizzo IP
- ID utente (se autenticato)
- Percorso della richiesta
- Tipo di limitatore attivato

5. Ho aggiunto header HTTP alla risposta per informare i client sui limiti di richieste e sulle richieste rimanenti
-->

---------------------------------------------------------------------------------------------------------

## 02-Operazioni critiche in post e non in get

### Scenario: 
Ci si espone a possibili attacchi CSRF portando in questo caso ad una vertical escalation of privileges.
Provare un attacco csrf creando un piccolo server php che visualizzi una pagina html in cui in background scatta una chiamata ajax ad una rotta potenzialmente critica e non protetta (es. /admin/{user}/set-admin). Partendo dal browser dell'utente è possibile che l'azione vada in porto in quanto l'utente ha i privilegi adeguati.

### Mitigazione
Cambiare da get a post, facendo i dovuti controlli

<!-- Per risolvere la challenge numero 2 relativa agli attacchi CSRF, ho implementato diverse misure di sicurezza:

1. Ho creato un nuovo middleware`ProtectCriticalOperations` che verifica che le operazioni critiche siano eseguite solo tramite POST e registra i tentativi di accesso non autorizzati.

2. Ho registrato il middleware nel file`bootstrap/app.php` con l'alias`protect_critical`.

3. Ho modificato le rotte nel file`web.php` , cambiando i metodi da GET a POST per le operazioni critiche di modifica dei ruoli:
   - `/admin/{user}/set-admin`
   - `/admin/{user}/set-revisor`
   - `/admin/{user}/set-writer`

4. Ho aggiornato il componente`requests-table.blade.php` sostituendo i link GET con form POST per le operazioni di modifica dei ruoli, includendo il token CSRF per proteggere da attacchi cross-site request forgery.

5. Ho implementato il logging delle operazioni critiche nel controller`AdminController.php` per garantire l'accountability e la non-ripudiazione, registrando:
   
- ID e nome dell'amministratore che esegue l'operazione
- ID e nome dell'utente target
- Indirizzo IP
- Timestamp 
-->

---------------------------------------------------------------------------------------------------------

## 03-Logging mancante per operazioni critiche

### Scenario:
Sui tentativi precedenti di DoS non si può risalire al colpevole violando il principiio di accountability e no repudiation

### Mitigazione:
Log di:
- login/registrazione/logout
- creazione/modifica/eliminazione articolo
- assegnazione/cambi di ruolo

<!-- Per risolvere la challenge 3 sui log mancanti per operazioni critiche, ho implementato un sistema completo di audit logging che traccia tutte le operazioni sensibili nell'applicazione. Ho creato una tabella audit_logs tramite migrazione per memorizzare eventi come login, logout, registrazione, modifiche agli articoli e cambi di ruolo. Ho sviluppato il modello AuditLog per interagire con questa tabella e un helper AuditLogger che fornisce metodi semplici per registrare i vari tipi di eventi. Ho configurato l'EventServiceProvider per ascoltare gli eventi di autenticazione di Laravel e registrarli nella tabella. Ho modificato i controller (AdminController per i ruoli, ArticleController per gli articoli) e i middleware di sicurezza (RateLimit e ProtectCriticalOperations) per utilizzare l'helper AuditLogger invece dei log standard di Laravel. Questo sistema garantisce l'accountability e la non-ripudiazione, permettendo di tracciare chi ha fatto cosa e quando nel sistema. -->

---------------------------------------------------------------------------------------------------------

## 04-ssrf attack per api delle news

### Scenario
Esiste la funzionalità di suggerimento news recenti in fase di scrittura dell'articolo per prendere ispirazione. E' presente un menu a scelta facilmente alterabile da ispeziona elemento. L'utente malintenzionato con un minimo di conoscenza del sistema cambia l'url e prova a far lanciare al server una richiesta che lui non sarebbe autorizzato.
Per esempio il server recupera dei dati sugli utenti da un altro server in esecuzione sulla porta 8001. 


### Mitigazione
Rimodellare la funzionalità in modo tale da non poter lasciare spazio di modifica dell'url da parte di utenti malevoli. Implementare o migliorare la validazione degli input.

https://newsapi.org/docs/endpoints/top-headlines
NewsAPI - api key 5fbe92849d5648eabcbe072a1cf91473

<!-- Ho implementato diverse misure di sicurezza per prevenire attacchi SSRF (Server-Side Request Forgery) nell'applicazione:

1. Miglioramento del servizio HttpService :
   
   - Rimosso "internal.finance" dai domini consentiti
   - Limitato i protocolli consentiti solo a HTTPS
   - Aggiunto blocco per intervalli IP privati e locali
   - Implementato un metodo`isUrlSafe()` che verifica vari aspetti di sicurezza dell'URL
   - Aggiunto controllo basato sui ruoli per l'accesso a risorse interne
   - Migliorato gli header di sicurezza nelle richieste
2. Rafforzamento del componente LatestNews :
   
   - Sostituito il sistema di selezione URL diretto con un sistema basato su codici paese
   - Implementato un metodo`generateApiUrl()` che crea URL sicuri internamente
   - Aggiunto validazione rigorosa dei paesi consentiti
3. Modifica dell'interfaccia utente :
   
   - Rimossi gli URL completi dal menu a tendina, sostituendoli con semplici codici paese
   - Eliminata la possibilità per l'utente di manipolare direttamente gli URL -->

---------------------------------------------------------------------------------------------------------

## 05-Stored XSS Attack

### Scenario
Durante la creazione di un articlo si può manomettere il body della richiesta con un tool tipo burpsuite in modalità proxy in modo da evitare l'auto escape eseguito dall'editor stesso e far arrivare alla funzionalità di creazione articolo uno script malevelo nel testo.
Questo script verra memorizzato ed eseguito quando un utente visualizza l'articolo infettato.
Supponiamo che ci sia una misconfiguration a livello di CORS (config/cors.php) che quindi permetta richieste da domini esterni, utile quando frontend e backend sono separati ma se non opportunamente configurato risulta essere un grave problema.

### Mitigazione
Creare un meccanismo che filtri il testo prima di salvarlo e per essere sicuri anche in fase di visualizzazione dell'articolo

<!-- Soluzione Implementata
1. Sanificazione in fase di salvataggio e modifica
Abbiamo integrato la libreria HTMLPurifier per sanificare il campo body degli articoli sia in fase di creazione che di modifica.

Come funziona:

Ogni volta che un articolo viene creato o aggiornato, il testo viene passato attraverso HTMLPurifier, che rimuove o neutralizza tag e attributi HTML pericolosi.
È possibile personalizzare i tag HTML permessi (ad esempio solo <p>, <b>, <a>, ecc.) per mantenere la formattazione desiderata e bloccare tutto il resto.
Esempio di codice nel controller:

php
CopyInsert
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
$sanitizedBody = $purifier->purify($request->input('body'));

Il valore sanificato viene poi salvato nel database:

php
CopyInsert
'body' => $sanitizedBody,

2. Applicazione sia in creazione che in modifica

Funzione store: la sanificazione viene applicata prima di salvare un nuovo articolo.
Funzione update: la sanificazione viene applicata anche quando un articolo viene modificato.

Vantaggi della soluzione
Protezione contro XSS persistente: nessun codice malevolo può essere salvato o aggiornato nel database tramite il campo body.
Sicurezza anche contro attacchi avanzati: la sanificazione avviene lato server, quindi protegge anche da richieste manipolate via proxy come BurpSuite.
Personalizzazione: è possibile scegliere quali tag HTML permettere, bilanciando sicurezza e formattazione.
 -->

---------------------------------------------------------------------------------------------------------

## 06-Uso non corretto di fillable nei modelli

### Scenario 
Un utente malevolo può provare a indovinare campi tipici di ruoli utente tipo isAdmin, is_admin etc.. alterando il form dal browser 

### Mitigazione
Nella proprietà fillable del modello in questione inserire tutti solo i campi gestiti nel form

<!-- Ecco i punti principali del passaggio 6 (Mitigazione Mass Assignment):

1. Ho analizzato il modello User.php e identificato che i campi sensibili (is_admin, is_revisor, is_writer) erano inclusi nella proprietà $fillable, creando una vulnerabilità di mass assignment
2. Ho rimosso questi campi sensibili dalla proprietà $fillable, limitandola solo ai campi che dovrebbero essere modificabili dall'utente (nome, email, password)
3. Ho aggiunto i campi sensibili alla proprietà $guarded per impedire esplicitamente la loro assegnazione in massa
4. Ho creato una pagina di profilo utente per dimostrare e testare la vulnerabilità prima della mitigazione
5. Ho implementato una validazione più rigorosa nel controller UserProfileController per verificare esplicitamente quali campi vengono aggiornati -->