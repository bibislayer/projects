var mongoose = require('mongoose'),
    Schema = mongoose.Schema;

var PokerUser = new Schema({
    username: String,
    place: String,
    money: String
}, { collection: 'pokerusercollection' });

module.exports = mongoose.model('PokerUser', PokerUser);