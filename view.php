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

function isValidISOFormat(value)
{
	var exp = new RegExp("^[A-Za-z]{3}$");
	return exp.test(value);
}

function updateColumnVisibility()
{
	var twoValid = (isValidISOFormat($("#country1").val()) && isValidISOFormat($("#country2").val()) || isValidISOFormat($("#country3").val()));
	var threeValid = (twoValid && isValidISOFormat($("#country3").val())) || isValidISOFormat($("#country4").val());
	
	$("#headerrow > th:nth-child(3)").css("display", (twoValid ? "" : "none"));
	$("#isorow > td:nth-child(3)").css("display", (twoValid ? "" : "none"));
	$("#headerrow > th:nth-child(4)").css("display", (threeValid ? "" : "none"));
	$("#isorow > td:nth-child(4)").css("display", (threeValid ? "" : "none"));
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
	
	if (Object.keys(data).length >= 2)
	{
		$.get("table.php", data, function(html) {			
			if (html.length > 0)
			{
				$("#results").html(html);
			}
		}, "html");
	}
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
  <form method="get" id="comp">
    <table border="1">
      <tr id="headerrow">
        <th><label for="country1">Country 1</label></th>
        <th><label for="country2">Country 2</label></th>
        <th><label for="country3">Country 3</label></th>
        <th><label for="country4">Country 4</label></th>
      </tr>
      <tr id="isorow">
        <td><input name="country1" type="text" class="larger" id="country1" size="5" /></td>
        <td><input name="country2" type="text" class="larger" id="country2" size="5" /></td>
        <td><input name="country3" type="text" class="larger" id="country3" size="5" /></td>
        <td><input name="country4" type="text" class="larger" id="country4" size="5" /></td>
      </tr>
    </table>
  </form>
  <br>
  <div id="results"></div>
</body>
</html>
