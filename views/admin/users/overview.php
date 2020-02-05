<div class="container-fluid w-50 mt-4">
	<div class="jumbotron">

<h2>User list:</h2>
      <table class="table">
      	<thead class="thead-dark">
		<tr>
			<th scope="col">ID</th>
			<th scope="col">Name</th>
			<th scope="col">Privilege</th>
			<th scope="col">Edit</th>
			<th scope="col">Delete</th>
		</tr>
	</thead>
	<tbody>
	<?php
	while($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>" . $row['user_id'] . "</td><td>" . $row['username'] . "</td><td> " . $row['priv_level'] . "</td><td><a class='btn btn-primary' href='/admin/user/" . $row['user_id'] ."/edit'>Edit</a></td><td><a class = 'btn btn-danger' href='/admin/user/" . $row['user_id'] ."/delete'>Delete</a></td></tr>";
    }
    ?>
	</tbody>
</table>

</div>
</div>