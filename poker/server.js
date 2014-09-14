/* 
 * All right reserved Dev-Monkey.
 */
// Code identique au précédent
var http = require('http');
var url = require("url");
var querystring = require('querystring');

var setServer = function(req, res) {
    var page = url.parse(req.url).pathname;
    var params = querystring.parse(url.parse(req.url).query);
    res.writeHead(200, {"Content-Type": "text/html"});
    switch (page) {
        case "/":
            res.write('Vous êtes à l\'accueil, que puis-je pour vous ?');
            break;
        case "/sous-sol":
            res.write('Vous êtes dans la cave à vins, ces bouteilles sont à moi !');
            break;
        case "/etage/1/chambre":
            res.write('Hé ho, c\'est privé ici !');
            break;
        default:
            res.writeHead(404, {"Content-Type": "text/html"});
            res.write('Not found');
            break;
    }
    if ('prenom' in params && 'nom' in params) {
        res.write('<br />Vous vous appelez ' + params['prenom'] + ' ' + params['nom']);
    }
    else {
        res.write('<br />Vous devez bien avoir un prénom et un nom, non ?');
    }
    res.end();
}

var server = http.createServer(setServer);
server.listen(8080);
