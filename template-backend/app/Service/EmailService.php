<?php

namespace App\Service;

use App;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Insegnamenti;
use App\InsegnamUgov;
use App\User;
use App\Precontrattuale;
use App\PrecontrattualePerGenerazione;
use App\SendEmail;
use App\Mail\FirstEmail;
use App\Mail\SubmitEmail;
use App\Mail\ValidateEmail;
use App\Mail\FirmaEmail;
use App\Mail\ContrattoEmail;
use App\Mail\InfoEmail;
use Illuminate\Support\Facades\Mail;
use DB;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class EmailService implements ApplicationService
{
    public static function sendToDocente($email, $pre, $tolocal = null){
   
        if (App::environment(['local','preprod'])) {
            //sviluppo debug
            if (Auth::user()){
                Mail::to(Auth::user())->send($email);            
            }else{
                //nel caso di comandi schedulati 
                Mail::to(config('unidem.administrator_email'))->send($email);                    
            }                                    
        } else {
            if ($pre->anagrafica){
                Mail::to($pre->user)
                    ->cc($pre->anagrafica->email_privata ?: [])
                    ->bcc(config('unidem.administrator_email'))->send($email);                
            }else{
                //leggo email privata da ugov (non esiste ancora l'anagrafica locale)
                $anagrafica = $pre->user->anagraficaugov()->first();
                Mail::to($pre->user)
                    ->cc($anagrafica->e_mail_privata ?: [])
                    ->bcc(config('unidem.administrator_email'))->send($email);
            }
            
        }          

    }


}
