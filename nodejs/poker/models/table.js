var mongoose = require('mongoose'),
    Schema = mongoose.Schema;

var Poker = new Schema({
    user: Number,
    pseudo: String,
    date: {type: Date, default: Date.now}
}, { collection: 'chatcollection' });

module.exports = mongoose.model('Chat', Chat);