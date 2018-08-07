new Vue({
    el: '#formcreatequiz',
    methods: {
        addanswer() {
            for(var i = 3; i<=10; i++) {
                if(document.getElementById("answerdiv" + i).classList.contains('displaynone')) {
                    document.getElementById("answerdiv" + i).classList.remove('displaynone');
                    break;
                }
            }
        },
        removeanswer(event){
            for(var i = 10; i>=3; i--) {
                if(!document.getElementById("answerdiv" + i).classList.contains('displaynone')) {
                    document.getElementById("answerdiv" + i).classList.add('displaynone');
                    document.getElementById('answer' + i).value= '';
                    break;
                }
            }
            for(var j = 1; j <= 10; j++) {
                if(document.getElementById('radioanswer' + j).checked == true) {
                    document.getElementById('radioanswer' + j).checked = false;
                    document.getElementById('correctanswer' + j).classList.add('displaynone');
                    document.getElementById('radioanswer1').checked = true;
                    document.getElementById('correctanswer1').classList.remove('displaynone');
                }
            }
        },
        removeanswerpsychotest() {
            for(var i = 10; i>=3; i--) {
                if(!document.getElementById("answerdiv" + i).classList.contains('displaynone')) {
                    document.getElementById("answerdiv" + i).classList.add('displaynone');
                    document.getElementById('answer' + i).value= '';
                    document.getElementById('radioanswer' + i).value = '';
                    break;
                }
            }
        },
        correctanswer(e) {
            for(var i = 1; i<=10; i++) {
                if(document.getElementById("radioanswer" + i).checked == true) {
                    document.getElementById("correctanswer" + i).classList.remove('displaynone');
                } else {
                    document.getElementById('correctanswer' + i).classList.add('displaynone');
                }
            }
        }
    }
})