environment = 'd';

config = Object();

if(environment == 'd')
{
    config.baseURLEndpoint = 'http://localhost/fincatech/api/v1/public/';
    config.baseURL = '/fincatech/';
}else{
    config.baseURLEndpoint = 'https://beta.fincatech.es/api/v1/public/';
    config.baseURL = 'https://beta.fincatech.es/';
}

//  Rutas endpoint
