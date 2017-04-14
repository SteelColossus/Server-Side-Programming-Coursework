<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex, nofollow" />
<link rel="stylesheet" type="text/css" href="style.css">
<style type="text/css">
.resultscell {
	text-align: center;
	width: 120px;
}
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
var numValid = 0;

function getCookie(cname)
{
	var cdef = cname + "=";
	var cookies = decodeURIComponent(document.cookie).split(';');
	
	for (var i = 0; i < cookies.length; i++)
	{
		var c = cookies[i];
		
		if (c.indexOf(cdef) != -1)
		{
			return c.substring(c.indexOf(cdef) + cdef.length);
		}
	}
	
	return "";
}

function updateCookie(cname, cvalue)
{
	document.cookie = cname + "=" + cvalue;
}

function updateCookies()
{
	updateCookie("country1", $("#country1").val());
	updateCookie("country2", $("#country2").val());
	updateCookie("country3", $("#country3").val());
	updateCookie("country4", $("#country4").val());
}

function loadCookies()
{
	c1 = getCookie("country1");
	
	if (c1 != "")
	{
		console.log("Cookie found for country 1: " + c1);
		$("#country1").val(c1);
	}
	
	c2 = getCookie("country2");
	
	if (c2 != "")
	{
		console.log("Cookie found for country 2: " + c2);
		$("#country2").val(c2);
	}
	
	c3 = getCookie("country3");
	
	if (c3 != "")
	{
		console.log("Cookie found for country 3: " + c3);
		$("#country3").val(c3);
	}
	
	c4 = getCookie("country4");
	
	if (c4 != "")
	{
		console.log("Cookie found for country 4: " + c4);
		$("#country4").val(c4);
	}
}

function isValidISOFormat(value)
{
	var exp = new RegExp("^[A-Za-z]{3}$");
	return exp.test(value);
}

function updateColumnVisibility()
{
	var twoValid = (isValidISOFormat($("#country1").val()) && isValidISOFormat($("#country2").val()) || isValidISOFormat($("#country3").val()));
	var threeValid = (twoValid && isValidISOFormat($("#country3").val())) || isValidISOFormat($("#country4").val());
	
	$("#inputtable th:nth-child(3), #inputtable td:nth-child(3)").css("display", (twoValid ? "" : "none"));
	$("#inputtable th:nth-child(4), #inputtable td:nth-child(4)").css("display", (threeValid ? "" : "none"));
}

function updateResults()
{
	updateColumnVisibility()
	
	var c1 = $("#country1").val();
	var c2 = $("#country2").val();
	var c3 = $("#country3").val();
	var c4 = $("#country4").val();
	
	var data = new Object();
	
	if (isValidISOFormat(c1))
	{
		data.country1 = c1;
	}
	
	if (isValidISOFormat(c2))
	{
		data.country2 = c2;
	}
	
	if (isValidISOFormat(c3))
	{
		data.country3 = c3;
	}
	
	if (isValidISOFormat(c4))
	{
		data.country4 = c4;
	}
	
	var currentNumValid = Object.keys(data).length;
	
	if (currentNumValid >= 2 && numValid != currentNumValid)
	{
		numValid = currentNumValid;
		
		$.get("table.php", data, function(json) {
			console.log(json);
			updateComparisonTable(json['countries']);
			updateCyclistsTable(json['cyclists']);
		}, "json");
	}
}

function getNumDigits(num)
{
	return Math.floor(Math.log10(num) + 1);
}

function round(number, dp)
{
	return +(Math.round(number + "e+" + dp) + "e-" + dp);
}

function numToShortForm(num)
{
	num = parseInt(num);
	
	if (num <= 0) return num.toString();
		
	dg = getNumDigits(num);
	shortNum = num / Math.pow(10, Math.floor((dg - 1) / 3) * 3);
	shortDg = getNumDigits(shortNum);
	shortNum = round(shortNum, 3 - shortDg);
	
	text = shortNum.toString();
	
	if (dg > 12)
	{
		text += " trillion";
	}
	else if (dg > 9)
	{
		text += " billion";
	}
	else if (dg > 6)
	{
		text += " million";
	}
	else if (dg > 3)
	{
		text += " thousand";
	}
	
	return text;
}

function updateComparisonTable(json)
{
	var table = document.createElement("TABLE");
	var rows = [];
	
	var highestIndexes = [];
	
	for (var prop in json[0])
	{		
		var highest = -1;

		for (var r = 0; r < json.length; r++)
		{
			var value = json[r][prop];
			
			if (!$.isNumeric(value))
			{
				highestIndexes[prop] = -1;
				break;
			}
			else
			{
				value = parseInt(value);
			}
			
			if (highest == -1 || value > highest)
			{
				highest = value;
				highestIndexes[prop] = r;
			}
		}
	}
	
	for (var r = 0; r < json.length; r++)
	{
		var country = json[r];
		var c = 0;
		
		for (var prop in country)
		{
			if (r == 0)
			{
				var newRow = $("<tr>");
				rows[c] = newRow;
				$(table).append(newRow);
				
				var titleCell = $("<td>");
				
				if (c > 0)
				{
					emoji = "";
					
					switch (prop)
					{
						case "total":
							emoji = "🏅";
							break;
						case "gold":
							emoji = "🥇";
							break;
						case "silver":
							emoji = "🥈";
							break;
						case "bronze":
							emoji = "🥉";
							break;
					}
					
					$(titleCell).html(emoji + " " + prop + " " + emoji);
				}
				
				$(titleCell).addClass("center");
				if (prop == "total") $(titleCell).css("font-weight", "bold");
				$(newRow).append(titleCell);
				
				if (prop == "total")
				{
					var blankRow = $("<tr>");
					rows[c+1] = blankRow;
					$(table).append(blankRow);
					
					var emptyCell = $("<td>");
					$(emptyCell).prop("colspan", json.length + 1);
					$(emptyCell).html("&nbsp;");
					$(blankRow).append(emptyCell);
				}
			}
			
			var newCell = $((c == 0) ? "<th>" : "<td>");
			
			var text = country[prop];
			if ($.isNumeric(text)) text = numToShortForm(text);
			if (prop == "gdp") text = "$" + text;
			$(newCell).html(text);
			
			$(newCell).addClass("resultscell");
			if (prop == "total") $(newCell).css("font-weight", "bold");
			if (r == highestIndexes[prop]) $(newCell).css("background-color", "#0fff00");
			$(rows[c]).append(newCell);
			
			c += (prop == "total" ? 2 : 1);
		}
	}
	
	$("#comptable").html(table);
}

function updateCyclistsTable(json)
{
	var table = document.createElement("TABLE");
	$(table).prop("border", "1");
	var currentRow;
	var prevID = "";
	
	for (var r = 0; r < json.length; r++)
	{
		var cyclist = json[r];
		
		if (prevID == "" || prevID != cyclist['iso_id'])
		{
			currentRow = $("<tr>");
			$(table).append(currentRow);
			
			var titleCell = $("<th>");
			$(titleCell).html(cyclist['country_name']);
			$(currentRow).append(titleCell);
			
			prevID = cyclist['iso_id'];
		}
		
		var newCell = $("<td>");
		$(newCell).html(cyclist['name']);
		$(currentRow).append(newCell);
	}
	
	$("#cyctable").html(table);
}

$(document).ready(function() {
	$("#country1").on("input", function() {
		updateResults();
	});
	
	$("#country2").on("input", function() {		
		updateResults();
	});
	
	$("#country3").on("input", function() {		
		updateResults();
	});
	
	$("#country4").on("input", function() {		
		updateResults();
	});
	
	$(window).on("beforeunload", function() {
		updateCookies();
	});
	
	loadCookies();
	
	updateResults();
});
</script>
<title>Country Comparison</title>
</head>
<body>
<h3 class="center">COA123 - Server-Side Programming</h3>
<h2 class="center">Individual Coursework - Olympic Cyclists</h2>
<h1 class="center">Task 4 - Country Comparison (view.php)</h1>
  <table>
  <tr>
  <td>
<div class="fixed">~  __0
 _-\<,_
(*)/ (*)
</div>
  </td>
  </tr>
  </table>
  <br>
  <form method="get" id="input">
    <table border="1" id="inputtable">
      <tr>
        <th><label for="country1">Country 1</label></th>
        <th><label for="country2">Country 2</label></th>
        <th><label for="country3">Country 3</label></th>
        <th><label for="country4">Country 4</label></th>
      </tr>
      <tr>
        <td><input name="country1" type="text" class="larger" id="country1" size="5"></td>
        <td><input name="country2" type="text" class="larger" id="country2" size="5"></td>
        <td><input name="country3" type="text" class="larger" id="country3" size="5"></td>
        <td><input name="country4" type="text" class="larger" id="country4" size="5"></td>
      </tr>
    </table>
  </form>
  <br>
  <div id="comptable"></div>
  <br>
  <div id="cyctable"></div>
</body>
</html>
