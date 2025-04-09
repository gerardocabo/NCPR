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
        <td>
            <?php
            if ($row['urgent'] === 'on') {
                echo '<i class="fas fa-exclamation-circle text-danger" title="Urgent"></i>';
            } else {
                echo '<i class="fas fa-minus-circle text-muted" title="Not Urgent"></i>';
            }
            ?>
        </td>

        <td>
            <?php
            $status = $row['status'];
            $badgeClass = '';

            if ($status === 'Open') {
                $badgeClass = 'badge bg-success';
            } elseif ($status === 'Close') {
                $badgeClass = 'badge bg-danger';
            } else {
                $badgeClass = 'badge bg-danger'; // default/unknown status
            }

            echo "<span class='$badgeClass'>$status</span>";
            ?>
        </td>

        <td class="text-center">
            <div class="d-flex flex-wrap gap-1 justify-content-center">
                <button class="btn btn-primary btn-sm view-btn fw-bold" data-id="<?php echo $row['ncpr_num']; ?>" data-bs-toggle="modal" data-bs-target="#viewModal">
                    NCPR Form
                </button>
                <button class="btn btn-primary btn-sm dispo-btn fw-bold" data-id="<?php echo $row['ncpr_num']; ?>" data-bs-toggle="modal" data-bs-target="#dispoModal">
                    Disposition Form
                </button>
                <button class="btn btn-warning btn-sm edit-btn text-light fw-bold" data-id="<?php echo $row['ncpr_num']; ?>" data-bs-toggle="modal" data-bs-target="#editModal">
                    Edit
                </button>
                <button class="btn btn-secondary btn-sm print-btn fw-bold" data-id="<?php echo $row['ncpr_num']; ?>">
                    Print
                </button>
            </div>
        </td>
    </tr>
<?php endwhile; ?>