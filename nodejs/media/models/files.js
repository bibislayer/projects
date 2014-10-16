var mongoose = require('mongoose'),
    Schema = mongoose.Schema;

var Files = new Schema({
    user: String,
    name: String,
    type: Array,
    path: Array,
    size: Number,
    time: Number,
    created: {type: Date, default: Date.now},
    updated: {type: Date, default: Date.now}
}, { collection: 'filescollection' });

module.exports = mongoose.model('Files', Files);