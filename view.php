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
	
	return null;
}

function updateResults()
{
	var exp = new RegExp("^[A-Za-z]{3}$");
	var c1 = $("#country1").val();
	var c2 = $("#country2").val();
	
	if (exp.test(c1) && exp.test(c2))
	{		
		$.get("table.php", { country1:c1, country2:c2 }, function(html) {			
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
	
	$(window).on("beforeunload", function() {
		document.cookie = "country1=" + $("#country1").val();
		document.cookie = "country2=" + $("#country2").val();
	});
	
	c1 = getCookie("country1");
	
	if (c1 != null)
	{
		console.log("Cookie found for country 1: " + c1);
		$("#country1").val(c1);
	}
	
	c2 = getCookie("country2");
	
	if (c1 != null)
	{
		console.log("Cookie found for country 2: " + c2);
		$("#country2").val(c2);
		$("#country2").trigger("input");
	}
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
      <tr>
        <th><label for="country1">Country 1</label></th>
        <th><label for="country2">Country 2</label></th>
      </tr>
      <tr>
        <td><input name="country1" type="text" class="larger" id="country1" size="5" /></td>
        <td><input name="country2" type="text" class="larger" id="country2" size="5" /></td>
      </tr>
    </table>
  </form>
  <br>
  <div id="results"></div>
</body>
</html>
