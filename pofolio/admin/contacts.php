<?php
include "../config/db.php";
$result = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC");
?>

<h2>📨 Contact Messages</h2>

<table border="1" cellpadding="10">
<tr>
  <th>ID</th>
  <th>Name</th>
  <th>Email</th>
  <th>Status</th>
  <th>Time</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
  <td><?= $row['id'] ?></td>
  <td><?= htmlspecialchars($row['name']) ?></td>
  <td><?= htmlspecialchars($row['email']) ?></td>
  <td><?= $row['status'] ?></td>
  <td><?= $row['created_at'] ?></td>
</tr>
<?php endwhile; ?>
</table>
    