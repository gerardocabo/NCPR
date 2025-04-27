<form id="editDispoForm" method="POST">
    <p><strong class="me-2">NCPR_NUMBER: </strong><span id="modal-id"></span></p>
    <div class="form-floating mb-3">
        <textarea class="form-control lock" name="containment" id="containment" placeholder="Enter details here..." style="height: 100px;"></textarea>
        <label for="containment">This space is intended for QA verification, containment and investigation activities</label>
    </div>

    <!-- Cause of Non-Conformance Table -->
    <table class="table table-bordered align-middle">
        <tr>
            <td>
                <strong>Cause of Non-conformance:</strong><br>
            </td>
            <td>
                <strong>Potential Field Failure:</strong><br>
            </td>
            <!-- Corrective Action Request -->
            <td class="d-flex align-items-center gap-3 mt-3">
                <strong class="me-2">Corrective Action Request:</strong>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input locked" type="radio" name="corrective_action" value="YES" id="corrective_yes">
                        <label class="form-check-label" for="corrective_yes">YES</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input locked" type="radio" name="corrective_action" value="NO" id="corrective_no">
                        <label class="form-check-label" for="corrective_no">NO</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input locked" type="radio" name="corrective_action" value="N/A" id="corrective_na">
                        <label class="form-check-label" for="corrective_na">N/A</label>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <!-- Cause of Non-Conformance -->
            <td>
                <div class="d-flex">
                    <!-- Left Side -->
                    <div class="me-4">
                        <input class="locked" type="checkbox" name="cause[]" value="Man"> Man<br>
                        <label>ID No:</label>
                        <input type="text" name="id_no" id="id_no" class="locked border-0 border-bottom w-75"><br>
                        <label>Name:</label>
                        <input type="text" name="name" id="name" class="locked border-0 border-bottom w-75"><br>
                        <input class="locked" type="checkbox" name="cause[]" value="Method"> Method<br>
                        <input class="locked" type="checkbox" name="cause[]" value="Machine"> Machine<br>
                    </div>

                    <!-- Right Side -->
                    <div>
                        <input class="locked" type="checkbox" name="cause[]" value="Material"> Material<br>
                        <input class="locked" type="checkbox" name="cause[]" value="NID Item"> NFLD Item<br>
                        <input class="locked" type="checkbox" name="cause[]" value="NID Purchased Item"> NFLD Purchased Item<br>
                        <input class="locked" type="checkbox" name="cause[]" value="For Expiry Expired"> For Expiry Expired<br>
                        <input class="locked" type="checkbox" name="cause[]" value="Local Supplier"> Local Supplier<br>
                        <input class="locked" type="checkbox" name="cause[]" value="Customer Furnish Material">
                        <span style="color: blue; text-decoration: underline;">Customer Furnish Material</span>
                    </div>
                </div>
            </td>
            <td>
                <div class="form-check">
                    <input class="form-check-input locked" type="radio" name="potential_failure" value="YES" id="potential_yes">
                    <label class="form-check-label" for="potential_yes">YES</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input locked" type="radio" name="potential_failure" value="NO" id="potential_no">
                    <label class="form-check-label" for="potential_no">NO</label>
                </div>
            </td>
            <td>
                <div class="d-flex align-items-center mb-2">
                    <input class="locked" type="checkbox" name="car" value="CAR"> CAR, CAR No:
                    <input type="text" name="car_no" class="locked border-0 border-bottom w-20 ms-2">
                    <span class="me-2">8D Report:</span>
                    <input class="locked" type="radio" name="bd_report" value="YES" class="me-1"> YES
                    <input class="locked" type="radio" name="bd_report" value="NO" class="ms-3 me-1"> NO
                </div>
                <div class="d-flex align-items-center mt-5">
                    <input class="locked" type="checkbox" name="scar" value="SCAR"> SCAR, SCAR No:
                    <input type="text" name="scar_no" class="locked border-0 border-bottom w-50"><br>
                </div>
            </td>
        </tr>
    </table>
    <div>
        <span class="fw-bold" style="font-size: 15px">
            Disposition Required From:
        </span>
    </div>
    <!-- Disposition Required From Section -->
    <table class="table table-bordered align-middle">
        <tr>
            <td colspan="2">
                <input class="locked" type="checkbox" name="dispo_from[]" value="NTPI"> NTPI

                <strong>MRB:</strong>
                <input class="locked" type="radio" name="mrb" value="YES"> YES
                <input class="locked" type="radio" name="mrb" value="NO"> NO

            </td>
            <td>
                <!-- First Checkbox Section -->
                <div class="mb-2">
                    <input class="locked" type="checkbox" name="dispo_from[]" value="NFLD"> NFLD (Attach reference e-mail)
                </div>

                <!-- Second Section with Spacing and Inline Layout -->
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <strong>Need Customer Approval?</strong>
                    <input class="locked" type="radio" name="customer_approval" value="YES"> YES
                    <input class="locked" type="radio" name="customer_approval" value="NO"> NO
                    <span>Document Alert No:</span>
                    <input type="text" name="document_alert" class="locked border-0 border-bottom w-25">
                </div>

            </td>
        </tr>

        <table class="table border">
            <tr>
                <!-- Left Side: Impact Analysis / Risk Assessment -->
                <td colspan="2">
                    <strong>IMPACT ANALYSIS / RISK ASSESSMENT:</strong>
                    <div class="d-flex gap-3 align-items-center flex-wrap mt-2">
                        <input class="locked" type="checkbox" name="impact_analysis[]" value="Review of NCP FMEA"> Review of NCP FMEA
                        <input class="locked" type="checkbox" name="impact_analysis[]" value="Review of NCP Control Plan"> Review of NCP Control Plan
                    </div>
                    <div class="form-floating w-100 mt-2">
                        <textarea id="notes" name="notes" class="form-control" placeholder="Enter impact analysis here..." rows="2"></textarea>
                        <label for="notes">Notes:</label>
                    </div>
                </td>

                <!-- Right Section (Spanning Rows) -->
                <td style="width: 40%; vertical-align: top;" rowspan="2">
                    <div class="h-100 d-flex flex-column justify-content-between">
                        <div class="mb-2">
                            <input class="locked" type="checkbox" name="affected_business" value="Affected business"> Affected business unit/ contact person <br>
                            <input type="text" name="contact_person" class="locked border-0 border-bottom w-100">
                        </div>
                        <div>
                            <input class="locked" type="checkbox" name="other_instructions" value="Other instructions"> Other instructions,
                            <span>pls specify;</span><br>
                            <input type="text" name="other_specify" class="locked border-0 border-bottom w-100">
                        </div>
                    </div>
                </td>
            </tr>

            <!-- Product Disposition (Left Side) -->
            <tr>
                <td colspan="2">
                    <strong>PRODUCT DISPOSITION:</strong>
                    <div class="d-flex gap-3 align-items-center flex-wrap mt-2">
                        <input class="locked" type="checkbox" name="product_dispo[]" value="Use as is"> Use as is
                        <input class="locked" type="checkbox" name="product_dispo[]" value="Re-inspection"> Re-inspection
                        <input class="locked" type="checkbox" name="product_dispo[]" value="Run under normal process"> Run under normal process
                    </div>
                    <div class="d-flex align-items-center mt-2">
                        Yield-off $ <input type="text" name="yield_off" class="locked border-0 border-bottom w-25 ms-2">
                    </div>
                    <div class="d-flex align-items-center mt-2 gap-2">
                        <input class="locked" type="checkbox" name="product_dispo[]" value="Re-grade"> Re-grade, DA No:
                        <input type="text" name="da_no" class="locked border-0 border-bottom w-25 ms-2">
                    </div>
                    <div class="d-flex align-items-center mt-2 gap-2">
                        <input class="locked" type="checkbox" name="product_dispo[]" value="Rework"> Rework, DA No:
                        <input type="text" name="rework_da_no" class="locked border-0 border-bottom w-25 ms-2">
                        WIS No:
                        <input type="text" name="wis_no" class="locked border-0 border-bottom w-25 ms-2">
                    </div>
                    <div class="d-flex gap-3 align-items-center flex-wrap mt-2 gap-2">
                        <input class="locked" type="checkbox" name="product_dispo[]" value="Re-press"> Re-press
                        <input class="locked" type="checkbox" name="product_dispo[]" value="Re-plate"> Re-plate
                        <input class="locked" type="checkbox" name="product_dispo[]" value="Re-Etest"> <span>Re-Etest</span>
                        <input class="locked" type="checkbox" name="product_dispo[]" value="Re-measure"> Re-measure
                        <input class="locked" type="checkbox" name="product_dispo[]" value="Rework Traveler"> Rework Traveler
                    </div>
                    <div class="d-flex align-items-center mt-2 gap-2">
                        <input class="locked" type="checkbox" name="product_dispo[]" value="Repair"> Repair, Document Alert #:
                        <input type="text" name="repair_DA" class="locked border-0 border-bottom w-25 ms-2">
                        <input class="locked" type="checkbox" name="product_dispo[]" value="Rework Traveler"> Rework Traveler
                    </div>
                    <div class="d-flex align-items-center mt-2 gap-2">
                        <input class="locked" type="checkbox" name="product_dispo[]" value="Scrap"> Scrap $
                        <input type="text" name="scrap_amount" class="locked border-0 border-bottom w-25 ms-2">
                    </div>
                    <div class="d-flex align-items-center mt-2">
                        <input class="locked" type="checkbox" name="product_dispo[]" value="RTV"> RTV <span style="color: blue; text-decoration: underline; margin-left: 20px;">Shipment Date:</span>
                        <input type="text" name="shipment_date" class="locked border-0 border-bottom w-25 ms-2">
                    </div>
                </td>
            </tr>
        </table>
        <div>
            <span class="fw-bold" style="font-size: 15px">
                For non-conforming product disposition requiring PE/ EE intervention; Otherwise, not applicable.
            </span>
        </div>

        <table class="table table-bordered">

            <!-- Actions Taken & Reason for Resumption -->
            <tr>
                <td style="width: 33%; vertical-align: top;">
                    <strong>Actions Taken:</strong><br>
                    <input class="locked" type="checkbox" name="actions_taken[]" value="AT1"> Problem solving/troubleshooting<br>
                    <input class="locked" type="checkbox" name="actions_taken[]" value="AT2"> Process Verification /
                    <span>Eng'g Eval.</span>

                    <br><br>
                    <strong>Process Disposition:</strong><br>
                    <input class="locked" type="checkbox" name="process_dispo[]" value="PD1"> Resume Production<br>
                    <input class="locked" type="checkbox" name="process_dispo[]" value="PD2"> Stop Production
                    <br><br>
                    <span>Affected process/es:</span>
                    <input type="text" name="affected_process" class="locked border-0 border-bottom w-100"><br><br>
                    <input class="locked" type="checkbox" name="process_dispo[]" value="F1">
                    <span>For further Eng’g Evaluation</span>
                </td>

                <td style="width: 33%; vertical-align: top;">
                    <strong>Reason for Resumption:</strong><br>
                    <input class="locked" type="checkbox" name="resumption_reason[]" value="R1"> Criteria
                    <input class="locked" type="checkbox" name="resumption_reason[]" value="R2"> Method
                    <input class="locked" type="checkbox" name="resumption_reason[]" value="R3"> Materials
                    <input class="locked" type="checkbox" name="resumption_reason[]" value="R4"> Machine
                    <br><br>
                    <input class="locked" type="checkbox" name="resumption_reason[]" value="R5"> Machine/fixture repair<br>
                    <input class="locked" type="checkbox" name="resumption_reason[]" value="R6">
                    <span>Others, pls specify</span>
                    <br>
                    <input type="text" name="other_resumption" class="locked border-0 border-bottom w-100"><br><br>

                    <strong>Process Instruction in general:</strong>
                    <span>(for <span>DJs</span> other than the affected)</span>
                    <textarea class="form-control mt-2" name="process_instruction" rows="2"></textarea>
                </td>

                <td style="width: 33%; vertical-align: top;">
                    <strong>Instructions in detail, see reference doc:</strong><br>
                    <input class="locked" type="checkbox" name="instructions_detail[]" value="D_I1"> Document Alert #:
                    <input type="text" name="document_alert_s" class="locked border-0 border-bottom w-75"><br>

                    <input class="locked" type="checkbox" name="instructions_detail[]" value="D_I2">
                    <span>Other (pls specify)</span>
                    <input type="text" name="other_specify_s" class="locked border-0 border-bottom w-75">
                    <br><br>

                    <strong>Documents needing revision:</strong><br>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="documents_revision[]" value="DR1" id="doc_na">
                            <label class="form-check-label" for="doc_na" style="font-size: 12px;">N/A</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="documents_revision[]" value="DR2" id="doc_wis">
                            <label class="form-check-label" for="doc_wis" style="font-size: 12px;">WIS</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="documents_revision[]" value="DR3" id="doc_dj">
                            <label class="form-check-label" for="doc_dj" style="font-size: 12px;">DJ</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="documents_revision[]" value="DR4" id="doc_proc">
                            <label class="form-check-label" for="doc_proc" style="font-size: 12px;">PROC</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="documents_revision[]" value="DR5" id="doc_cp">
                            <label class="form-check-label" for="doc_cp" style="font-size: 12px;">CP</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="documents_revision[]" value="DR6" id="doc_pfmea">
                            <label class="form-check-label" for="doc_pfmea" style="font-size: 12px;">PFMEA</label>
                        </div>
                    </div>

                    <br><br>

                    <strong>Process Released By:</strong>
                    <input type="text" name="released_by" class="locked border-0 border-bottom w-100">
                    <br>
                    <div class="text-center mt-3">
                        <small>(Signature Above Printed Name/ Date)</small>
                    </div>
                </td>
            </tr>
        </table>
        <!-- Approving Authorities & Distribution Section -->
        <table class="table table-bordered mt-3">
            <tr>
                <th colspan="3" class="text-center">Approving Authorities</th>
            </tr>
            <tr>
                <td>
                    <strong>QA Engineer / NT Representative:</strong><br>
                    (Signature & Date)
                    <div class="signature-line">
                        <strong><span id="approvd_by_engineer"></span></strong>
                    </div>
                </td>
                <td>
                    <strong>QA Manager or his/her appointee:</strong><br>
                    (Signature & Date)
                    <div class="signature-line">
                        <strong><span id="approvd_by_supv_mgr"></span></strong>
                    </div>
                </td>
                <td>
                    <strong>Sheildahl / NT Representative:</strong><br>
                    (Signature & Date)
                    <div class="signature-line">
                        <strong><span id="approvd_by_SheldahlRep"></span></strong>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="form-floating">
                        <input type="text" name="acknowledgment_signature" class="form-control" id="acknowledgment_signature" placeholder="Signature">
                        <label for="acknowledgment_signature"><strong>Acknowledgment</strong></label>
                    </div>
                    <small class="text-muted">(Applies only with CAR & COPQ issues)</small>
                </td>

                <!-- PE or EE Head Section -->
                <td>
                    <div class="form-floating">
                        <input type="text" name="head_signature" class="form-control" id="pe_ee_head_signature" placeholder="PE or EE Head">
                        <label for="head_signature"><strong>PE or EE Head or his/her appointee:</strong></label>
                    </div>
                </td>

                <!-- Production Manager Section -->
                <td>
                    <div class="form-floating">
                        <input type="text" name="prod_manager_signature" class="form-control" id="prod_manager_signature" placeholder="Production Manager">
                        <label for="prod_manager_signature"><strong>Production Manager or his/her appointee:</strong></label>
                    </div>
                </td>
            </tr>
        </table>
        <table class="table table-bordered mt-3">
            <!-- File Attachment Section -->
            <tr>
                <td colspan="3">
                    <strong>Attach Supporting Documents:</strong><br>
                    <small class="text-muted d-block mt-1">You can upload multiple files (JPEG, PDF, Excel, Word, PowerPoint, etc.).</small>
                    <div id="fileUploadContainer">
                        <!-- File input that will allow the user to select files -->
                        <button type="button" class="btn btn-primary mb-2" onclick="document.getElementById('editfileInput').click()">Add File</button>
                        <input type="file" id="editfileInput" name="attachments[]" class="d-none" onchange="handleFileSelection(this, 'editfileList')" multiple>

                        <!-- Display list of selected files here -->
                        <div id="editfileList"></div>
                    </div>
                </td>
            </tr>
        </table>
        <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-warning btn-lg w-50">Submit</button>
        </div>
</form>