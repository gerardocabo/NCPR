<?php
include 'conn.php'; // Make sure you have a proper database connection here
// Fetch data from ncpr_table
$query = "SELECT id, initiator, ncpr_num, date, part_number, part_name, status, urgent, dispo_id FROM ncpr_table";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td hidden><?php echo $row['id']; ?></td>
        <td hidden><?php echo $row['dispo_id']; ?></td>
        <td><?php echo $row['ncpr_num']; ?></td>
        <td><?php echo $row['initiator']; ?></td>
        <td><?php echo $row['date']; ?></td>
        <td><?php echo $row['part_number']; ?></td>
        <td><?php echo $row['part_name']; ?></td>
        <td><?php echo $row['urgent'] ? 'Yes' : 'No'; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td>
            <button class="btn btn-info btn-sm view-btn" data-id="<?php echo $row['ncpr_num']; ?>" data-bs-toggle="modal" data-bs-target="#viewModal">
                <i class="fas fa-eye"></i> NCPR
            </button>
            <button class="btn btn-warning btn-sm edit-btn" data-id="<?php echo $row['ncpr_num']; ?>" data-bs-toggle="modal" data-bs-target="#editModal">
                <i class="fas fa-eye"></i> EDIT
            </button>
            <button class="btn btn-info btn-sm dispo-btn" data-id="<?php echo $row['ncpr_num']; ?>" data-bs-toggle="modal" data-bs-target="#dispoModal">
                <i class="fas fa-eye"></i> DISPO
            </button>
        </td>
    </tr>
<?php endwhile; ?>