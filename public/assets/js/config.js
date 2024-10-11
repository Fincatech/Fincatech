let server = window.location.hostname;
var environment = 'd';

config = Object();
const serverHost = window.location.hostname;
if(environment == 'd')
{
    config.baseURLEndpoint = 'https://naslivin.synology.me/fincatech/api/v1/public/';
    config.baseURL = '/fincatech/';
}else{
    config.baseURLEndpoint = `https://${server}/api/v1/public/`;
    config.baseURL = `https://${server}/`;
}

//  Rutas endpoint
