var mongoose = require('mongoose'),
    Schema = mongoose.Schema;

var Bug = new Schema({
    user: { type: Schema.Types.ObjectId, ref: 'User' },
    comment: String,
    category: Number,
    status: Number,
    created: {type: Date, default: Date.now},
    updated: {type: Date, default: Date.now}
}, { collection: 'bugcollection' });

module.exports = mongoose.model('Bug', Bug);