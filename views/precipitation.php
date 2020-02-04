<style>


 td, s th {
  border: 1px solid #ddd;
  padding: 8px;
}


 tr:nth-child(even){background-color: #f2f2f2;}

 tr:hover {background-color: #ddd;}

 th {
  padding-top: 12px;
  padding-bottom: 12px;
  padding-right: 50px;
  text-align: left;
  background-color: #4CAF50;
  color: white;
}
</style>

<table>
	<tr><th>Station</th><th>Rainfall</th></tr>
<?php 
foreach ($topTen ?? [] as $a) {
	echo "<tr><td>";
	echo ucfirst(strtolower($a->station->name));
	echo "</td><td>";
	echo $a->rainfall;
	echo "</td></tr>"; 	
	}
?>
</table>