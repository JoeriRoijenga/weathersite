<div class="row">
  <div class="side">
  	<h2>Top 10 rainfall:</h2>
      <table class="table table-striped">
      	<thead class="thead-dark">
		<tr>
			<th scope="col">Station</th>
			<th scope="col">Rainfall</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($topTen ?? [] as $a) {
				echo "<tr><td>";
				echo ucfirst(strtolower($a->station->name));
				echo "</td><td>";
				echo $a->rainfall;
				echo "</td></tr>"; 	
			}
		?>
	</tbody>
		</table>
  </div>
  <div class="main">
      <h2>Content</h2>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque in porta est. In ac rutrum mauris, vel tincidunt orci. Sed gravida erat pharetra nulla posuere suscipit. Phasellus non egestas justo, vitae ultricies purus. Quisque luctus viverra augue quis placerat. In posuere quis risus quis ultrices. Quisque nec enim sollicitudin, molestie arcu id, bibendum lacus. Maecenas varius diam at eros posuere, eget semper mi commodo. Nulla semper libero in faucibus convallis. Curabitur hendrerit magna sapien, nec tempor turpis aliquet eget. Aenean ac malesuada odio, eu dapibus libero. Nulla porta purus et elit scelerisque, sit amet sodales eros tincidunt. </p>
  </div>
</div>