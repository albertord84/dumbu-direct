webpackJsonp(["main"],{

/***/ "../../../../../src/$$_gendir lazy recursive":
/***/ (function(module, exports) {

function webpackEmptyAsyncContext(req) {
	// Here Promise.resolve().then() is used instead of new Promise() to prevent
	// uncatched exception popping up in devtools
	return Promise.resolve().then(function() {
		throw new Error("Cannot find module '" + req + "'.");
	});
}
webpackEmptyAsyncContext.keys = function() { return []; };
webpackEmptyAsyncContext.resolve = webpackEmptyAsyncContext;
module.exports = webpackEmptyAsyncContext;
webpackEmptyAsyncContext.id = "../../../../../src/$$_gendir lazy recursive";

/***/ }),

/***/ "../../../../../src/app/app-routing.module.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return AppRoutingModule; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_router__ = __webpack_require__("../../../router/@angular/router.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__components_login_login_component__ = __webpack_require__("../../../../../src/app/components/login/login.component.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__components_actions_actions_component__ = __webpack_require__("../../../../../src/app/components/actions/actions.component.ts");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};




var routes = [
    { path: '', component: __WEBPACK_IMPORTED_MODULE_2__components_login_login_component__["a" /* LoginComponent */] },
    { path: 'actions', component: __WEBPACK_IMPORTED_MODULE_3__components_actions_actions_component__["a" /* ActionsComponent */] }
];
var AppRoutingModule = (function () {
    function AppRoutingModule() {
    }
    return AppRoutingModule;
}());
AppRoutingModule = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["M" /* NgModule */])({
        imports: [__WEBPACK_IMPORTED_MODULE_1__angular_router__["b" /* RouterModule */].forRoot(routes)],
        declarations: [],
        exports: [__WEBPACK_IMPORTED_MODULE_1__angular_router__["b" /* RouterModule */]]
    })
], AppRoutingModule);

//# sourceMappingURL=app-routing.module.js.map

/***/ }),

/***/ "../../../../../src/app/app.component.html":
/***/ (function(module, exports) {

module.exports = "<div class=\"container\">\n  <div class=\"title text-center\">\n    <h1>DUMBU</h1>\n  </div>\n  <router-outlet></router-outlet>\n</div>\n"

/***/ }),

/***/ "../../../../../src/app/app.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return AppComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};

var AppComponent = (function () {
    function AppComponent() {
    }
    return AppComponent;
}());
AppComponent = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["o" /* Component */])({
        selector: 'app-root',
        template: __webpack_require__("../../../../../src/app/app.component.html")
    }),
    __metadata("design:paramtypes", [])
], AppComponent);

//# sourceMappingURL=app.component.js.map

/***/ }),

/***/ "../../../../../src/app/app.module.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return AppModule; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_platform_browser__ = __webpack_require__("../../../platform-browser/@angular/platform-browser.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_common_http__ = __webpack_require__("../../../common/@angular/common/http.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__ngrx_store__ = __webpack_require__("../../../../@ngrx/store/index.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__app_component__ = __webpack_require__("../../../../../src/app/app.component.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5__app_routing_module__ = __webpack_require__("../../../../../src/app/app-routing.module.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_6__components_login_login_component__ = __webpack_require__("../../../../../src/app/components/login/login.component.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_7__services_user__ = __webpack_require__("../../../../../src/app/services/user.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_8__reducers_user__ = __webpack_require__("../../../../../src/app/reducers/user.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_9__components_actions_actions_component__ = __webpack_require__("../../../../../src/app/components/actions/actions.component.ts");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};










var AppModule = (function () {
    function AppModule() {
    }
    return AppModule;
}());
AppModule = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_2__angular_core__["M" /* NgModule */])({
        declarations: [
            __WEBPACK_IMPORTED_MODULE_4__app_component__["a" /* AppComponent */],
            __WEBPACK_IMPORTED_MODULE_6__components_login_login_component__["a" /* LoginComponent */],
            __WEBPACK_IMPORTED_MODULE_9__components_actions_actions_component__["a" /* ActionsComponent */]
        ],
        imports: [
            __WEBPACK_IMPORTED_MODULE_0__angular_platform_browser__["a" /* BrowserModule */],
            __WEBPACK_IMPORTED_MODULE_5__app_routing_module__["a" /* AppRoutingModule */],
            __WEBPACK_IMPORTED_MODULE_1__angular_common_http__["b" /* HttpClientModule */],
            __WEBPACK_IMPORTED_MODULE_3__ngrx_store__["b" /* StoreModule */].provideStore({
                user: __WEBPACK_IMPORTED_MODULE_8__reducers_user__["c" /* reducer */]
            })
        ],
        providers: [__WEBPACK_IMPORTED_MODULE_7__services_user__["a" /* UserService */]],
        bootstrap: [__WEBPACK_IMPORTED_MODULE_4__app_component__["a" /* AppComponent */]]
    })
], AppModule);

//# sourceMappingURL=app.module.js.map

/***/ }),

/***/ "../../../../../src/app/components/actions/actions.component.css":
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__("../../../../css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "", ""]);

// exports


/*** EXPORTS FROM exports-loader ***/
module.exports = module.exports.toString();

/***/ }),

/***/ "../../../../../src/app/components/actions/actions.component.html":
/***/ (function(module, exports) {

module.exports = "<div class=\"actions row\">\n  \n  <div class=\"question row text-muted text-center\">\n    <h4>What do you want to do?</h4>\n  </div>\n  <br>\n  \n  <div class=\"action\">\n    <a class=\"btn btn-lg btn-primary\" href>\n      <div class=\"panel panel-primary\">\n        <div class=\"panel-heading\">\n            <h2>Send Message</h2>\n        </div>\n      </div>\n    </a>\n  </div>\n\n  <div class=\"action\" *ngIf=\"isAdmin\">\n    <a class=\"btn btn-lg btn-success\" href>\n      <div class=\"panel panel-success\">\n        <div class=\"panel-heading\">\n            <h2>Create a Promotion!</h2>\n        </div>\n      </div>\n    </a>\n  </div>\n\n  <div class=\"action\" *ngIf=\"isAdmin\">\n    <a class=\"btn btn-lg btn-info\" href>\n      <div class=\"panel panel-info\">\n        <div class=\"panel-heading\">\n            <h2>Start a Campaign!</h2>\n        </div>\n      </div>\n    </a>\n  </div>\n    \n</div>\n"

/***/ }),

/***/ "../../../../../src/app/components/actions/actions.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return ActionsComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};

var ActionsComponent = (function () {
    function ActionsComponent() {
    }
    ActionsComponent.prototype.ngOnInit = function () {
    };
    return ActionsComponent;
}());
ActionsComponent = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["o" /* Component */])({
        selector: 'app-actions',
        template: __webpack_require__("../../../../../src/app/components/actions/actions.component.html"),
        styles: [__webpack_require__("../../../../../src/app/components/actions/actions.component.css")]
    }),
    __metadata("design:paramtypes", [])
], ActionsComponent);

//# sourceMappingURL=actions.component.js.map

/***/ }),

/***/ "../../../../../src/app/components/login/login.component.css":
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__("../../../../css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "", ""]);

// exports


/*** EXPORTS FROM exports-loader ***/
module.exports = module.exports.toString();

/***/ }),

/***/ "../../../../../src/app/components/login/login.component.html":
/***/ (function(module, exports) {

module.exports = "<div class=\"login\">\n  <div class=\"panel panel-default\">\n    <div class=\"panel-body\">\n      \n      <form name=\"loginForm\" role=\"form\" class=\"login-form\">\n        <h4 class=\"text-center\">Login with your Instagram</h4>\n        \n        <div class=\"form-group\">\n          <input type=\"text\" class=\"form-control input-lg\" id=\"username\" name=\"username\"\n          autocomplete=\"off\" (keyup)=\"change($event)\"\n          placeholder=\"Instagram user...\">\n        </div>\n        \n        <div class=\"form-group\">\n          <input type=\"password\" class=\"form-control input-lg\" id=\"password\" name=\"password\"\n          autocomplete=\"off\" (keyup)=\"change($event)\"\n          placeholder=\"Password account...\">\n        </div>\n        \n        <div class=\"row buttons\">\n          <div class=\"col-xs-12\">\n            <button type=\"button\" class=\"btn btn-primary btn-block btn-lg\"\n                    (click)=\"login($event)\" [disabled]=\"!canLogIn\"\n                    *ngIf=\"!logging\">\n              <h4>Log In</h4>\n            </button>\n          </div>\n          <div class=\"col-xs-12 text-center\" *ngIf=\"logging\">\n            <img src=\"assets/loading-small.gif\">\n          </div>\n        </div>\n        \n        <div class=\"row error\" *ngIf=\"error\">\n          <div class=\"col-xs-12\">\n            <br>\n            <div class=\"alert alert-danger\">\n              <strong>Error:</strong>&nbsp;{{errorMsg}}\n            </div>\n          </div>\n        </div>\n      </form>\n      \n      \n    </div>\n  </div>\n</div>\n"

/***/ }),

/***/ "../../../../../src/app/components/login/login.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return LoginComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__ngrx_store__ = __webpack_require__("../../../../@ngrx/store/index.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__angular_router__ = __webpack_require__("../../../router/@angular/router.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__reducers_user__ = __webpack_require__("../../../../../src/app/reducers/user.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__services_user__ = __webpack_require__("../../../../../src/app/services/user.ts");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};





var LoginComponent = (function () {
    function LoginComponent(store, userService, router) {
        var _this = this;
        this.store = store;
        this.userService = userService;
        this.router = router;
        this.canLogIn = false;
        this.logging = false;
        this.error = false;
        this.errorMsg = '';
        this._userName = '';
        this._password = '';
        this.user = store.select('user');
        this.user.subscribe(function (s) {
            _this._userName = s.userName;
            _this._password = s.password;
        });
    }
    LoginComponent.prototype.ngOnInit = function () {
    };
    LoginComponent.prototype.login = function (e) {
        var _this = this;
        this.logging = true;
        this.error = false;
        this.userService.login(this._userName, this._password)
            .then(function (res) {
            if (res.success) {
                _this.logging = false;
                setTimeout(function () {
                    _this.router.navigate(['actions']);
                }, 700);
            }
            else {
                _this.showError("Something happened trying to log " + _this._userName + " in");
            }
        })
            .catch(function (res) {
            setTimeout(function () {
                _this.logging = false;
                _this.showError(res.error.message || res.message);
            }, 1000);
        });
    };
    LoginComponent.prototype.showError = function (e) {
        this.error = true;
        this.errorMsg = e;
    };
    LoginComponent.prototype.change = function (e) {
        var key = e.keyCode;
        var value = e.target.value;
        var name = e.target.name;
        switch (name) {
            case 'username': {
                this.store.dispatch({
                    type: __WEBPACK_IMPORTED_MODULE_3__reducers_user__["b" /* SET_USERNAME */], payload: value
                });
                break;
            }
            case 'password': {
                this.store.dispatch({
                    type: __WEBPACK_IMPORTED_MODULE_3__reducers_user__["a" /* SET_PASSWORD */], payload: value
                });
                break;
            }
            default: {
                break;
            }
        }
        if (this._userName.trim().length < 3 || this._password.trim().length < 3) {
            this.canLogIn = false;
        }
        else {
            this.canLogIn = true;
        }
        if (key === 13 && this.canLogIn) {
            this.login(e);
        }
    };
    return LoginComponent;
}());
LoginComponent = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["o" /* Component */])({
        selector: 'app-login',
        template: __webpack_require__("../../../../../src/app/components/login/login.component.html"),
        styles: [__webpack_require__("../../../../../src/app/components/login/login.component.css")]
    }),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_1__ngrx_store__["a" /* Store */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_1__ngrx_store__["a" /* Store */]) === "function" && _a || Object, typeof (_b = typeof __WEBPACK_IMPORTED_MODULE_4__services_user__["a" /* UserService */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_4__services_user__["a" /* UserService */]) === "function" && _b || Object, typeof (_c = typeof __WEBPACK_IMPORTED_MODULE_2__angular_router__["a" /* Router */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_2__angular_router__["a" /* Router */]) === "function" && _c || Object])
], LoginComponent);

var _a, _b, _c;
//# sourceMappingURL=login.component.js.map

/***/ }),

/***/ "../../../../../src/app/reducers/user.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return SET_USERNAME; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return SET_PASSWORD; });
/* unused harmony export SET_PK */
/* unused harmony export SET_PRIV */
/* unused harmony export initialState */
/* harmony export (immutable) */ __webpack_exports__["c"] = reducer;
var SET_USERNAME = '[User] Set Name';
var SET_PASSWORD = '[User] Set Password';
var SET_PK = '[User] Set Instagram Id';
var SET_PRIV = '[User] Set Privileges';
var initialState = {
    pk: 0,
    userName: '',
    password: '',
    priv: 0
};
function reducer(state, action) {
    if (state === void 0) { state = initialState; }
    switch (action.type) {
        case SET_USERNAME: {
            return Object.assign({}, state, { userName: action.payload });
        }
        case SET_PASSWORD: {
            return Object.assign({}, state, { password: action.payload });
        }
        case SET_PK: {
            return Object.assign({}, state, { pk: action.payload });
        }
        case SET_PRIV: {
            return Object.assign({}, state, { priv: action.payload });
        }
        default: {
            return state;
        }
    }
}
//# sourceMappingURL=user.js.map

/***/ }),

/***/ "../../../../../src/app/services/user.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return UserService; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_common_http__ = __webpack_require__("../../../common/@angular/common/http.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__ngrx_store__ = __webpack_require__("../../../../@ngrx/store/index.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__angular_router__ = __webpack_require__("../../../router/@angular/router.es5.js");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};




var UserService = (function () {
    function UserService(http, router, store) {
        var _this = this;
        this.http = http;
        this.router = router;
        this.store = store;
        this._isLogged = false;
        this._isAdmin = false;
        this.store.subscribe(function (s) {
            if (s.pk > 0) {
                _this._isLogged = true;
            }
            else {
                _this._isLogged = false;
            }
            if (s.priv === 1) {
                _this._isAdmin = true;
            }
            else {
                _this._isAdmin = false;
            }
        });
    }
    UserService.prototype.login = function (username, password) {
        return this.http.post('/index.php/auth', {
            username: username,
            password: password
        }).toPromise();
    };
    UserService.prototype.isLogged = function () {
        return this._isLogged;
    };
    UserService.prototype.isAdmin = function () {
        return this._isAdmin;
    };
    return UserService;
}());
UserService = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["C" /* Injectable */])(),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_1__angular_common_http__["a" /* HttpClient */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_1__angular_common_http__["a" /* HttpClient */]) === "function" && _a || Object, typeof (_b = typeof __WEBPACK_IMPORTED_MODULE_3__angular_router__["a" /* Router */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_3__angular_router__["a" /* Router */]) === "function" && _b || Object, typeof (_c = typeof __WEBPACK_IMPORTED_MODULE_2__ngrx_store__["a" /* Store */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_2__ngrx_store__["a" /* Store */]) === "function" && _c || Object])
], UserService);

var _a, _b, _c;
//# sourceMappingURL=user.js.map

/***/ }),

/***/ "../../../../../src/environments/environment.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return environment; });
// The file contents for the current environment will overwrite these during build.
// The build system defaults to the dev environment which uses `environment.ts`, but if you do
// `ng build --env=prod` then `environment.prod.ts` will be used instead.
// The list of which env maps to which file can be found in `.angular-cli.json`.
// The file contents for the current environment will overwrite these during build.
var environment = {
    production: false
};
//# sourceMappingURL=environment.js.map

/***/ }),

/***/ "../../../../../src/main.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_platform_browser_dynamic__ = __webpack_require__("../../../platform-browser-dynamic/@angular/platform-browser-dynamic.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__app_app_module__ = __webpack_require__("../../../../../src/app/app.module.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__environments_environment__ = __webpack_require__("../../../../../src/environments/environment.ts");




if (__WEBPACK_IMPORTED_MODULE_3__environments_environment__["a" /* environment */].production) {
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["_24" /* enableProdMode */])();
}
Object(__WEBPACK_IMPORTED_MODULE_1__angular_platform_browser_dynamic__["a" /* platformBrowserDynamic */])().bootstrapModule(__WEBPACK_IMPORTED_MODULE_2__app_app_module__["a" /* AppModule */])
    .catch(function (err) { return console.log(err); });
//# sourceMappingURL=main.js.map

/***/ }),

/***/ 0:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("../../../../../src/main.ts");


/***/ })

},[0]);
//# sourceMappingURL=main.bundle.js.map