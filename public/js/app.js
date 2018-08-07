function preventsubmit() {
    var questionvalue = document.getElementById('question').value;
    var answer1value = document.getElementById('answer1').value;
    var answer2value = document.getElementById('answer2').value;
    if(questionvalue != '' && answer1value != '' && answer2value != '') {
        document.getElementById('formcreatequiz').submit();
        document.getElementById('btnsubmit').disabled = true;
    }
}

function preventsubmitresult() {
    document.getElementById('formcreatequiz').submit();
    document.getElementById('btnsubmit').disabled = true;
}


var blocker = false;
if(document.getElementById('psychotestscripts')) {

    var quizscripts = new Vue({
        el: '#psychotestscripts',
        data: {
            chooseblock: false,
            score: 0,
            endingscore: 0
        },
        delimiters: ['<{', '}>'],
        methods: {
            answerchoosen: function (event) {
                var answerscore = event.target.getAttribute('data-answerscore');
                var questionnumber = event.target.getAttribute('data-questionnumber');
                var questioncount = event.target.getAttribute('data-questioncount');
                ;
                questioncount = parseInt(questioncount);
                var quizid = event.target.getAttribute('data-quizid');
                var maxscore = event.target.getAttribute('data-quizid');
                var nextquestion = parseInt(questionnumber);
                nextquestion++;
                if (blocker === false) {
                    event.target.classList.add('answerblue');
                    this.score += parseInt(answerscore);
                    blocker = true;

                    if (questionnumber < questioncount) {
                        this.nextquestion(questionnumber, nextquestion);
                    } else {
                        this.endquote(quizid, questionnumber);
                    }
                }
                //  end of anwerchoosen
            },
            nextquestion(questionnumber, nextquestion) {
                $('#question' + questionnumber).delay(1000).fadeOut(500, function () {
                    if (blocker != false) {
                        blocker = false;
                    }
                });
                $('#question' + nextquestion).delay(1500).fadeIn(500);
                $("html, body").delay(1500).animate({scrollTop: 250}, "slow");
            },
            //end of next questionp
            endquote(quizid, questionnumber) {
                fetch("checkscorepsychotest/" + this.score + '/' + quizid).then(function (Response) {
                    return Response.json();
                }).then(function (data) {
                    var endingscorepercentage = parseInt(data[1]);
                    endingscorepercentage = String(endingscorepercentage);
                    document.getElementById('endingscore' + data[0]).innerHTML = endingscorepercentage + '%';
                    $('#question' + questionnumber).delay(2000).fadeOut(500);
                    $('#endingquote' + data[0]).delay(2500).fadeIn(500);
                    $("html, body").delay(2500).animate({scrollTop: 240}, "slow");
                });
            }
            //end of endqoute
        }
    });

}

function redirectalsolike(id)
{
    window.location.replace('/quiz/' + id);
}

function redirectcategory(id)
{
    window.location.replace('/category/' + id);
}

new Vue({
    el: '#categories',
    data: {
        show1: false,
        show2: false,
        show3: false,
        show4: false,
        show5: false,
        show6: false,
        show7: false,
        show8: false,
        show9: false,
        show10: false,
        show11: false,
        show12: false,
        show13: false,
        show14: false,
        show15: false,
        show16: false,
    },
    mounted: function () {
        var self = this;
        setTimeout(function () {
            self.show1 = true
        }, 60);
        setTimeout(function () {
            self.show2 = true
        }, 120);
        setTimeout(function () {
            self.show3 = true
        }, 180);
        setTimeout(function () {
            self.show4 = true
        }, 240);
        setTimeout(function () {
            self.show5 = true
        }, 300);
        setTimeout(function () {
            self.show6 = true
        }, 360);
        setTimeout(function () {
            self.show7 = true
        }, 420);
        setTimeout(function () {
            self.show8 = true
        }, 480);
        setTimeout(function () {
            self.show9 = true
        }, 540);
        setTimeout(function () {
            self.show10 = true
        }, 600);
        setTimeout(function () {
            self.show11 = true
        }, 660);
        setTimeout(function () {
            self.show12 = true
        }, 720);
        setTimeout(function () {
            self.show13 = true
        }, 780);
        setTimeout(function () {
            self.show14 = true
        }, 840);
        setTimeout(function () {
            self.show15 = true
        }, 900);
        setTimeout(function () {
            self.show16 = true
        }, 960);
    }
})



