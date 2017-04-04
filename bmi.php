<?php
$min_weight = $_REQUEST['min_weight'];
$max_weight = $_REQUEST['max_weight'];
$min_height = $_REQUEST['min_height'];
$max_height = $_REQUEST['max_height'];

echo "<table border=\"1\">";

for ($h = $min_height - 5; $h <= $max_height; $h += 5)
{
	echo "<tr>";
	
	for ($w = $min_weight - 5; $w <= $max_weight; $w += 5)
	{
		echo "<td>";
		
		if ($h < $min_height && $w < $min_weight)
		{
			echo "&emsp;&emsp;\\ weight<br>height \\";
		}
		elseif ($h < $min_height)
		{
			echo $w;
		}
		elseif ($w < $min_weight)
		{
			echo $h;
		}
		else
		{
			if ($h > 0)
			{
				$hcm = $h / 100;
				
				echo round($w/($hcm*$hcm), 3);
			}
			else
			{
				echo "ERR";
			}
		}
		
		echo "</td>";
	}
	
	echo "</tr>";
}

echo "</table>";
?>