var mongoose = require('mongoose'),
    Schema = mongoose.Schema;

var Poker = new Schema({
    user: Array,
    table: Number,
    round: Number,
    money: Number,
    created: {type: Date, default: Date.now},
    updated: {type: Date, default: Date.now}
}, { collection: 'pokercollection' });

module.exports = mongoose.model('Poker', Poker);