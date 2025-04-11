# Guida ai Test di Sicurezza

Questo documento fornisce istruzioni dettagliate su come testare le implementazioni di sicurezza del progetto CyberBlog. Le principali misure di sicurezza implementate sono:

1. Rate limiter contro attacchi DoS
2. Protezione CSRF per operazioni critiche
3. Sistema di logging delle operazioni critiche

## 1. Test del Rate Limiter

Il sistema implementa tre livelli di protezione contro attacchi DoS:

- Rate limiter specifico per `/articles/search`: 10 richieste al minuto per IP
- Rate limiter specifico per `/careers/submit`: 3 richieste al minuto per IP
- Rate limiter globale: 60 richieste al minuto per IP

### 1.1 Test del Rate Limiter Globale

```bash
# Dalla directory del progetto
cd XXX-AttackTools/dos
# Esegui lo script di attacco DoS
./dos_attacck.sh
```

Questo script invierà 1000 richieste al server. Dovresti osservare che dopo 60 richieste in un minuto, le successive richieste riceveranno una risposta 429 (Too Many Requests).

### 1.2 Test del Rate Limiter per la Ricerca Articoli

```bash
# Dalla directory del progetto
cd XXX-AttackTools/dos
# Per macOS
./search_manage_mem_mac.sh
# Per Linux
./search_manage_mem_linux.sh
```

Questi script invieranno richieste alla rotta `/articles/search`. Dovresti osservare che dopo 10 richieste in un minuto, le successive richieste riceveranno una risposta 429.

### 1.3 Verifica dei Log di Rate Limiting

Dopo aver eseguito i test, controlla i log di sicurezza per verificare che gli eventi di rate limiting siano stati registrati:

```bash
cat storage/logs/security.log
```

Dovresti vedere messaggi di log che indicano "Rate limit exceeded" con dettagli come IP, percorso della richiesta e tipo di limitatore attivato.

### 1.4 Verifica degli Header HTTP

Puoi anche verificare gli header HTTP nelle risposte per confermare che il rate limiting sia attivo:

```bash
curl -I http://cyber.blog:8000
```

Dovresti vedere header come `X-RateLimit-Limit` e `X-RateLimit-Remaining` nelle risposte.

## 2. Test della Protezione CSRF

Il sistema implementa protezione CSRF per tutte le operazioni critiche, in particolare per le operazioni di modifica dei ruoli utente.

### 2.1 Test di un Attacco CSRF

```bash
# Dalla directory del progetto
cd XXX-AttackTools/csrf
# Avvia un server PHP locale per simulare un sito malevolo
php -S localhost:8080
```

Ora apri un browser e visita `http://localhost:8080`. La pagina tenterà di eseguire un attacco CSRF contro la rotta `/admin/2/set-admin`.

### 2.2 Verifica della Protezione

Se la protezione CSRF funziona correttamente, l'attacco dovrebbe fallire e dovresti essere reindirizzato alla dashboard con un messaggio di errore che indica che le operazioni critiche devono essere eseguite tramite POST.

### 2.3 Verifica dei Log di Sicurezza

Dopo aver tentato l'attacco CSRF, controlla i log di sicurezza:

```bash
cat storage/logs/security.log
```

Dovresti vedere messaggi di log che indicano "Tentativo di accesso a operazione critica tramite GET" con dettagli come IP, ID utente e percorso della richiesta.

## 3. Test del Sistema di Logging

Il sistema implementa un sistema di logging completo per tutte le operazioni critiche, utilizzando la tabella `audit_logs`.

### 3.1 Verifica dei Log di Autenticazione

Esegui operazioni di login, logout e registrazione, quindi verifica che queste operazioni siano state registrate:

```sql
SELECT * FROM audit_logs WHERE action LIKE 'auth.%' ORDER BY created_at DESC LIMIT 10;
```

### 3.2 Verifica dei Log di Modifica Ruoli

Esegui operazioni di modifica dei ruoli utente (promuovi un utente ad amministratore, revisore o scrittore), quindi verifica che queste operazioni siano state registrate:

```sql
SELECT * FROM audit_logs WHERE action LIKE 'role.%' ORDER BY created_at DESC LIMIT 10;
```

### 3.3 Verifica dei Log di Sicurezza

Dopo aver eseguito i test di rate limiting e CSRF, verifica che gli eventi di sicurezza siano stati registrati:

```sql
SELECT * FROM audit_logs WHERE action LIKE 'security.%' ORDER BY created_at DESC LIMIT 10;
```

### 3.4 Esame dei Dettagli dei Log

Per esaminare i dettagli di un log specifico, puoi utilizzare la seguente query:

```sql
SELECT id, user_id, action, ip_address, created_at, details FROM audit_logs WHERE id = [ID_DEL_LOG];
```

I campi `old_values` e `new_values` contengono informazioni sui valori prima e dopo una modifica, mentre il campo `details` contiene dettagli aggiuntivi sull'operazione.

## 4. Interpretazione dei Risultati

### 4.1 Rate Limiter

Se il rate limiter funziona correttamente:
- Le richieste che superano il limite impostato riceveranno una risposta 429
- Gli header HTTP mostreranno i limiti e le richieste rimanenti
- I log di sicurezza conterranno eventi di rate limiting

### 4.2 Protezione CSRF

Se la protezione CSRF funziona correttamente:
- I tentativi di accesso a operazioni critiche tramite GET falliranno
- I tentativi di accesso a operazioni critiche tramite POST senza token CSRF falliranno
- I log di sicurezza conterranno eventi di tentativi di accesso non autorizzati

### 4.3 Sistema di Logging

Se il sistema di logging funziona correttamente:
- Tutte le operazioni critiche saranno registrate nella tabella `audit_logs`
- I log conterranno informazioni dettagliate come IP, user agent, valori prima e dopo la modifica
- I log saranno organizzati per tipo di azione (auth, role, security, ecc.)

## Conclusione

Questi test ti permetteranno di verificare che le implementazioni di sicurezza del progetto funzionino correttamente. Se riscontri problemi o comportamenti inaspettati durante i test, controlla i log di sicurezza per ulteriori informazioni e verifica la configurazione delle misure di sicurezza.