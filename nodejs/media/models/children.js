var mongoose = require('mongoose'),
    Files = require('./files'),
    Schema = mongoose.Schema;

var Children = new Schema({
    user: String,
    child: [Files],
    level: Number,
    root_id: String,
    parent_id: String,
    name: String,
    type: String,
    convert:Array,
    path: String,
    size: Number,
    time: Number,
    access: Number,
    allowedEmails: Array,
    created: {type: Date, default: Date.now},
    updated: {type: Date, default: Date.now}
}, { collection: 'filescollection' });

module.exports = mongoose.model('Children', Children);