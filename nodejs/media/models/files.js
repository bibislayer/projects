var mongoose = require('mongoose'),
    Schema = mongoose.Schema;

var Files = new Schema({
    user: String,
    level: Number,
    root_id: String,
    parent_id: String,
    name: String,
    type: String,
    convert:Array,
    path: String,
    size: Number,
    time: Number,
    permissions: Array,
    created: {type: Date, default: Date.now},
    updated: {type: Date, default: Date.now}
}, { collection: 'filescollection' });

module.exports = mongoose.model('Files', Files);