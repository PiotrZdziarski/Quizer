$(document).ready(function() {
  $("#owl-demo").owlCarousel({
    items : 5,
    lazyLoad : true,
    navigation : true
  });
  $('.fadeinimage').fadeIn(1500);
});

$(function () {
  $('[data-toggle="tooltip"]').tooltip();
});
new Vue({
    el:'.carousel_maindiv',
    data: {
        showelement: false,
    },
    methods: {
        mouseOverFunction() {
            this.showelement  = true;
        },
        mouseLeaveFunction(){
            this.showelement = false;
        }
    }
});
var registerform = new Vue({
    el: '#registerform',
    data: {
        username: '',
        email: '',
        password: '',
        confirm_password: '',
    },
    methods: {
        validation() {
            var everythinggood = true;
            if (this.username.length < 4 || this.username.length > 40) {
                var eusername = '';
                everythinggood = false;
                if (this.username === '') {
                    eusername = 'Please fill in this field!';
                } else {
                    eusername = "Length of the username must contain from 4 to 40 characters!";
                }
            } else {
                fetch('/checkuser/' + this.username).then(Response => Response.json()).then(function (data) {
                    if (data == 0) {
                        everythinggood = false;
                        eusername = 'Username already taken!';
                        document.getElementById('usernameerror').innerHTML = '';
                        document.getElementById('usernameerror').innerHTML += eusername;
                    }
                }).then(function () {
                    if (everythinggood == true) {
                        document.getElementById('registerform').submit();
                    }
                });
            }


            if (this.email === '') {
                everythinggood = false;
                var eemail = 'Please fill in this field!';
            } else {
                var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                var validateemail = re.test(this.email);
                if (validateemail === false) {
                    everythinggood = false;
                    var eemail = 'Enter valid E-Mail address!';
                } else {
                    fetch('/checkemail/' + this.email).then(Response => Response.json()).then(function (data) {
                        if (data == 0) {
                            everythinggood = false;
                            eemail = 'E-Mail address already taken!';
                            document.getElementById('emailerror').innerHTML = '';
                            document.getElementById('emailerror').innerHTML += eemail;
                        }
                    }).then(function () {
                        if (everythinggood == true) {
                            document.getElementById('registerform').submit();
                        }
                    });
                }
            }

            if (this.password === '') {
                everythinggood = false;
                var epassword = 'Please fill in this field!';
            } else if (this.password.length < 4 || this.password.length > 40) {
                everythinggood = false;
                var epassword = "Length of the password must contain from 4 to 40 characters!";
            }

            if (this.confirm_password === '') {
                everythinggood = false;
                var econfirmpassword = 'Please fill in this field!';
            } else if (this.confirm_password !== this.password) {
                everythinggood = false;
                var econfirmpassword = 'Passwords are not identical!';
            }
            if (typeof eusername !== 'undefined') {
                document.getElementById('usernameerror').innerHTML = '';
                document.getElementById('usernameerror').innerHTML += eusername;
            } else {
                document.getElementById('usernameerror').innerHTML = '';
            }

            if (typeof eemail !== 'undefined') {
                document.getElementById('emailerror').innerHTML = '';
                document.getElementById('emailerror').innerHTML += eemail;
            } else {
                document.getElementById('emailerror').innerHTML = '';
            }
            if (typeof epassword !== 'undefined') {
                document.getElementById('passworderror').innerHTML = '';
                document.getElementById('passworderror').innerHTML += epassword;
            } else {
                document.getElementById('passworderror').innerHTML = '';
            }

            if (typeof econfirmpassword !== 'undefined') {
                document.getElementById('confirmpassworderror').innerHTML = '';
                document.getElementById('confirmpassworderror').innerHTML += econfirmpassword;
            } else {
                document.getElementById('confirmpassworderror').innerHTML = '';
            }
        }
    }
});

//  why is this needed? I am asking myself too...
    document.getElementById('btnform').onclick = function () {
        registerform.validation();
    }


var registerpreventer = document.getElementById('registerform');
if (registerpreventer.addEventListener) {
    registerpreventer.addEventListener("submit", function(evt) {
        evt.preventDefault();
        window.history.back();
    }, true);
}
else {
    registerpreventer.attachEvent('onsubmit', function(evt){
        evt.preventDefault();
        window.history.back();
    });
}









