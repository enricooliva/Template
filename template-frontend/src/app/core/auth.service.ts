import { Inject, Injectable } from '@angular/core';
import { LOCAL_STORAGE, StorageService } from 'ngx-webstorage-service';
import { Observable, BehaviorSubject } from 'rxjs';
import { map } from 'rxjs/operators';
import { nullSafeIsEquivalent } from '@angular/compiler/src/output/output_ast';
import { HttpClient, HttpHeaders, HttpResponse } from '@angular/common/http';
import { JwtHelperService } from '@auth0/angular-jwt';
import { Router } from '@angular/router';
import { NgxPermissionsService } from 'ngx-permissions';
import { AppConstants } from '../app-constants';



interface LoginResponse {
  accessToken: string;
  accessExpiration: number;
}

const httpOptions = {
  headers: new HttpHeaders({
    'Content-Type': 'text'
  })
};

@Injectable({
  providedIn: 'root'
})
export class AuthService {

    private authUrl: string;
    private loggedIn = new BehaviorSubject<boolean>(false);

    _username: string;
    _roles: string[]  = [''];
    _id: number;
    _email: string;
    _dips: string[];

    static TOKEN = 'tokenunicontr'

    constructor( private http: HttpClient,
                 public jwtHelper: JwtHelperService,
                 private router: Router,
                 private permissionsService: NgxPermissionsService ) {
        this.loggedIn.next(this.isAuthenticated());
        this.authUrl = AppConstants.baseURL;
    }

    login() {
        // il login purtroppo non passa da questo metodo.
        // Effetuando la chimamata da una sorgente diversa da quello del server
        // otteniamo un errore CORS

        return this.http.get(`${this.authUrl}/loginSaml`, httpOptions)
        .subscribe(res => {
            // if (res.headers.get(AuthService.TOKEN)) {
            //   localStorage.setItem('auth_token', res.headers.get('token'));
            //   this.loggedIn = true;
            // }
            // console.log(res.accessToken, res.accessExpiration);
            // localStorage.setItem('auth_token', res.accessToken);
            console.log(res);
        });
    }

    loginWithToken(token: any) {
        // console.log(localStorage.setItem(AuthService.TOKEN, token));
        localStorage.setItem(AuthService.TOKEN, token);
        this.loggedIn.next(this.isAuthenticated());
        this.reload();
    }

    reload(): any {
        if (this.isAuthenticated()) {
            const helper = new JwtHelperService();
            // console.log(helper);
            const decodedToken = helper.decodeToken(localStorage.getItem(AuthService.TOKEN));
            // console.log(decodedToken);
            this._email = decodedToken['email'];
            this._username = decodedToken['name'];
            this._roles = decodedToken['roles'];
            this._dips = decodedToken['dips'];
            // console.log(this.roles);
            this._id = decodedToken['id'];
            this.permissionsService.loadPermissions(this._roles);
        }
    }

    redirectFirstLogin(){

        const permissions = this.permissionsService.getPermissions();
        if (permissions['SUPER_ADMIN']){
            this.router.navigate(['home']);                    
        }
        this.router.navigate(['home']); 
    }


    resetFields() {
        this._username = '';
        this._id = null;
        this._roles = [];
        this._email = '';
    }

    getToken() {
        localStorage.getItem(AuthService.TOKEN);
    }

    logout() {
        localStorage.removeItem(AuthService.TOKEN);
        this.permissionsService.flushPermissions();
        this.resetFields();
        this.loggedIn.next(false);
    }

    get isLoggedIn() {
        return this.loggedIn.asObservable();
    }

    public get userid(): number {
        return this._id;
    }

    public get email(): string {
        return this._email;
    }

    public get username(): string {
        return this._username;
    }

    public get roles(): string[] {
        return this._roles;
    }

    public get dips(): string[] {
        return this._dips;
    }

    public isAuthenticated(): boolean {
        const token = localStorage.getItem(AuthService.TOKEN);
        // alert(token);
        // Check whether the token is expired and return
        // true or false
        return !this.jwtHelper.isTokenExpired(token);
    }

    /**
     * Handle any errors from the API
     */
    private handleError(err) {
        let errMessage: string;
        errMessage = '';
        // if (err instanceof Response) {
        //   let body = err.json() || '';
        //   let error = body.error || JSON.stringify(body);
        //   errMessage = `${err.status} - ${err.statusText || ''} ${error}`;
        // } else {
        //   errMessage = err.message ? err.message : err.toString();
        // }

        return Observable.throw(errMessage);
    }
}
