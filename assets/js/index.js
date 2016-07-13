
window.currentQuizData;
window.currentQuestionIndex;
window.currentQuizScoreData;
window.currentQuizScoreDataForResultsPage;
window.user;
window.currentStatData;
window.barGraphData;
window.donutGraphData;
window.questionsCorrect;

window.hintsWhenIncorrect = {
	"Formicidae" : "Ants have 3 distinct body sections and a narrow waist.",
	"Hymenoptera" : "Bees and wasps have 2 pairs of wings with the hind wings smaller than the front wings.",
	"Coleoptera" : "Beetles have a straight line down the back where the two hard wing casings meet.",
	"Lepidoptera larvae" : "Some caterpillars are camouflaged and look like the twigs or leaves that they are found on.",
	"Lepidoptera" : "Butterflies and moths have four large wings covered by fine scales.",
	"Opiliones" : "Daddy longlegs have 8 very long legs, and they appear to have a single round body.",
	"Araneae" : "Spiders have 8 legs, and the abdomen is distinct from the rest of the body.",
	"Diptera" : "Flies have just a single pair of membranous wings.",
	"Orthoptera" : "Grasshoppers and crickets have large hind legs for jumping.",
	"Sternorrhyncha" : "Aphids and pysillids are quite small, usually a few millimeters at most, and are often green, yellow, orange in color.",
	"Auchenorrhyncha" : "Leafhoppers, planthoppers, and cicadas usually have a wide head relative to their body. Hoppers have wings folded tentlike over their back, while cicadas have large membranous wings.",
	"Heteroptera" : "True Bugs have semi-transparent wings which partially overlap on the back making a triangle or “X” shape on the back. The often have obvious pointy “shoulders” too."
};

window.difficultyMaps = {
	"Formicidae" : 1,
	"Hymenoptera" : 1,
	"Coleoptera" : 2,
	"Lepidoptera larvae" : 1,
	"Lepidoptera" : 1,
	"Opiliones" : 2,
	"Araneae" : 2,
	"Diptera" : 1,
	"Orthoptera" : 1, 
	"Sternorrhyncha" : 3,
	"Auchenorrhyncha" : 3,
	"Heteroptera" : 3
};

$(document).ready(function(){

	$.ajax("/api/login.php", {
        type: "GET",
        async: false,
        success: function (data, status, xhr){
            user = data;
			$("span.userID").html(user.userID);
            $("span.userName").html(user.name);
            $("span.userEmail").html(user.email);
        },
        error: function (xhr, status) {
        	window.user = {"userID":-1};
	    	$("#statsButton1").hide();
	    	$("#statsButton2").hide();
	    	$("#loginButton").fadeIn("fast");
        }
    });

    $("#signout").click(function (e) {
        $.ajax({
            url: "/api/login.php?signout=1",
            type: "GET",
            success: function (data, xhr, status) {
                window.location.replace("login.html");
            }
        });
    });

    //disable right click on images
    $('#quiz').on('contextmenu', 'img', function(e){ return false; });

	//$("#titleText1").delay(600).fadeIn(2200);
	//$("#titleText2").delay(1800).fadeIn(1000);
	//$("#beginButton").delay(2800).fadeIn(100);
});

function loginButton(){
	window.location.replace("login.html");
}

function statsButton(){
	currentStatData = [];
	$("#results").fadeOut("fast");
	$("#title").fadeOut("fast");
	$("#statistics").fadeIn("fast");
	$("#replayButton").hide();
	loadStats();
}

function loadStats(){
	$.getJSON("assets/backend.php", {userData: encodeURIComponent(user.userID), action: "statData"}, function(statDataFromServer){
		currentStatData = statDataFromServer;
		barGraphData = [];
		donutGraphData = [];
		loadChartData();
	});
}

function loadChartData(){
	console.log(currentStatData.length);
	
	if(currentStatData.length <= 8){
		for (var i = currentStatData.length-1; i >= 0; i--) {
			var element = {};
			var t = currentStatData[i].quiz_time.split(/[- :]/);
			var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
			element.label = (d.getMonth() + 1).toString() + "/" + d.getDate().toString();
			element.y = parseInt(currentStatData[i].score);
			element.z = currentStatData[i].percentile;
			barGraphData.push(element);
		}
	} else {
		for (var i = currentStatData.length-8; i <= currentStatData.length-1; i++) {
			var element = {};
			var t = currentStatData[i].quiz_time.split(/[- :]/);
			var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
			element.label = (d.getMonth() + 1).toString() + "/" + d.getDate().toString();
			element.y = parseInt(currentStatData[i].score);
			element.z = currentStatData[i].percentile;
			barGraphData.push(element);
		}
	}

	var p9 = 0, p8 = 0, p6 = 0, p4 = 0, p2 = 0, p0 = 0, count = 0, total = 0;
	for (var j = currentStatData.length - 1; j >= 0; j--) {
		var p = currentStatData[j].percentile 

		total = total + p;
		count ++;

		console.log("tot:" + total + " count:" + count);

		if(p > 90){
			p9++;
		}
		else if(p > 90){
			p8++;
		}
		else if(p > 60){
			p6++;
		}
		else if(p > 40){
			p4++;
		}
		else if(p > 20){
			p2++;
		}
		else p0++;
	}



	donutGraphData = [
				{ label: "91+",  y: p9 },
				{ label: "81-90", y: p8 },
				{ label: "61-80", y: p6 },
				{ label: "41-60",  y: p4 },
				{ label: "21-40",  y: p2 },
				{ label: "0-20",  y: p0 },
			];

	//console.log(barGraphData);
	//console.log(donutGraphData);

	var avgPercentile = total/count;

	buildCharts(avgPercentile);
}

function buildCharts(avgPercentile){

	var barGraph = new CanvasJS.Chart("barGraph", {
		animationEnabled: true,
		animationDuration: 2200,
		theme: "theme3",
		backgroundColor: "",
		title:{
			text: "Recent Scores",
			fontColor: "orange",
		},
		toolTip:{   
			content: "{name}: {z}"      
		},
		axisX:{
        	labelFontColor: "black",
      	},
      	axisY:{
        	labelFontColor: "black",
      	},
		data: [{
			type: "column",
			name: "Percentile",
			dataPoints: barGraphData
		}]
	});
	barGraph.render();

	var donutGraph = new CanvasJS.Chart("donutGraph", {
		animationEnabled: true,
		animationDuration: 1100,
		theme: "theme3",
		backgroundColor: "",
		title:{
			text: "Average Percentile:  " + Math.round(avgPercentile),
			fontColor: "orange",
		},
		data: [{
       		indexLabelFontColor: "black",
       		indexLabelLineColor: "white",
			type: "doughnut",
			dataPoints: donutGraphData
		}]
	});
	donutGraph.render();
	$("#statisticsTitle").empty();
	$("<div>", {
		html: "<h2>" + user.name + "'s Profile</h2>"
	}).appendTo("#statisticsTitle");

}

function beginButton(){
	$("#statistics").fadeOut("fast");
	$("#results").fadeOut("fast");
	$("#title").fadeOut("fast");
	$("#quiz").fadeIn("fast");
	loadQuiz();
}

function loadQuiz(){
	$.getJSON("assets/backend.php", {userData: encodeURIComponent(user.userID), action: "quizData"}, function(quizDataFromServer){
		currentQuizData = quizDataFromServer;
		currentQuestionIndex = 0;
		currentQuizScoreData = [];
		currentQuizScoreDataForResultsPage = [];
		loadNextQuestion();
	});
	questionsCorrect = 0;
}

function loadNextQuestion(){
	$(".tooltip").remove();
	questionData = currentQuizData[currentQuestionIndex];
	$("#imageDisplay").attr('src',questionData.imageURL);

	$("input[name='photoQuiz']").parent().removeClass('btn-success');
	$("input[name='photoQuiz']").parent().removeClass('btn-danger');

	$("#nextButton").hide();
	$("#submitButton").prop('disabled', true); 
	$("#submitButton").show("fast");
	$("label[for='photoQuiz']").attr('disabled', false);
}

function enableSubmit(){
	$("#submitButton").prop('disabled', false);
}

function submitButton(){

	$("#submitButton").hide();
	$("#replayButton").hide();
	$("#nextButton").show("fast");
	$("label[for='photoQuiz']").attr('disabled', true);
	$("input[name='photoQuiz']").parent().removeClass('active');

	var selected = $("input[name='photoQuiz']:checked");
	var correct = currentQuizData[currentQuestionIndex].correctAnswer;

	if(selected.val() == correct){
		//currentQuizScoreData.push(currentQuizData[currentQuestionIndex].difficulty);
		currentQuizScoreData.push(difficultyMaps[correct]);
		currentQuizScoreDataForResultsPage.push(["Correct",difficultyMaps[correct]]);
		selected.parent().addClass('btn-success');
		questionsCorrect++;
	}else{
		currentQuizScoreData.push(0);
		currentQuizScoreDataForResultsPage.push(["Incorrect",difficultyMaps[correct]]);
		selected.parent().addClass('btn-danger');
		$(":radio[value=\""+correct+"\"]").parent().addClass('btn-success');
		$(":radio[value=\""+correct+"\"]").parent().tooltip({placement:'top',trigger:'manual',title:hintsWhenIncorrect[correct]}); 
		$(":radio[value=\""+correct+"\"]").parent().tooltip('show');
	}

}

function nextButton(){

	currentQuestionIndex++;	
	if(currentQuestionIndex < currentQuizData.length){
		loadNextQuestion();
	}else if(user.userID != -1){
		var perc;
		perc = submitScoresToServer(user.userID);
		showResultsPage(false, perc);
	}else {
		showResultsPage(true, -1);
	}
}

/*Might be redundant instead of directly calling loadQuiz with the onClick, but both
	to match style and just in case we need it for something else */
function replayButton(){ 
	$("#resultImages").empty();
	beginButton();
}


function retrieveUserData(){
	//Gets logged in user data and pass it to score submission
	$.ajax({
        url: '/api/login.php',
        type: "GET",
        success: function(data, xhr, status){
        	window.user=data;
        	$("span.userID").html(user.userID);
            $("span.userName").html(user.name);
            $("span.userEmail").html(user.email);
        },
        error: function (xhr, status) {
        	window.user="";
        }
    });
}

function totalPointsPossible(){
	var totalPoints = 0; 
	for (var i = 0; i < currentQuizData.length; i++) {
		totalPoints += difficultyMaps[currentQuizData[i].correctAnswer];
	}
	return totalPoints; 
}

function submitScoresToServer(userData){
	//TODO: add necessary parameters and URL
	var percentile = 0;
	$.ajax({
		url: "assets/backend.php",
		type: "POST",
		dataType: 'text',
		async:false,
		data: {
			currentQuizScoreData:currentQuizScoreData, 
			userData:userData, 
			totalPointsPossible:totalPointsPossible(),
			action:"submit",
		}, 
		success: function(response, status, jqXHR){
			//alert("Response from server: " + response);
			//alert("Quiz scores saved on server!");
			percentile = response;
		},
		error: function (xhr, status) {
        	percentile = -1;
        }
	});
	return percentile;
}

function showResultsPage(wasGuest, perc){
	$("#quiz").fadeOut("fast");
	$("#results").fadeIn("slow");
	$("#replayButton").show("slow");
	var correctPoints = 0;
	var totalPoints = 0;
	for (var i = 0; i < currentQuizScoreDataForResultsPage.length; i++) {
		htmlToAdd = "";
		if(currentQuizScoreDataForResultsPage[i][0]==="Correct"){
			htmlToAdd = '<i class="fa fa-check-circle correctIcon" aria-hidden="true"><span class="incorrectLabel"></span></i>';
			correctPoints += currentQuizScoreDataForResultsPage[i][1];
		}else{
			htmlToAdd = '<i class="fa fa-times-circle incorrectIcon" aria-hidden="true"><span class="incorrectLabel"></span></i>';
		}
		totalPoints += currentQuizScoreDataForResultsPage[i][1];
		var $div = $("<div>", {
			html: "<div class=\"imageContainer\">"+htmlToAdd+"<img src='" + currentQuizData[i].imageURL + "' class='resultImg'></div>",
			"class": "col-sm-6 resultDiv"
		}).appendTo("#resultImages");
	}

	if (wasGuest){
		perc = loadGuestStats(correctPoints, totalPoints);
	}

	$("<div>", {
		html: "<h2 id=\"stats\">You got " + questionsCorrect + "/10 questions correct! Which gave you "+correctPoints+"/"+totalPoints+" points.</h2>",
		"class": "col-sm-6 resultDiv"
	}).appendTo("#resultImages");

	$("<div>", {
		html: "<h2 id=\"stats\"> Percentile:" + perc + "</h2>",
		"class": "col-sm-6 resultDiv"
	}).appendTo("#resultImages");

	if(wasGuest){
		$("<div>", {
			html: "<h2 id=\"stats\"><a href=\"/quiz/login.html\">Log in</a> or <a href=\"/quiz/login.html\">register</a> to take additional photo ID quizzes!</h2>",
			"class": "col-sm-6 resultDiv"
		}).appendTo("#resultImages");
	}

}

function loadGuestStats(tpe, tpp){
	var guestPercentile = 0;

	//$.getJSON("assets/backend.php", {userData: encodeURIComponent(user.userID), action: "guestPercentile", tpe: encodeURIComponent(tpe), tpp: encodeURIComponent(tpp)}, function(percentileFromServer){
		//guestPercentile = percentileFromServer;
		//return guestPercentile;
	//});
	
	$.ajax({
		url: "assets/backend.php",
		type: "GET",
		dataType: 'text',
		async:false,
		data: {
			action: "guestPercentile", 
			tpe:tpe,
			tpp:tpp,
		}, 
		success: function(response, status, jqXHR){
			guestPercentile = response;
		},
		error: function (xhr, status) {
        	guestPercentile = -1;
        }
	});
	return guestPercentile;
}