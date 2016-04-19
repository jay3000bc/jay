/*
Author: jay@enterhelix.com
Date: 22/03/2015
*/
var express = require('express');
var routes = require('./routes');
var http = require('http');
var path = require('path');
var mongoose = require('mongoose');
var models = require('./models');
var exphbs = require('express-handlebars');

var dbUrl = process.env.MONGOHQ_URL || 'mongodb://@localhost::27017/node-signup';
var db = mongoose.connect(dbUrl, {safe: true});

var favicon = require('serve-favicon');
var session = require('express-session');
var logger = require('morgan');
var errorHandler = require('errorhandler');
var cookieParser = require('cookie-parser');
var bodyParser = require('body-parser');
var methodOverride = require('method-override');

var app = express();
app.locals.appTitle = "Test-Express";

//Configure Passport
//We will not use Passport Sessions
var passport = require('passport');
app.use(passport.initialize());
app.use(passport.session());

var initPassport = require('./passport/passport');
initPassport(passport);

var passportStrategies = require('./passport/passportStrategies');
passportStrategies(passport);

app.use(function(req,res,next){
   if(!models.Member)
       return next(new Error('No Models'));
   req.models = models;
   return next();
});

app.set('port', process.env.PORT || 5131);
// view engine setup
app.set('views', path.join(__dirname, 'views'));

app.engine('.hbs', exphbs({defaultLayout: 'single', extname: '.hbs'}));
app.set('view engine', '.hbs');

// uncomment after placing your favicon in /public
//app.use(favicon(path.join(__dirname, 'public', 'favicon.ico')));
app.use(logger('dev'));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: false }));
app.use(cookieParser('3CCC4ACD-6ED1-4844-9217-82131BDCB239'));
app.use(session({secret:'2C44774A-D649-4D44-9535-46E296EF984F', resave: true, saveUninitialized: true}));
app.use(methodOverride());
app.use(express.static(path.join(__dirname, 'public')));

//Authentication
app.use(function(req,res,next){
if(req.session.user)
  res.locals.user = true;
  next();
})
//Authorization
var authorize = function(req,res,next){
  if(req.session.user)
    return next();
  else
    res.redirect('login');
}
//Custom Passport Redirect
app.post('/login', function(req, res, next) {
  passport.authenticate('local', function(err, user, info) {
    if (err) { return next(err); }
    if (!user) { return res.redirect('/login'); }
    req.logIn(user, function(err) {
      if (err) { return next(err); }
      req.session.user = user.Email;
      return res.redirect('/');
    });
  })(req, res, next);
});

// error handlers
if('development' === app.get('env')){
    app.use(errorHandler());
}

app.get('/', authorize, routes.index);
app.get('/login', routes.member.login);
app.get('/logout', routes.member.logout);

app.get('/members', routes.member.show);
app.post('/members', routes.member.post);

app.get('/facebook', passport.authenticate('facebook'));
app.get('/facebookSuccess', passport.authenticate('facebook', {
            successRedirect : '/profile',
            failureRedirect : '/fbFailure'
        }));

app.get('/twitter', passport.authenticate('twitter'));
app.get('/twitterSuccess', passport.authenticate('twitter', {
            successRedirect : '/twitterProfile',
            failureRedirect : '/twitterFailure'
        }));

app.get('/google', passport.authenticate('google', {scope:['profile', 'email']}));
app.get('/googleSuccess', passport.authenticate('google', {
            successRedirect : '/googleProfile',
            failureRedirect : '/googleFailure'
        }));

app.get('/profile', function(req, res, next) {
  res.send('Successfully authenticated to facebook');
});

app.get('/fbFailure', function(req, res, next) {
  res.send('Failed to authenticate to facebook');
});

app.get('/twitterProfile', function(req, res, next) {
  res.send('Successfully authenticated to twitter');
});

app.get('/twitterFailure', function(req, res, next) {
  res.send('Failed to authenticate to twitter');
});

app.get('/googleProfile', function(req, res, next) {
  res.send('Successfully authenticated to Google');
});

app.get('/googleFailure', function(req, res, next) {
  res.send('Failed to authenticate to Google');
});

app.all('*', function(req,res){
    res.send(404);
});

http.createServer(app).listen(5131);
/*var server = http.createServer(app);
var boot = function(){
    server.listen(app.get('port'), function(){
        console.info('Express server listening on port' + app.get('port'));
    });
}

var shutdown = function(){
    server.close();
}

if(require.main === module){
    boot();
}
else{
    console.info('Running app as a module');
    exports.boot = boot;
    exports.shutdown = shutdown;
    exports.port = app.get('port');
}*/

