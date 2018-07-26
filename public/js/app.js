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
var quizscripts = new Vue({
    el: '#psychotestscripts',
    data: {
        chooseblock: false,
        score: 0,
    },
    delimiters: ['<{', '}>'],
    methods:  {
        answerchoosen: function(event) {
            var answerscore = event.target.getAttribute('data-answerscore');
            var questionnumber = event.target.getAttribute('data-questionnumber');
            var questioncount = event.target.getAttribute('data-questioncount');
            var quizid = event.target.getAttribute('data-quizid');
            var maxscore = event.target.getAttribute('data-quizid');
            var nextquestion = parseInt(questionnumber);
            nextquestion++;
            if (blocker === false) {
                event.target.classList.add('answerblue');
                this.score += parseInt(answerscore);
                blocker = true;

                if(questionnumber < questioncount) {
                    this.nextquestion(questionnumber, nextquestion);
                } else {
                    this.endquote(quizid, questionnumber);
                }
            }
        //  end of anwerchoosen
        },
        nextquestion(questionnumber, nextquestion, tempScrollTop) {
            $('#question' + questionnumber).delay(900).fadeOut(500,  function () {
            if(blocker != false) {
                blocker = false;
            }
            });
            $('#question' + nextquestion).delay(1500).fadeIn(500);
            $("html, body").delay(1500).animate({ scrollTop: 250 }, "slow");
        },
        //end of next questionp
        endquote(quizid, questionnumber,tempScrollTop) {
            fetch("checkscore/" + this.score + '/' + quizid).then(function (Response) {
                return Response.json();
            }).then(function(data) {
                $('#question' + questionnumber).delay(2000).fadeOut(500);
                $('#endingquote' + data).delay(2500).fadeIn(500);
                $("html, body").delay(2500).animate({ scrollTop: 240 }, "slow");
            });
        }
        //end of endqoute
    }
});