exports.chat = function(req, res) {
    res.render('chat.ejs', {title: 'search', req: req});
};