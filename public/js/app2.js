var loadFile = function(event) {
  var output = document.getElementById('output');
  output.src = URL.createObjectURL(event.target.files[0]);
};

var loadFile1 = function (event) {
  var output = document.getElementById('output1');
  output.src = URL.createObjectURL(event.target.files[0]);
}
var loadFile2 = function (event) {
  var output = document.getElementById('output2');
  output.src = URL.createObjectURL(event.target.files[0]);
}
var loadFile3 = function (event) {
  var output = document.getElementById('output3');
  output.src = URL.createObjectURL(event.target.files[0]);
}
var loadFile4 = function (event) {
  var output = document.getElementById('output4');
  output.src = URL.createObjectURL(event.target.files[0]);
}

if(document.getElementById('profileform')) {

    new Vue({
        el: '#profileform',
        data: {
            email: '',
            username: ''
        },
        methods: {
            getter(id) {
                return document.getElementById(id);
            },
            validationprofile() {
                var everythinggood = true;
                var newusername = this.getter('username').value;
                var newemail = this.getter('email').value;


                //username
                if (newusername.length < 4 || newusername.length > 40) {
                    everythinggood = false;
                    var eusername = '';
                    var eusername = 'Length of the username must contain from 4 to 40 characters!';

                } else {
                    var oldusername = this.getter('username').getAttribute('data-username');
                    fetch('/checkuser/' + newusername).then(Response => Response.json()).then(function (data) {
                        if (oldusername != newusername) {
                            if (data == 0) {
                                everythinggood = false;
                                eusername = 'Username already taken!';
                                document.getElementById('usernameerror').innerHTML = '';
                                document.getElementById('usernameerror').innerHTML += eusername;
                            }
                        }
                    });
                }

                //email
                if (newemail === '') {
                    everythinggood = false;
                    var eemail = 'Please fill in this field!';
                } else {
                    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    var validateemail = re.test(newemail);
                    var oldemail = this.getter('email').getAttribute('data-email');
                    if (validateemail === false) {
                        everythinggood = false;
                        var eemail = 'Enter valid E-Mail address!';
                    } else {
                        fetch('/checkemail/' + newemail).then(Response => Response.json()).then(function (data) {
                            if (oldemail != newemail) {
                                if (data == 0) {
                                    everythinggood = false;
                                    eemail = 'E-Mail address already taken!';
                                    document.getElementById('emailerror').innerHTML = '';
                                    document.getElementById('emailerror').innerHTML += eemail;
                                }
                            }
                        }).then(function () {
                            if (everythinggood == true) {
                                document.getElementById('profileform').submit();
                            }
                        });
                    }
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
            }
        }
    });

}

function showmore(event) {
    var quizcount = event.target.getAttribute('data-quizcount');
    var counter = 1;
    for(var i = 1; i <= quizcount; i++) {
        if(document.getElementById('post' + i).classList.contains('displaynone') && counter <= 10) {
            counter++;
            document.getElementById('post' + i).classList.add('fade_in_anim');
            doSetTimeout(i, counter);
            if(i == quizcount) {
                setTimeout(function () {
                    event.target.classList.add('disabledbtn');
                }, 300);
            }
        }
    }
}
function doSetTimeout(i, counter) {

    //little more time await for first post for comfort
    counter += 4;

    setTimeout(function(){
         document.getElementById('post' +i).classList.remove('displaynone');
         document.getElementById('post' +i).classList.add('posthome');
    },counter * 60);
}
