var mongoose = require('mongoose'),
    Schema = mongoose.Schema;

var Sondage = new Schema({
    user: { type: Schema.Types.ObjectId, ref: 'User' },
    type: String,
    text: String,
    note: Number,
    created: {type: Date, default: Date.now},
    updated: {type: Date, default: Date.now}
}, { collection: 'sondagecollection' });

module.exports = mongoose.model('Sondage', Sondage);