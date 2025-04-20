<?php
include 'conn.php'; // Ensure this connects to your database

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ncpr_num'])) {
    $ncpr_num = $_GET['ncpr_num'];

    // Fetch NCPR and FOMO data in one query
    $query = "
        SELECT 
            ncpr.*, 
            fomo.supplier, fomo.supplier_part_name, fomo.supplier_part_number, fomo.invoice_num, fomo.purchase_order
        FROM ncpr_table AS ncpr
        LEFT JOIN fomo ON ncpr.id = fomo.ncpr_id
        WHERE ncpr.ncpr_num = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $ncpr_num);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($ncpr = $result->fetch_assoc()) {
        // ✅ Check if `ncpr_id` exists in `fomo`
        $fomo_exists = !empty($ncpr['fomo_ncpr_id']); // If exists, it's true
        // ✅ Checkbox checked if fomo exists
        $fomo_checked = $fomo_exists ? 'checked' : '';
        // ✅ Updated: Fetch related materials with specific columns
        $stmt_material = $conn->prepare("
        SELECT material_id, ntdj_num, mns_num, lot_sublot_qty, qty_affected, qty_affected_text, defect_rate, ncpr_id 
        FROM material 
        WHERE ncpr_id = ?
    ");
        $stmt_material->bind_param("i", $ncpr['id']);
        $stmt_material->execute();
        $materials = $stmt_material->get_result()->fetch_all(MYSQLI_ASSOC);

        // Fetch related files
        $stmt_files = $conn->prepare("SELECT * FROM uploaded_file WHERE ncpr_id = ?");
        $stmt_files->bind_param("i", $ncpr['id']);
        $stmt_files->execute();
        $files = $stmt_files->get_result()->fetch_all(MYSQLI_ASSOC);

        // ✅ Fix: Remove second $result->fetch_assoc()
        $urgent = $ncpr['urgent'] ?? 'off'; // Default to 'off' if NULL
        $checked = ($urgent === "on" || $urgent === "1") ? 'checked' : ''; // Set checkbox checked if urgent

        $checkbox_values = [
            'deviation' => $ncpr['deviation'],
            'repeating' => $ncpr['repeating'],
            'one' => $ncpr['one'],
            'one_one' => $ncpr['one_one'],
            'two' => $ncpr['one_one'],
            'three' => $ncpr['one_one'],
            'four' => $ncpr['one_one'],
            'five' => $ncpr['one_one'],
            'six' => $ncpr['one_one'],
            'seven' => $ncpr['one_one'],
            'eight' => $ncpr['one_one'],
            'nine' => $ncpr['one_one'],
            'recall' => $ncpr['recall'],
            'shipment' => $ncpr['shipment'],
            'wip' => $ncpr['wip'],
            'fgpart' => $ncpr['fgparts'],
            'stop_proc' => $ncpr['stop_proc'],
            'mcs' => $ncpr['mcs'],
            'cust_notif' => $ncpr['customer_notif'] // Value from deviation_type
        ];
?>
        <style>
            .locked_checkbox {
                pointer-events: none;
                /* Prevent clicking */
            }
        </style>
        <div class="row d-flex">
            <div class="border mb-3 align-items-center p-2">
                <h5 class="text-center">NON-CONFORMING PRODUCT RECORD</h5>
            </div>
            <!-- Left Side Content (Takes up 9 columns) -->
            <div class="col-md-9 m-0">
                <div class="row">
                    <div class="col-md-4 border p-2">
                        <span class="d-block" style="font-size: 12px"><strong>Initiator:</strong></span>
                        <span id="view-initiator" style="font-size: 12px"><?= htmlspecialchars($ncpr['initiator'] ?? 'N/A') ?></span>
                    </div>
                    <div class="col-md-4 border p-2">
                        <span class="d-block" style="font-size: 12px"><strong>NCPR Number:</strong></span>
                        <span id="view-ncpr-num" style="font-size: 14px"><?= htmlspecialchars($ncpr['ncpr_num'] ?? 'N/A') ?></span>
                    </div>
                    <div class="col-md-4 border p-2">
                        <span class="d-block" style="font-size: 12px"><strong>Date:</strong></span>
                        <span id="view-date" style="font-size: 12px"><?= htmlspecialchars($ncpr['date'] ?? 'N/A') ?></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 border p-2">
                        <span class="d-block" style="font-size: 12px"><strong>Part Number:</strong></span>
                        <span id="view-part-number" style="font-size: 14px"><?= htmlspecialchars($ncpr['part_number'] ?? 'N/A') ?></span>
                    </div>
                    <div class="col-md-4 border p-2">
                        <span class="d-block" style="font-size: 12px"><strong>Part Name:</strong></span>
                        <span id="view-part-name" style="font-size: 14px"><?= htmlspecialchars($ncpr['part_name'] ?? 'N/A') ?></span>
                    </div>
                    <div class="col-md-4 border p-2">
                        <span class="d-block" style="font-size: 12px"><strong>Process:</strong></span>
                        <span id="view-process" style="font-size: 14px"><?= htmlspecialchars($ncpr['process'] ?? 'N/A') ?></span>
                    </div>
                </div>
                <div class="row supplier-details">
                    <div class="col-md-4 border p-2">
                        <span class="d-block" style="font-size: 15px"><strong>FOR ON HOLD MATERIAL ONLY <input type="checkbox" class="locked_checkbox form-check-input form-check-input-sm" id="fomo-checkbox" <?= $fomo_checked; ?>></strong></span>

                    </div>
                    <div class="col-md-4 border p-2">
                        <span class="d-block" style="font-size: 12px"><strong>Supplier Part Name:</strong></span>
                        <span id="view-supplier-part-name" style="font-size: 14px"><?= htmlspecialchars($ncpr['supplier_part_name'] ?? 'N/A') ?></span>
                    </div>
                    <div class="col-md-4 border p-2">
                        <span class="d-block" style="font-size: 12px"><strong>Supplier Part Number:</strong></span>
                        <span id="view-supplier-part-number" style="font-size: 14px"><?= htmlspecialchars($ncpr['supplier_part_number'] ?? 'N/A') ?></span>
                    </div>
                    <div class="col-md-4 border p-2">
                        <span class="d-block" style="font-size: 12px"><strong>Supplier:</strong></span>
                        <span id="view-supplier" style="font-size: 14px"><?= htmlspecialchars($ncpr['supplier'] ?? 'N/A') ?></span>
                    </div>
                    <div class="col-md-4 border p-2">
                        <span class="d-block" style="font-size: 12px"><strong>Invoice Number:</strong></span>
                        <span id="view-invoice-num" style="font-size: 14px"><?= htmlspecialchars($ncpr['invoice_num'] ?? 'N/A') ?></span>
                    </div>
                    <div class="col-md-4 border p-2">
                        <span class="d-block" style="font-size: 12px"><strong>Purchase Order:</strong></span>
                        <span id="view-purchase-order" style="font-size: 14px"><?= htmlspecialchars($ncpr['purchase_order'] ?? 'N/A') ?></span>
                    </div>
                </div>
            </div>

            <!-- Right Box (Aligned to the right) -->
            <div class="col-md-3 d-flex align-items-stretch g-0">
                <div class="right-box p-4 border w-100 text-center">
                    <h3 style="color: red; font-weight: bold;">URGENT!</h3>
                    <span>Check the checkbox if the held parts is a potential OTD Miss Shipment.</span>
                    <!-- Large Checkbox -->
                    <div class="mt-2">
                        <input type="checkbox" id="view-urgent-checkbox" name="urgent" class="locked_checkbox form-check-input" style="transform: scale(1.8);" <?= $checked; ?>>
                        <label for="view-urgent-checkbox" class="ms-2 fw-bold">Mark as Urgent</label>
                    </div>
                </div>
            </div>
            <div class="row table-responsive m-0 g-0 p-0">
                <table class="table table-bordered text-center" id="material-table">
                    <thead>
                        <tr>
                            <th style="font-size: 15px">NT DJ Number</th>
                            <th style="font-size: 15px">Material / NFLD Lob / Sublot Number</th>
                            <th style="font-size: 15px">Lot / Sublot Quantity</th>
                            <th style="font-size: 15px">Quantity Affected</th>
                            <th style="font-size: 15px">Defect Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($materials)): ?>
                            <?php foreach ($materials as $material): ?>
                                <tr>
                                    <td><?= htmlspecialchars($material['ntdj_num'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($material['mns_num'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($material['lot_sublot_qty'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($material['qty_affected'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($material['defect_rate'] ?? 'N/A') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No materials found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <span style="font-size: 12px" class="fw-bold">Problem Description (specefic column/s where appropriate)</span>
        <div class="row">
            <div class="col-md-3 border p-2">
                <span class="d-block" style="font-size: 12px"><strong>Issue call out</strong></span>
                <span id="view-issue" style="font-size: 15px"><?= htmlspecialchars($ncpr['issue'] ?? 'N/A') ?></span>
            </div>
            <div class="col-md-3 border p-2">
                <div class="d-flex">
                    <p style="font-size: 10px;" class="me-5">
                        <strong>AWPI:</strong>
                        <span id="view-awpi" class="border-bottom border-dark d-inline-block text-center" style="min-width: 50px;"><?= htmlspecialchars($ncpr['awpi'] ?? 'N/A') ?></span>
                    </p>
                    <p style="font-size: 10px;">
                        <strong>DC:</strong>
                        <span id="view-dc" class="border-bottom border-dark d-inline-block text-center" style="min-width: 50px;"><?= htmlspecialchars($ncpr['dc'] ?? 'N/A') ?></span>
                    </p>
                </div>

                <div class="d-flex">
                    <p style="font-size: 12px;"><strong>Deviation?</strong></p>
                    <div class="form-check form-check-inline ms-5">
                        <input class="locked_checkbox form-check-input form-check-input-sm" type="checkbox" id="deviation-yes" 
                        <?php echo $checkbox_values['deviation'] == 'Yes' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="deviation-yes" style="font-size: 10px;">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="locked_checkbox form-check-input form-check-input-sm" type="checkbox" id="deviation-no"
                        <?php echo $checkbox_values['deviation'] == 'No' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="deviation-no" style="font-size: 10px;">No</label>
                    </div>
                </div>

                <div class="d-flex align-items-center">
                    <p class="m-0" style="font-size: 12px;"><strong>Issue Repeating?</strong></p>
                    <div class="form-check form-check-inline" style="margin-left: 12px;">
                        <input class="locked_checkbox form-check-input form-check-input-sm" type="checkbox" id="repeating-yes" 
                        <?php echo $checkbox_values['repeating'] == 'Yes' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="repeating-yes" style="font-size: 10px;">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="locked_checkbox form-check-input form-check-input-sm" type="checkbox" id="repeating-no" 
                        <?php echo $checkbox_values['repeating'] == 'No' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="repeating-no" style="font-size: 10px;">No</label>
                    </div>
                </div>
                <div style="display: d-block; align-items: center;">
                    <span class="d-inline-block" style="font-size: 12px;"><strong>Cavity Affected:</strong></span>
                    <span id="view-cavity" class="border-bottom border-dark d-inline-block text-center" style="min-width: 100px; font-size: 12px">
                        <?= htmlspecialchars($ncpr['cavity'] ?? 'N/A') ?>
                    </span>
                </div>

                <div style="display: d-block; align-items: center;">
                    <span class="d-inline-block" style="font-size: 12px;"><strong>Machine:</strong></span>
                    <span id="view-machine" class="border-bottom border-dark d-inline-block text-center" style="min-width: 100px; font-size: 12px">
                        <?= htmlspecialchars($ncpr['machine'] ?? 'N/A') ?>
                </span>
                </div>
            </div>
            <div class="col-md-3 border p-2">
                <span class="d-block" style="font-size: 12px"><strong>Criteria / Doc Reference</strong></span><span id="view-ref" style="font-size: 12px"><?= htmlspecialchars($ncpr['ref'] ?? 'N/A') ?></span>
            </div>
            <div class="col-md-3 border p-2">
                <span class="d-block" style="font-size: 12px"><strong>Issue background or information relevant in determining this root cause of the problem</strong></span><span id="view-bg" style="font-size: 12px"><?= htmlspecialchars($ncpr['bg'] ?? 'N/A') ?></span>
            </div>
        </div>
        <div class="row mt-3 border m-0">
            <div class="col-md-6 border p-2">
                <span style="font-size: 12px" class="fw-bold">Immediate containment action/s or countermeasure/s taken (tick as many as appropriate):</span>
                <div style="display: block; margin-bottom: 5px;">
                    <div style="display: inline-flex; align-items: center;">
                        <input type="checkbox" class="locked_checkbox form-check-input" id="view-one" style="width: 12px; height: 12px; margin-right: 5px;"
                        <?php echo $checkbox_values['one'] == 'yes' ? 'checked' : ''; ?>>
                        <span style="font-size: 12px;">1. Segregate affected part/s - write custodian of the segregated parts</span>
                    </div>
                </div>
                <div style="display: block;">
                    <div style="display: inline-flex; align-items: center;">
                        <input type="checkbox" class="locked_checkbox form-check-input" id="view-one-one" style="width: 12px; height: 12px; margin-right: 5px;"
                        <?php echo $checkbox_values['one_one'] == 'yes' ? 'checked' : ''; ?>>
                        <span style="font-size: 12px;"><strong>1.1. At Hotpress:</strong>Put on hold inventory of affected lay-up materials together with the parts</span>
                    </div>
                </div>
                <div style="display: block;">
                    <div style="display: inline-flex; align-items: center;">
                        <input type="checkbox" class="locked_checkbox form-check-input" id="view-two" style="width: 12px; height: 12px; margin-right: 5px;"
                        <?php echo $checkbox_values['two'] == 'yes' ? 'checked' : ''; ?>>

                        <span style="font-size: 12px;" class="me-2">2. Yield off/ 100% inspection. <strong>INSPECTION RESULTS:</strong></span>
                        <span id="view-two-one" style="font-size: 12px; display: inline-block; border-bottom: 1px solid black; min-width: 100px;"><?= htmlspecialchars($ncpr['two_one'] ?? 'N/A') ?></span>
                    </div>
                </div>
                <div style="display: block;">
                    <div style="display: inline-flex; align-items: center;">
                        <input type="checkbox" class="locked_checkbox form-check-input" id="view-three" style="width: 12px; height: 12px; margin-right: 5px;"
                        <?php echo $checkbox_values['three'] == 'yes' ? 'checked' : ''; ?>>
                        <span style="font-size: 12px;" class="me-2">3. Call the attention of QAE/PE/EE/TECH/CHIEF:</span>
                        <span id="view-three-one" style="font-size: 12px; display: inline-block; border-bottom: 1px solid black; min-width: 200px;"><?= htmlspecialchars($ncpr['three_one'] ?? 'N/A') ?></span>
                    </div>
                </div>
                <div style="display: block;">
                    <div style="display: inline-flex; align-items: center;">
                        <input type="checkbox" class="locked_checkbox form-check-input" id="view-four" style="width: 12px; height: 12px; margin-right: 5px;"
                        <?php echo $checkbox_values['four'] == 'yes' ? 'checked' : ''; ?>>
                        <span id="view-four" style="font-size: 12px;" class="me-2">4. Attach On-hold Tag and put in On-Hold cage/area</span>
                    </div>
                </div>
                <div style="display: block;">
                    <div style="display: inline-flex; align-items: center;">
                        <input type="checkbox" class="locked_checkbox form-check-input" id="view-five" style="width: 12px; height: 12px; margin-right: 5px;"
                        <?php echo $checkbox_values['five'] == 'yes' ? 'checked' : ''; ?>>
                        <span id="view-five" style="font-size: 12px;">5. Check MCS stock for similar Lot Number/AWPI/DC and request to file NCPR</span>
                    </div>
                </div>
                <div style="display: block;">
                    <div style="display: inline-flex; align-items: center;">
                        <input type="checkbox" class="locked_checkbox form-check-input" id="view-six" style="width: 12px; height: 12px; margin-right: 5px;"
                        <?php echo $checkbox_values['six'] == 'yes' ? 'checked' : ''; ?>>
                        <span id="view-six" style="font-size: 12px;">6. Attach copy of OCAP if available, and/or other log forms as part of the containment action</span>
                    </div>
                </div>
                <div style="display: inline-flex; align-items: center; gap: 10px;">
                    <span style="font-size: 12px;">7. File Shutdown Record</span>

                    <label class="locked_checkbox" style="font-size: 12px; display: flex; align-items: center;">
                        <input type="checkbox" class="form-check-input" id="view-seven-yes" style="width: 12px; height: 12px; margin-right: 5px;"
                        <?php echo $checkbox_values['seven'] == 'yes' ? 'checked' : ''; ?>> Yes
                    </label>

                    <label class="locked_checkbox" style="font-size: 12px; display: flex; align-items: center;">
                        <input type="checkbox" class="form-check-input" id="view-seven-no" style="width: 12px; height: 12px; margin-right: 5px;"
                        <?php echo $checkbox_values['seven'] == 'no' ? 'checked' : ''; ?>> No
                    </label>
                    <span style="font-size: 12px;">WHO:</span>
                    <span id="view-seven-one" style="font-size: 12px; display: inline-block; border-bottom: 1px solid black; min-width: 80px;"><?= htmlspecialchars($ncpr['seven_one'] ?? 'N/A') ?></span>
                    <span style="font-size: 12px;">TIME/SHIFT:</span>
                    <span id="view-seven-two" style="font-size: 12px; display: inline-block; border-bottom: 1px solid black; min-width: 80px;"><?= htmlspecialchars($ncpr['seven_two'] ?? 'N/A') ?></span>
                </div>
                <div style="display: block;">
                    <div style="display: inline-flex; align-items: center;">
                        <input type="checkbox" class="locked_checkbox form-check-input" id="view-eight" style="width: 12px; height: 12px; margin-right: 5px;"
                        <?php echo $checkbox_values['eight'] == 'yes' ? 'checked' : ''; ?>>
                        <span style="font-size: 12px;" class="me-2">8. Others (please specify):</span>
                        <span id="view-eight-one" style="font-size: 12px; display: inline-block; border-bottom: 1px solid black; min-width: 300px;"><?= htmlspecialchars($ncpr['eight'] ?? 'N/A') ?></span>
                    </div>
                </div>
                <div style="display: block;">
                    <div style="display: inline-flex; align-items: center;">
                        <input type="checkbox" class="locked_checkbox form-check-input" id="view-nine" style="width: 12px; height: 12px; margin-right: 5px;"
                        <?php echo $checkbox_values['nine'] == 'yes' ? 'checked' : ''; ?>>
                        <span style="font-size: 12px;" class="me-2">9. Find affected WIP, FG & raw materials - specify DJ/s and LN/s</span>
                        <span id="view-nine-one" style="font-size: 12px; display: inline-block; border-bottom: 1px solid black; min-width: 150px;"><?= htmlspecialchars($ncpr['nine '] ?? 'N/A') ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 border p-2">
                <div style="display: inline-flex; align-items: center; gap: 100px;" class="mb-3">
                    <span style="font-size: 15px;">Product Recall</span>

                    <label class="locked_checkbox" style="font-size: 15px; display: flex; align-items: center;">
                        <input type="checkbox" class="form-check-input" id="view-recall-yes" style="margin-right: 5px;" 
                        <?php echo $checkbox_values['recall'] == 'yes' ? 'checked' : ''; ?>> Yes
                    </label>

                    <label class="locked_checkbox" style="font-size: 15px; display: flex; align-items: center;">
                        <input type="checkbox" class="form-check-input" id="view-recall-no" style="margin-right: 5px;"
                        <?php echo $checkbox_values['recall'] == 'no' ? 'checked' : ''; ?>> No
                    </label>
                </div>  
                <div style="display: inline-flex; align-items: center; gap: 50px;" class="mb-3">
                    <div style="display: block;">
                        <div style="display: inline-flex; align-items: center;">
                            <input type="checkbox" class="locked_checkbox form-check-input" id="view-fgparts" style="margin-right: 5px;"
                            <?php echo $checkbox_values['fgpart'] == 'yes' ? 'checked' : ''; ?>>
                            <span id="view-fgparts" style="font-size: 15px;">FG PARTS</span>
                        </div>
                    </div>

                    <span style="font-size: 15px;">Cancel Shipment</span>

                    <label class="locked_checkbox" style="font-size: 15px; display: flex; align-items: center;">
                        <input type="checkbox" class="form-check-input" id="view-shipment-yes" style="margin-right: 5px;"
                        <?php echo $checkbox_values['shipment'] == 'yes' ? 'checked' : ''; ?>> Yes
                    </label>

                    <label class="locked_checkbox" style="font-size: 15px; display: flex; align-items: center;">
                        <input type="checkbox" class="form-check-input" id="view-shipment-no" style="margin-right: 5px;"
                        <?php echo $checkbox_values['shipment'] == 'no' ? 'checked' : ''; ?>> No
                    </label>
                </div>

                <span class="d-inline-block" style="font-size: 15px;"><strong>Shipment SCHEDULE:</strong></span>
                <span id="view-ship_sched" class="border-bottom border-dark d-inline-block text-center" style="min-width: 500px; font-size: 15px"><?= htmlspecialchars($ncpr['ship_sched'] ?? 'N/A') ?></span>
                <div style="display: inline-flex; align-items: center; gap: 50px;" class="mt-3">
                    <div style="display: block;">
                        <div style="display: inline-flex; align-items: center;">
                            <input type="checkbox" class="locked_ceckbox form-check-input" id="view-wip" style="margin-right: 5px;"
                            <?php echo $checkbox_values['wip'] == 'yes' ? 'checked' : ''; ?>>
                            <span id="view-wip" style="font-size: 15px;">WIP</span>
                        </div>
                    </div>

                    <span style="font-size: 15px;">Stop Process</span>

                    <label class="locked_checkbox" style="font-size: 15px; display: flex; align-items: center;">
                        <input type="checkbox" class="form-check-input" id="view-stop_proc-yes" style="margin-right: 5px;"
                        <?php echo $checkbox_values['stop_proc'] == 'yes' ? 'checked' : ''; ?>> Yes
                    </label>

                    <label class="locked_checkbox" style="font-size: 15px; display: flex; align-items: center;">
                        <input type="checkbox" class="form-check-input" id="view-stop_proc-no" style="margin-right: 5px;"
                        <?php echo $checkbox_values['stop_proc'] == 'no' ? 'checked' : ''; ?>> No
                    </label>
                </div>
                <div style="display: block;">
                    <div style="display: d-block; align-items: center;">
                        <span class="d-inline-block" style="font-size: 15px;"><strong>LOCATIONS:</strong></span>
                        <span id="view-location" class="border-bottom border-dark d-inline-block text-center" style="min-width: 500px; font-size: 15px"><?= htmlspecialchars($ncpr['location'] ?? 'N/A') ?></span>
                    </div>
                </div>
                <div style="display: block;">
                    <div style="display: inline-flex; align-items: center;">
                        <input type="checkbox" class="locked_checkbox form-check-input" id="view-mcs" style="margin-right: 5px;"
                        <?php echo $checkbox_values['mcs'] == 'yes' ? 'checked' : ''; ?>>
                        <div style="display: d-block; align-items: center;">
                            <span id="view-mcs" style="font-size: 15px;">MCS</span>
                            <span id="view-mcs_details" class="border-bottom border-dark d-inline-block text-center" style="min-width: 300px; font-size: 15px"><?= htmlspecialchars($ncpr['mcs_details'] ?? 'N/A') ?></span>
                        </div>
                    </div>
                </div>

                <div style="display: block;">
                    <div style="display: inline-flex; align-items: center;">
                        <input type="checkbox" class="locked_checkbox form-check-input" id="view-customer_notif" style="margin-right: 5px;"
                        <?php echo $checkbox_values['cust_notif'] == 'yes' ? 'checked' : ''; ?>>
                        <span id="view-customer_notif" style="font-size: 15px;">Customer notification if non-conforming products have been shipped.</span>
                    </div>
                </div>
            </div>
        </div>

        <h5 class="text-center mb-5 mt-5">File Attachments</h5>
        <div id="file-list" class="d-block flex-wrap">
            <!-- Files will be dynamically inserted here -->
        </div>
        </div>

        <!-- Uploaded Files -->
        <ul>
            <?php if (!empty($files)): ?>
                <h6 class="mt-3">Uploaded Files</h6>
                <?php foreach ($files as $file): ?>
                    <?php
                    $fileType = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));
                    if (in_array($fileType, ['jpg', 'png', 'jpeg', 'gif'])):
                    ?>
                        <li>
                            <img src="<?= htmlspecialchars($file['file_path']) ?>"
                                class="img-thumbnail"
                                style="max-width: 150px; margin: 5px; margin-bottom: 10px;"
                                alt="<?= htmlspecialchars($file['file_name']) ?>">
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="<?= htmlspecialchars($file['file_path']) ?>"
                                download="<?= htmlspecialchars($file['file_name']) ?>"
                                class="btn btn-primary btn-sm"
                                style="margin-bottom: 10px;">
                                <i class="fa fa-download"></i> Download <?= htmlspecialchars($file['file_name']) ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No files uploaded</li>
            <?php endif; ?>
        </ul>
        </div>
        </div>

<?php
    } else {
        echo "<p class='text-danger'>No record found.</p>";
    }

    // Close statements
    $stmt->close();
    $stmt_material->close();
    $stmt_files->close();
}

// Close database connection
$conn->close();
?>