<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Http\Controllers\SoapControllerTitulus;
use Artisaninweb\SoapWrapper\SoapWrapper;
use App\Soap\Request\SaveDocument;
use App\Soap\Request\SaveParams;
use App\Soap\Request\AttachmentBean;
use Illuminate\Support\Facades\Storage;
use Spatie\ArrayToXml\ArrayToXml;
use App\Models\Titulus\Fascicolo;
use App\Models\Titulus\Documento;
use App\Models\Titulus\Rif;
use App\Models\Titulus\Element;
use App\Models\PersonaInterna;
use App\Models\StrutturaInterna;
use Illuminate\Support\Collection;
use App\Service\QueryTitulusBuilder;
use App\Http\Controllers\SoapControllerTitulusAcl;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Api\V1\StrutturaInternaController;
use App\Http\Controllers\Api\V1\PersonaInternaController;
use App\Http\Controllers\Api\V1\DocumentoController;
use Auth;
use App\User;

class TitulusTest extends TestCase
{

    use WithoutMiddleware;
      
    const NOME_RPA = 'cappellacci marco';
    const UFF = 'Ufficio Protocollo e Archivio';
      
    // ./vendor/bin/phpunit  --testsuite Unit --filter testLoadDocumentTitulus    
    public function testBasicLoadDocumentTitulus()
    {
        $sc = new SoapControllerTitulus(new SoapWrapper);                
        $response = $sc->loadDocument('827308',false);
        
        $obj = simplexml_load_string($response);
        $this->assertNotNull($obj->Document);        
    }
    
    /**
     * test titulus
     *
     * @return void
     */     
    // ./vendor/bin/phpunit  --testsuite Unit --filter testLoadDocumentTitulus
    public function testLoadDocumentTitulus()
    {
        // <Document physdoc="718031">
        // <doc anno="2019" annullato="no" cod_amm_aoo="UNURTST" data_prot="20190304" nrecord="000718031-UNURTST-d8830e7e-0cb3-4a99-aad8-cf2bc62b8d0e" num_prot="2019-UNURTST-0000023" scarto="10" tipo="arrivo" physdoc="718031">
        $sc = new SoapControllerTitulus(new SoapWrapper);                
        $response = $sc->loadDocument('827308',false);

        $this->assertNotNull($response);
        $obj = simplexml_load_string($response);
        $this->assertNotNull($obj->Document);
        $document = $obj->Document;
        $this->assertNotNull($obj->doc);
        $doc = $document->doc;
        
    }

    // ./vendor/bin/phpunit  --testsuite Unit --filter testSearchTitulus
    public function testSearchTitulus()
    {
        $sc = new SoapControllerTitulus(new SoapWrapper);
        $response = $sc->search('([UD,/xw/@UdType/]="indice_titolario")',null,null,null);
        //var_dump($response);
        $this->assertNotNull($response);
    }

    // ./vendor/bin/phpunit  --testsuite Unit --filter testXML1
    public function testXML1(){
        $array = [
            'Good guy' => [
                'name' => [
                    '_value' => 'valore',
                    '_attributes' => ['attr' => 'prova'],
                ],
                'weapon' => 'Lightsaber'
            ],
            'Bad guy' => [
                'name' => 'Sauron',
                'weapon' => 'Evil Eye'
            ]
        ];
                
        $result = ArrayToXml::convert($array);
        $this->assertNotNull($result);
    }

     // ./vendor/bin/phpunit  --testsuite Unit --filter testXMLFascicolo
    public function testXMLFascicolo(){
        $fascicolo = new Fascicolo;
        $fascicolo->oggetto = 'prova';
        $fascicolo->rootElementAttributes->stato = 'prova';
        $result = $fascicolo->toXml();
        $this->assertNotNull($result);
    }

    // ./vendor/bin/phpunit  --testsuite Unit --filter testXMLDocumento
    public function testXMLDocumento(){
        $doc = new Documento;
        $doc->rootElementAttributes->tipo = 'arrivo';
        $doc->addAllegato('1 - test1');
        $doc->addAllegato('2 - test2');        

        $doc->voce_indice = 'UNIPEO - Domanda di progressione economica orizzontale';        
            
        $nome = new Element('nome');
        $nome->rootElementAttributes->nominativo ="Mario Rossi";
        $nome->rootElementAttributes->cod = "SE000095";

        $rif_esterno = new Rif('rif_esterno');
        $rif_esterno->nome = $nome;

        $rif_esterno1 = new Rif('rif_esterno');
        $rif_esterno1->nome = "pippo";

        $doc->rif_esterni = array($rif_esterno, $rif_esterno1);

        $arr = $doc->toArray();        
        $this->assertNotNull($arr);
        $result = $doc->toXml();
        $this->assertEquals(str_replace(array("\n", "\r"), '', $result),'<?xml version="1.0" encoding="UTF-8"?><doc tipo="arrivo"><allegato>1 - test1</allegato><allegato>2 - test2</allegato><voce_indice>UNIPEO - Domanda di progressione economica orizzontale</voce_indice><rif_esterni><rif_esterno><nome cod="SE000095" nominativo="Mario Rossi"/></rif_esterno><rif_esterno><nome>pippo</nome></rif_esterno></rif_esterni></doc>');
    }
    
 
// ./vendor/bin/phpunit  --testsuite Unit --filter testNewFascicolo
    public function testNewFascicolo()
    {
        $sc = new SoapControllerTitulus(new SoapWrapper);        
        $fasc = new Fascicolo;
        $fasc->oggetto = 'convenzione di prova creato mediante ws';
        $fasc->addClassifCod('03/13');
        $fasc->addRPA(TitulusTest::UFF,TitulusTest::NOME_RPA);                  
        //$fasc->voce_indice = 'UNIPEO - Domanda di progressione economica orizzontale';  
        //var_dump($fasc->toXml());
//<voce_indice>UNIPEO - Domanda di progressione economica orizzontale</voce_indice>
        $this->assertEquals(str_replace(array("\n", "\r"), '',$fasc->toXml()),
        '<?xml version="1.0" encoding="UTF-8"?><fascicolo><oggetto>convenzione di prova creato mediante ws</oggetto><classif cod="03/13"/><rif_interni><rif diritto="RPA" nome_persona="cappellacci marco" nome_uff="Ufficio Protocollo e Archivio"/></rif_interni></fascicolo>');
        
        $response = $sc->newFascicolo($fasc->toXml());
        $this->assertNotNull($response);
        //var_dump($response);
        $year = date('Y');
        $obj = simplexml_load_string($response);
        $this->assertEquals($obj->Document->fascicolo['anno'],$year);
        $this->assertNotNull($obj->Document->fascicolo['nrecord']);
        $this->assertNotNull($obj->Document->fascicolo['numero']);
        var_dump($obj->Document->fascicolo['numero']);

    }
    
    // ./vendor/bin/phpunit  --testsuite Unit --filter testSearchDocumentiTitulus
    public function testSearchDocumentiTitulus()
    {
        $sc = new SoapControllerTitulus(new SoapWrapper);
        $response = $sc->search('([/doc/@tipo]=arrivo)',null,null,2);
        var_dump($response);
        $this->assertNotNull($response);
        $sessionId = implode(';', $sc->getSessionId());  

        $sc = new SoapControllerTitulus(new SoapWrapper);

        $response = $sc->nextTitlePage($sessionId);
        var_dump($response);
    }   

    // ./vendor/bin/phpunit  --testsuite Unit --filter testTitulusQuery
    public function testTitulusQuery(){        

        $request = new \Illuminate\Http\Request();
        $request->setMethod('POST');
        $request->replace([
            'rules' => [
                [
                    'field' => 'persint_nomcogn',
                    'operator' => '=',
                    'value' => 'Righi'
                ],
            ],
            'limit' => 25,
            ]);          

        $sc = new SoapControllerTitulusAcl(new SoapWrapper);
        $queryBuilder = new QueryTitulusBuilder(new PersonaInterna, $request, $sc);
        $result = $queryBuilder->build()->get();
        
        $this->assertNotNull($queryBuilder);
        $this->assertNotNull($result);
    }


     // ./vendor/bin/phpunit  --testsuite Unit --filter testTitulusQueryStrutturaInterna
    public function testTitulusQueryStrutturaInterna(){        

        $request = new \Illuminate\Http\Request();
        $request->setMethod('POST');
        $request->replace([
            'rules' => [
                [
                    'field' => 'struint_coduff',
                    'operator' => '=',
                    'value' => '*'
                ],
            ],
            'limit' => 2,
            ]);          

        $sc = new SoapControllerTitulusAcl(new SoapWrapper);
        $queryBuilder = new QueryTitulusBuilder(new StrutturaInterna, $request, $sc);
        $result = $queryBuilder->build()->get();
        
        $this->assertNotNull($queryBuilder);
        $this->assertNotNull($result);
    }

    
     // ./vendor/bin/phpunit  --testsuite Unit --filter testTitulusReadXMLStrutturaEsterna
     public function testTitulusReadXMLStrutturaEsterna(){        
        $xmlresponse = '<Response xmlns:xw="http://www.kion.it/ns/xw"
                            canSee="true"
                            canEdit="true"
                            canDelete="true">
            <Document physdoc="13395">
                <struttura_esterna cod_uff="SE001359"
                                    codice_fiscale="02191651203"
                                    nrecord="000013395-CDAMMAOO-d6653a2f-a9b4-420e-a470-4569e9f7a955"
                                    physdoc="13395"
                                    partita_iva="02191651203"
                                    tipologia="Cineca Company">
                    <nome xml:space="preserve">Kion s.p.a.</nome>
                    <indirizzo cap="40033" comune="Casalecchio di Reno" nazione="Italia" prov="Bologna">via Magnanelli, 2</indirizzo>
                    <telefono num="+39 051 6111411" tipo="tel"/>   
                    <telefono num="+39 051 570423" tipo="fax"/>           
                    <email addr="email1@kion.it"/>
                    <email addr="email2@kion.it"/>
                    <email_certificata addr="email_cert@kion.it"/>
                    <sito_web url="www.kion.it"/>
                    <sito_web url="www.cineca.it"/>
                    <note xml:space="preserve">Queste sono note</note>
                    <storia>
                    <creazione cod_oper="PI000122" cod_uff_oper="SI000085" data="20120314" oper="Grillini Federico" ora="17:23:31" uff_oper="Sviluppo"/>
                    </storia>
                </struttura_esterna>
            </Document>
        </Response>';

        $objResult = simplexml_load_string($xmlresponse);

        // $res = [];        
        // $arr = QueryTitulusBuilder::simpleXmlObjectToArray($objResult->Document->struttura_esterna);        
        // var_dump($arr);

        $arr = QueryTitulusBuilder::xmlToArray($objResult->Document->struttura_esterna, []);
        //var_dump($arr);   

        $this->assertEquals(count($arr['telefono']),2); 
        $this->assertEquals(count($arr['indirizzo']),5); 

     }

    // ./vendor/bin/phpunit  --testsuite Unit --filter testPersStrutturaInterna
    public function testPersStrutturaInterna(){
        $ctr = new StrutturaInternaController();        
        $strint = $ctr->getminimal('SI000084');

        $this->assertEquals('PI000083',$strint->cod_responsabile);
        $this->assertEquals('Dipartimento di Scienze Pure e Applicate - DISPeA',$strint->nome);

        $ctr = new PersonaInternaController();
        $persint = $ctr->getminimal('PI000083');
        $this->assertEquals('Mara Mancini',$persint->descrizione);

    }


    // ./vendor/bin/phpunit  --testsuite Unit --filter setwsuser
    public function testSetWSuser(){
        $user = User::where('email','enrico.oliva@uniurb.it')->first();
        $user->v_ie_ru_personale_id_ab = 39842;
        $this->actingAs($user);

        $sc = new SoapControllerTitulus(new SoapWrapper); 

        $pers =  Auth::user()->personaleRespons()->first(); 
        $ctrPers = new PersonaInternaController();
        $persint = $ctrPers->getminimalByName($pers->utenteNomepersona);

        $result = $sc->setWSUser($persint->loginName,$persint->matricola);
        $this->assertNotNull($result);

        $sessionId = $sc->getSessionId();

        $response = $sc->search('([/doc/@tipo]=arrivo)',null,null,2,$sessionId);
        var_dump($response);
        $this->assertNotNull($response);
    }

    
    // ./vendor/bin/phpunit  --testsuite Unit --filter testWorkflowEmail
    public function testDocumentController(){
        $user = User::where('email','enrico.oliva@uniurb.it')->first();
        $user->v_ie_ru_personale_id_ab = 39842;
        $this->actingAs($user);

        $ctr = new DocumentoController();   
        
        $request = new \Illuminate\Http\Request();
        $request->setMethod('POST');
        $request->replace([            
            'limit' => 25,
            ]);          

        $result = $ctr->query($request);
        $this->assertNotNull($result);
    }

    // ./vendor/bin/phpunit  --testsuite Unit --filter testResponseError
    public function testResponseError(){
        
        //    '<Response result="error">
        //     <errore cod="WS_E006">
        //     <descrizione>Utente inesistente</descrizione>
        //     </errore>
        //     </Response>';

        $sc = new SoapControllerTitulus(new SoapWrapper); 
        $result = $sc->setWSUser("nome.errore","P012345");

        $obj = simplexml_load_string($result);
        $this->assertNotNull($obj);             
        var_dump((string) $obj['result']);
        $this->assertTrue(isset($obj['result']));        
        $this->assertEquals((string) $obj['result'],'error');

    }

    //./vendor/bin/phpunit  --testsuite Unit --filter testLookupAcl
    public function testLookupAcl(){
        $sc = new SoapControllerTitulusAcl(new SoapWrapper); 

        $result = $sc->lookup('Servizio Sistema Informatico di Ateneo',null);

        $obj = simplexml_load_string($result);
        $this->assertNotNull($obj);                

        $strutturaInterna = new StrutturaInterna;
        $personaInterna = new PersonaInterna;
    
        $arr = QueryTitulusBuilder::xmlToArray($obj->struttura_interna, []);
        $strutturaInterna->fill($arr);
        $personaInterna->fill($arr['persona_interna']);
        
        $this->assertNotNull($personaInterna->matricola); 
        $this->assertNotNull($strutturaInterna->nome);         

    }

    //./vendor/bin/phpunit  --testsuite Unit --filter testgetResponsabile
    public function testgetResponsabile(){
        $ctr = new StrutturaInternaController();      
        //Attenzione ai testi con le parentesi 
        $persint = $ctr->getResponsabile('Servizio Sistema Informatico di Ateneo');
        $this->assertNotNull($persint->matricola); 
    }
    
}

