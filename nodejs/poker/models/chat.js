var mongoose = require('mongoose'),
    Schema = mongoose.Schema;

var Chat = new Schema({
    message: String,
    pseudo: String,
    date: {type: Date, default: Date.now}
}, { collection: 'chatcollection' });

module.exports = mongoose.model('Chat', Chat);