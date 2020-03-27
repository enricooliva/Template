Template per applicazioni web: Laravel (backend) - Angular (frontend) - Shibboleth (SSO)
-------------------------------

## Features

- 🔥 Web application 
- ⚡️ Supporto per il SSO con Shibbolet
- ⚡️ Integrazione per la lettura dati da Ugov
    - lettura afferenza organizzativa
- ⚡️ Integrazione con Titulus
    - interrogazione strutture interne, persone e documenti
    - protocollazione e repertoriazione
- 📝 Sistema multi utente e multi ruolo
- 📝 Sistema configurabile con login utenti basato sulla struttura organizzativa 
- 📝 Disponibili componenti di interfaccia per
    - costruzione filtri di ricerca dinamici
    - inserimento dati (data, testo, numeri, ricerca, selezione, bottoni di scelta, tabelle)
    - finestre di recerca
    - barra di navigazione
    - finestre di popup per conferma o ricerca
- 📝 Generazione di pdf basato su [wkhtmltopdf](https://github.com/barryvdh/laravel-snappy)
- 😍 Tema Boostrap 
- 💪 Costruito su 
    - [Laravel](https://laravel.com/) 
    - [Angular](https://angular.io/)
    - [Dynamic forms in Angular](https://formly.dev/)


## Creazione di una applicazione

1) Fare un fork del repository "template" e rinominarlo nel nuovo progetto denominato "uniform"

2) Eseguire il clone del progetto `git clone https://username@bitbucket.org/enoliva/uniform.git`

3) Rinominare le cartelle sostituendo "template" con il nome del progetto

## Configurazione "template"-backend

1) Entrare nella cartella `cd .\template-backend\`

2) Creare un file di configurazione .env (copiare, rinominare e modificare il file .env.exmaple inserendo il nome dell'applicazione, 
il database di riferimento ...)

3) Eseguire `composer install` per l'istallazione dei package

4) Eseguire `php artisan migrate:fresh --seed` 

## Configurazione "template"-frontend

1) Entrare nella cartella `cd .\template-frontend\`

2) Eseguire `npm install`
   
## Configurazione "template"-mockipd

1) Entranre nella cartella cd `cd .\template-mock-idp\`

2) Eseguire  `npm install fake-sso-idp`

3) Il mock idp è configurato con un utente a cui è associato il ruolo SUPER-ADMIN

## Lancio dell'applicazione

1) Aprire tre terminal

2) Lancio dei servizi di backend 
   
    cd .\template-backen\
    php artisan serve --port 80
    

3) Lancio del frontend
   
    cd .\uniform-frontend\
    ng serve
   

4) Lancio del mock idp

    cd .\uniform-mock-idp\  
    node start.js
    

Aprire il broswer all'indirizzo  `http://localhost:4200/`


![home](/.vscode/home.PNG "Screenshot dell'applicazione")



Happy coding! 

