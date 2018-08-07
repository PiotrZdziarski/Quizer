var blocker = false;
if(document.getElementById('quizscripts')) {

    var quizscripts = new Vue({
        el: '#quizscripts',
        data: {
            chooseblock: false,
            score: 0,
            questioncount: 0,
        },
        delimiters: ['<{', '}>'],
        methods: {
            answerchoosen: function (event) {
                var correct_answer = event.target.getAttribute('data-correctanswer');
                var questionnumber = event.target.getAttribute('data-questionnumber');
                var questioncount = event.target.getAttribute('data-questioncount');
                this.questioncount = questioncount;
                questioncount = parseInt(questioncount);
                var quizid = event.target.getAttribute('data-quizid');
                var nextquestion = parseInt(questionnumber);
                nextquestion++;
                if (correct_answer == 1 && blocker === false) {
                    event.target.classList.add('answergreen');
                    this.score++;
                    blocker = true;

                    if (questionnumber < questioncount) {
                        this.nextquestion(questionnumber, nextquestion);
                    } else {
                        this.endquote(quizid, questionnumber);
                    }
                } else if (blocker === false) {
                    event.target.classList.add('answerred');
                    $('.correctanswer' + questionnumber).addClass('answergreen');
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
                $('#question' + questionnumber).delay(2500).fadeOut(500, function () {
                    if (blocker != false) {
                        blocker = false;
                    }
                });
                $('#question' + nextquestion).delay(3000).fadeIn(500);
                $("html, body").delay(3000).animate({scrollTop: 250}, "slow");
            },
            //end of next questionp

            endquote(quizid, questionnumber) {
                fetch("checkscore/" + this.score + '/' + quizid).then(function (Response) {
                    return Response.json();
                }).then(function (data) {
                    document.getElementById('endingscore' + data).innerHTML = quizscripts.score + '/' + quizscripts.questioncount;
                    $('#question' + questionnumber).delay(2500).fadeOut(500);
                    $('#endingquote' + data).delay(3000).fadeIn(500);
                    $("html, body").delay(3000).animate({scrollTop: 240}, "slow");
                });
            }
            //end of endqoute
        }
    });

}

function reloadBrowser() {
    location.reload();
}
function changeaftercollapse(event, id) {
    $('#collapseQuiz' + id).on('hide.bs.collapse', function () {
        document.getElementById('angle' + id).classList.remove('fa-angle-up');
        document.getElementById('angle' + id).classList.add('fa-angle-down');
    })
    $('#collapseQuiz' + id).on('show.bs.collapse', function () {
        document.getElementById('angle' + id).classList.remove('fa-angle-down');
        document.getElementById('angle' + id).classList.add('fa-angle-up');
    })
}

