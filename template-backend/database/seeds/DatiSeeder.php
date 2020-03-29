<?php
use Illuminate\Database\Seeder;
use App\Convenzione;
use App\MappingRuolo;
use App\Role;
use App\Personale;
use App\Precontrattuale;
use Illuminate\Support\Facades\Hash;

//php artisan db:seed --class=DatiSeeder
//composer dump-autoload -o 

////php artisan migrate:fresh --seed
class DatiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {                       
        $this->attachmenttypes();                  
        $this->mappingtable();        
        $this->mappingruoli();        
    }

  

    private function onlyFirstUpper($value){
        return ucwords(mb_strtolower($value, 'UTF-8'));
    }


    public function mappingruoli(){
                        
    }


    public function mappingtable(){
        
        DB::table('mappinguffici')->insert([                          
            'unitaorganizzativa_uo' => '000001',
            'descrizione_uo' => 'Plesso Scientifico',                     
            'strutturainterna_cod_uff' => '00000000',
            'descrizione_uff' => 'Plesso Scientifico2',                     
        ]);

    }  

    public function attachmenttypes(){
        DB::table('attachmenttypes')->insert([   
            'codice' => 'DOC_CV',        
            'gruppo' => 'anagrafica',
            'descrizione' => 'Curriculum',
            'descrizione_compl' => 'Curriculum',         
            'parent_type' => User::class,   
        ]);

        DB::table('attachmenttypes')->insert([   
            'codice' => 'DOC_CI',        
            'gruppo' => 'anagrafica',
            'descrizione' => 'Carta di identità',  
            'descrizione_compl' => 'Carta di identità',        
            'parent_type' => User::class,    
        ]);

        DB::table('attachmenttypes')->insert([   
            'codice' => 'AUT_PA',        
            'gruppo' => 'B4RapportoPA',
            'descrizione' => 'Autorizzazione PA',  
            'descrizione_compl' => 'Autorizzazione Pubblica Amministrazione',        
            'parent_type' => B4RapportoPA::class,    
        ]);
        
        DB::table('attachmenttypes')->insert([   
            'codice' => 'CONTR_BOZZA',        
            'gruppo' => 'Precontrattuale',
            'descrizione' => 'Contratto bozza',  
            'descrizione_compl' => 'Contratto in stato di bozza',        
            'parent_type' => Precontrattuale::class,    
        ]);
        DB::table('attachmenttypes')->insert([   
            'codice' => 'CONTR_FIRMA',        
            'gruppo' => 'Precontrattuale',
            'descrizione' => 'Contratto',  
            'descrizione_compl' => 'Contratto',        
            'parent_type' => Precontrattuale::class,    
        ]);        

        DB::table('attachmenttypes')->insert([   
            'codice' => 'DOM_GS',        
            'gruppo' => 'D1_Inps',
            'descrizione' => 'Iscrizione GS',  
            'descrizione_compl' => 'Domanda di iscrizione alla Gestione Separata',        
            'parent_type' => D1_Inps::class,
        ]);
     
    }
}
