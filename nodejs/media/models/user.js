var mongoose = require('mongoose'),
    Schema = mongoose.Schema,
    passportLocalMongoose = require('passport-local-mongoose');

var User = new Schema({
    username: String,
    password: String,
    selected_folder: String,
    email: String,
    role: Array,
    salt: String,
    addressMac: []
}, { collection: 'usercollection' });

User.plugin(passportLocalMongoose);

module.exports = mongoose.model('User', User);