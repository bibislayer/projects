var mongoose = require('mongoose'),
    Schema = mongoose.Schema;

var schema = new Schema({
    user: { type: Schema.Types.ObjectId, ref: 'User' },
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
    allowedUsers: [{ type: Schema.Types.ObjectId, ref: 'User' }],
    password: String,
    created: {type: Date, default: Date.now},
    updated: {type: Date, default: Date.now}
}, { versionKey: false, collection: 'filescollection' });

var Files = mongoose.model('Files', schema);

module.exports = mongoose.model('Files', Files);