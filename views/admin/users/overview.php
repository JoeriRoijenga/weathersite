<table>
	<tr>
		<th>ID</th>
		<th>Name</th>
		<th>Privilege</th>
	</tr>
	<?php
	while($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>" . $row['user_id'] . "</td><td>" . $row['username'] . "</td><td> " . $row['priv_level'] . "</td><td><a href='/admin/user/" . $row['user_id'] ."/edit'>Edit</a></td><td><a href='/admin/user/" . $row['user_id'] ."/delete'>Delete</a></td></tr>";
    }
    ?>
</table>