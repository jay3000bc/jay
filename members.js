/*
Author: jay@enterhelix.com
Date: 22/03/2015
*/
var bCrypt = require('bcryptjs');


exports.show = function(req,res,next){
		res.render('add-member', {'name': req.session.user});
}
exports.post = function(req,res,next){
	if(req.body.fname == "" || req.body.lname == "")
		return res.render("add-member", {error: 'Please fill out the mandatory fields'})
	var member = new req.models.Member({
		'First Name': req.body.fname,
		'Last Name': req.body.lname,
		'Username': req.body.uname,
		'Password': createHash(req.body.passwd),
		'Email': req.body.email,
		'Address': req.body.address,
		'Mobile': req.body.mobile
	})
	member.save(function(error,postResponse){
		if(error)
			return next(error)
		res.send(postResponse + salt)
	})
}
exports.login = function(req, res, next){
	res.render('login');
}
exports.logout = function(req,res,next){
	req.session.destroy();
	res.redirect('login');
}
exports.validateLogin = function(req,res,next){
    //successRedirect: '/loginSuccess',
    //failureRedirect: '/loginFailure'
    req.session.user = req.user.Username;
    res.redirect('members');
    //res.send(req.user.Username);
}

/*var hash = function(password){
        return crypto.createHash ("md5").update(password).digest("hex");
}
exports.genHash = function(password){
        return crypto.createHash ("md5").update(password).digest("hex");
}*/
var salt = bCrypt.genSaltSync(10);

var createHash = function createHash(password){
        return bCrypt.hashSync(password, salt);
    }

/*exports.comparePassword = function(password, storedPassword){
	return bCrypt.compareSync(password, storedPassword);
}*/