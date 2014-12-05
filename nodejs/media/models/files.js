var mongoose = require('mongoose'),
    Schema = mongoose.Schema;

var Files = new Schema({
    user: String,
    child: [{ type: Schema.Types.ObjectId, ref: 'Files' }],
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
    permissions: [],
    allowedEmails: Array,
    created: {type: Date, default: Date.now},
    updated: {type: Date, default: Date.now}
}, { collection: 'filescollection' });

module.exports = mongoose.model('Files', Files);