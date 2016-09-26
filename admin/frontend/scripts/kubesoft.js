coorpobari.module('coorpobari')
.service('kubeServices', function($http, $rootScope, $q, $translate){

    var language = $translate.use();
    
    //----- SECCION PARA LA CARGA DE INFORMACION -----// 
    //----------------------- x ----------------------//
    //----------------------- x ----------------------//
    //----------------------- x ----------------------//

    //RETORNO LOS CLIENTES

    this.getCustomers = function (){

        var rute ='laravel/public/customers/customers';
        return getValues(rute);

    };

    //RETORNO LOS PRODUCTOS O SERVICIOS

    this.getProducts = function (){

        var rute = 'laravel/public/products/products';
        return getValues(rute);

    };

    //RETORNO LOS BANNERS

    this.getFlags = function (){

        var rute = 'laravel/public/flags/flags';
        return getValues(rute);

    };

    //RETORNO LAS NOTICIAS

    this.getNews = function (){

        var rute = 'laravel/public/reports/reports';
        return getValues(rute);
    };

    //RETORNO LA INFORMACION CORPORATIVA

    this.getInformacionCorporativa = function (){

        var rute = 'laravel/public/information/info';
        return getValues(rute);
    };

    //RETORNA UNA CANTIDAD DE NOTICIAS

    this.getCantNews = function (cant){
        var deferred = $q.defer();

        $http({
            method: 'GET',
            url: $rootScope.url_base+'laravel/public/reports/cant',
            params: {cant_reports: cant }
        }).success(function(result){
            deferred.resolve(result);
        });       

        return deferred.promise;

    };

    //RETORNO UN PRODUCTO ESPECIFICO 

    this.getProduct = function (product_id){

        var deferred = $q.defer();

        $http({
            method: 'GET',
            url: $rootScope.url_base+'laravel/public/products/product',
            params: {product_id: product_id }
        }).success(function(result){
            deferred.resolve(result);
        });       

        return deferred.promise;

    };

    //RETORNA UNA NOTICIA

    this.getReport = function (report_id){

        var deferred = $q.defer();

        $http({
            method: 'GET',
            url: $rootScope.url_base+'laravel/public/reports/report',
            params: {report_id: report_id }
        }).success(function(result){
            deferred.resolve(result);
        });       

        return deferred.promise;

    };


    this.getGalleries = function (product_id){

        var deferred = $q.defer();
        $http({
            url: $rootScope.url_base+'laravel/public/galleries/galleries',
            method: 'GET',
            params: {product_id: product_id}
        }).success(function(result){
            deferred.resolve(result.galleries);
        });
        return deferred.promise;

    };

    //METODO GENERICO PARA TOMAR OBJETOS DE UNA RUTA EN LARAVEL

    function getValues(rute){

        var deferred = $q.defer();

        $http({
            method: 'GET',
            url: $rootScope.url_base+rute
        }).success(function(result){
            deferred.resolve(result);
        });       

        return deferred.promise;

    };

    //----------------- FIN  SECCION -----------------//

    //------- SECCION PARA CONTROL DE MENU   ---------// 
    //----------------------- x ----------------------//
    //----------------------- x ----------------------//
    //----------------------- x ----------------------//



    this.activeMenu = function (source, item){

        source = inactiveAll(source);
        switch(item){
            case '/':
            case '':
            case 'home':
                source.home = 'current';                
                break;

            case 'products':
                source.products = 'current';
                break;
            case 'news':
                source.news='current';
                break;
            case 'contact':
                source.contact='current';
                break;                
            case 'company':
                source.company='current';
                break;
        }
        //console.log(source);
        return source;
    };

    function inactiveAll (source){

        source={
            home:'',
            products:'',
            news:'',
            contacts:'',
            company:''
        };

        return source;

    };

    //----------------- FIN  SECCION -----------------//






    //---------- SECCION PARA LA TRADUCCION ----------// 
    //----------------------- x ----------------------//
    //----------------------- x ----------------------//
    //----------------------- x ----------------------//

    //TRADUSCO LOS PRODUCTOS

    this.translateProducts = function (source){
        switch(language){
            case 'eng':
                for(i=0;i<source.length;i++){
                    source[i].name=source[i].name_eng;
                    source[i].features=source[i].features_eng;
                    source[i].resume = source[i].resume_eng;
                }
                break;
            case 'esp':
                for(i=0;i<source.length;i++){
                    source[i].name=source[i].name_esp;
                    source[i].features=source[i].features_esp;
                    source[i].resume = source[i].resume_esp;
                }
                break;        
        }
    };

    //TRADUSCO BANNERS

    this.translateFlags = function (source){
        switch(language){
            case 'eng':
                for(i=0;i<source.length;i++){
                    source[i].big_text=source[i].big_text_eng;
                    source[i].small_text=source[i].small_text_eng;
                }
                break;
            case 'esp':
                for(i=0;i<source.length;i++){
                    source[i].big_text=source[i].big_text_esp;
                    source[i].small_text=source[i].small_text_esp;
                }
                break;        
        }
    };

    //TRADUSCO NOTICIAS

    this.translateNews = function (source){
        switch(language){
            case 'eng':
                for(i=0;i<source.length;i++){
                    source[i].name=source[i].name_eng;
                    source[i].resume=source[i].resume_eng;
                    source[i].content=source[i].content_eng;
                    //source[i].day=getDay(source[i]);
                    //source[i].month=getMonth(source[i]);



                }
                break;
            case 'esp':
                for(i=0;i<source.length;i++){
                    source[i].name=source[i].name_esp;
                    source[i].resume=source[i].resume_esp;
                    source[i].content=source[i].content_esp;
                    //source[i].day=getDay(source[i]);
                    //source[i].month=getMonth(source[i]);

                }
                break;        
        }
    };


    //TRADUCIR UN UNICO PRODUCTO

    this.translateProduct = function (source){
        switch(language){
            case 'eng':

                source.name=source.name_eng;
                source.features=source.features_eng;
                source.resume = source.resume_eng;

                break;
            case 'esp':
                source.name=source.name_esp;
                source.features=source.features_esp;
                source.resume = source.resume_esp;

                break;        
        }
    };

    //TRADUCIR UNA UNICA NOTICIA

    this.translateReport = function (source){
        switch(language){
            case 'eng':

                source.name=source.name_eng;
                source.resume=source.resume_eng;
                source.content = source.content_eng;

                break;
            case 'esp':
                source.name=source.name_esp;
                source.resume=source.resume_esp;
                source.content = source.content_esp;

                break;        
        }
    };


    // FUNCTION QUE TRADUCE UN SOURCE DE PRODUCTOS 

    function translateProducts (source, lang){
        switch(lang){
            case 'eng':
                for(i=0;i<source.length;i++){
                    source[i].name=source[i].name_eng;
                    source[i].features=source[i].features_eng;
                    source[i].resume = source[i].resume_eng;
                }
                break;
            case 'esp':
                for(i=0;i<source.length;i++){
                    source[i].name=source[i].name_esp;
                    source[i].features=source[i].features_esp;
                    source[i].resume = source[i].resume_esp;
                }
                break;        
        }
        return source;
    };


    //TRADUSCO LOS BANNERS

    function translateFlags (source, lang){
        switch(lang){
            case 'eng':
                for(i=0;i<source.length;i++){
                    source[i].big_text=source[i].big_text_eng;
                    source[i].small_text=source[i].small_text_eng;
                }
                break;
            case 'esp':
                for(i=0;i<source.length;i++){
                    source[i].big_text=source[i].big_text_esp;
                    source[i].small_text=source[i].small_text_esp;
                }
                break;        
        }
        return source;
    };

    //Function que construye el dia de una noticia

    function getDay(source){
        var day = source.date.split('-')[2];
        return day;  

    };

    //Function que construye el mes de una noticia

    function getMonth(source){
        var month= source.date.split('-')[1];

        switch(month){
            case '01':
                month='Ene';
                break;
            case '02':
                month='Feb';
                break;
            case '03':
                month='Abr';
                break;
            case '04':
                month='Mar';
                break;
            case '05':
                month='May';
                break;
            case '06':
                month='Jun';
                break;
            case '07':
                month='Jul';
                break;
            case '08':
                month='Ago';
                break;
            case '09':
                month='Sep';
                break;
            case '10':
                month='Oct';
                break;
            case '11':
                month='Nov';
                break;
            case '12':   
                month='Dic';
                break;        
        }
        return month;
    };

});





