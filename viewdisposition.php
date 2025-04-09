<p><strong class="me-2">NCPR_NUMBER: </strong><span id="modal-id"></span></p>
<div class="border mb-3 align-items-center p-2">
    <span class="d-block"><strong>This space is intended for QA verification, containment and investigation activities.</strong></span>
    <span id="containment" style="margin-left: 12px;"></span>
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
                    <input class="locked form-check-input" type="radio" name="corrective_action" value="YES" id="corrective_yes">
                    <label class="form-check-label" for="corrective_yes">YES</label>
                </div>
                <div class="form-check">
                    <input class="locked form-check-input" type="radio" name="corrective_action" value="NO" id="corrective_no">
                    <label class="form-check-label" for="corrective_no">NO</label>
                </div>
                <div class="form-check">
                    <input class="locked form-check-input" type="radio" name="corrective_action" value="NA" id="corrective_na">
                    <label class="form-check-label" for="corrective_na">NA</label>
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
                    <span id="id_no"></span><br>
                    <label>Name:</label>
                    <span id="name"></span><br>
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
            <input class="locked" type="radio" name="potential_failure" value="YES" class="mb-5"> Yes<br>
            <input class="locked" type="radio" name="potential_failure" value="NO"> No<br>
        </td>
        <td>
            <div class="d-flex align-items-center mb-2">
                <input class="locked" type="checkbox" name="car" value="CAR"> CAR, CAR No:
                <span id="car_no"></span><br>
                <span class="me-2">8D Report:</span>
                <input class="locked" type="radio" name="bd_report" value="YES" class="me-1"> YES
                <input class="locked" type="radio" name="bd_report" value="NO" class="ms-3 me-1"> NO
            </div>
            <div class="d-flex align-items-center mt-5">
                <input class="locked" type="checkbox" name="scar" value="SCAR"> SCAR, SCAR No:
                <span id="scar_no"></span><br>
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
                <span id="document_alert"></span>
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
                <label for="impact_analysis">Notes:</label>
                <span id="impact_analysis"></span>
            </td>

            <!-- Right Section (Spanning Rows) -->
            <td style="width: 40%; vertical-align: top;" rowspan="2">
                <div class="h-100 d-flex flex-column justify-content-between">
                    <div class="mb-2">
                        <input class="locked" type="checkbox" name="affected_business" value="Affected business"> Affected business unit/ contact person <br>
                        <span id="contact_person"></span>
                    </div>
                    <div>
                        <input class="locked" type="checkbox" name="other_instructions" value="Other instructions"> Other instructions,
                        <span>pls specify;</span><br>
                        <span id="other_specify"></span>
                    </div>
                </div>
            </td>
        </tr>

        <!-- Product Disposition (Left Side) -->
        <tr>
            <td colspan="2">
                <strong>PRODUCT DISPOSITION:</strong>
                <div class="d-flex flex-column gap-2 mt-2">
                    <div>
                        <input class="locked" type="checkbox" name="product_dispo[]" value="Use as is"> Use as is
                    </div>
                    <div class="d-flex gap-3">
                        <input class="locked  ms-4" type="checkbox" name="product_dispo[]" value="Re-inspection"> Re-inspection
                        <input class="locked" type="checkbox" name="product_dispo[]" value="Run under normal process"> Run under normal process
                    </div>
                </div>
                <div class="d-flex align-items-center mt-2">
                    Yield-off $<span id="yield_off"></span>
                </div>
                <div class="d-flex align-items-center mt-2 gap-2">
                    <input class="locked" type="checkbox" name="product_dispo[]" value="Re-grade"> Re-grade, DA No:
                    <span id="da_no"></span>
                </div>
                <div class="d-flex align-items-center mt-2 gap-2">
                    <input class="locked" type="checkbox" name="product_dispo[]" value="Rework"> Rework, DA No:
                    <span id="rework_da_no"></span>
                    WIS No:
                    <span id="wis_no"></span>
                </div>
                <div class="d-flex gap-3 align-items-center flex-wrap mt-2 gap-2">
                    <input class="locked  ms-4" type="checkbox" name="product_dispo[]" value="Re-press"> Re-press
                    <input class="locked" type="checkbox" name="product_dispo[]" value="Re-plate"> Re-plate
                    <input class="locked" type="checkbox" name="product_dispo[]" value="Re-Etest"> <span>Re-Etest</span>
                    <input class="locked" type="checkbox" name="product_dispo[]" value="Re-measure"> Re-measure
                    <input class="locked" type="checkbox" name="product_dispo[]" value="Rework Traveler"> Rework Traveler
                </div>
                <div class="d-flex align-items-center mt-2 gap-2">
                    <input class="locked" type="checkbox" name="product_dispo[]" value="Repair"> Repair, Document Alert #:
                    <span id="document_alert"></span>
                    <input class="locked" type="checkbox" name="product_dispo[]" value="Rework Traveler"> Rework Traveler
                </div>
                <div class="d-flex align-items-center mt-2 gap-2">
                    <input class="locked" type="checkbox" name="product_dispo[]" value="Scrap"> Scrap $
                    <span id="scrap_amount"></span>
                </div>
                <div class="d-flex align-items-center mt-2 gap-2">
                    <input class="locked" type="checkbox" name="product_dispo[]" value="RTV"> RTV <span style="color: blue; text-decoration: underline; margin-left: 20px;">Shipment Date: </span>
                    <span id="shipment_date"></span>
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
                <input class="locked" type="checkbox" name="actions_taken[]" value="Problem solving/troubleshooting"> Problem solving/troubleshooting<br>
                <input class="locked" type="checkbox" name="actions_taken[]" value="Process Verification/Engg Eval"> Process Verification /
                <span>Eng'g Eval.</span>

                <br><br>
                <strong>Process Disposition:</strong><br>
                <input class="locked" type="checkbox" name="process_dispo[]" value="Resume Production"> Resume Production<br>
                <input class="locked" type="checkbox" name="process_dispo[]" value="Stop Production"> Stop Production
                <br><br>
                <span>Affected process/es:</span>
                <span id="affected_process" style="font-size: 10px"></span><br><br>
                <input class="locked" type="checkbox" name="further_eval" value="For further Eng'g Evaluation">
                <span>For further Eng’g Evaluation</span>
            </td>

            <td style="width: 33%; vertical-align: top;">
                <strong>Reason for Resumption:</strong><br>
                <input class="locked" type="checkbox" name="resumption_reason[]" value="Criteria"> Criteria
                <input class="locked" type="checkbox" name="resumption_reason[]" value="Method"> Method
                <input class="locked" type="checkbox" name="resumption_reason[]" value="Materials"> Materials
                <input class="locked" type="checkbox" name="resumption_reason[]" value="Machine"> Machine
                <br><br>
                <input class="locked" type="checkbox" name="resumption_reason[]" value="Machine/fixture repair"> Machine/fixture repair<br>
                <input class="locked" type="checkbox" name="resumption_reason[]" value="Others">
                <span>Others, pls specify</span>
                <br>
                <span id="other_resumption" style="font-size: 10px"></span><br><br>

                <strong>Process Instruction in general:</strong>
                <span>(for <span>DJs</span> other than the affected)</span>
                <span id="process_insttruction" style="font-size: 10px"></span>
            </td>

            <td style="width: 33%; vertical-align: top;">
                <strong>Instructions in detail, see reference doc:</strong><br>
                <input class="locked" type="checkbox" name="instructions_detail[]" value="Document Alert #"> Document Alert #:
                <span id="document_alert" style="font-size: 10px"></span><br>

                <input class="locked" type="checkbox" name="instructions_detail[]" value="Other">
                <span>Other (pls specify)</span>
                <span id="other_specify" style="font-size: 10px"></span>
                <br><br>

                <strong>Documents needing revision:</strong><br>
                <div class="d-flex flex-wrap gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="documents_revision[]" value="N/A" id="doc_na">
                        <label class="form-check-label" for="doc_na" style="font-size: 12px;">N/A</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="documents_revision[]" value="WIS" id="doc_wis">
                        <label class="form-check-label" for="doc_wis" style="font-size: 12px;">WIS</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="documents_revision[]" value="DJ" id="doc_dj">
                        <label class="form-check-label" for="doc_dj" style="font-size: 12px;">DJ</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="documents_revision[]" value="PROC" id="doc_proc">
                        <label class="form-check-label" for="doc_proc" style="font-size: 12px;">PROC</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="documents_revision[]" value="CP" id="doc_cp">
                        <label class="form-check-label" for="doc_cp" style="font-size: 12px;">CP</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="documents_revision[]" value="PFMEA" id="doc_pfmea">
                        <label class="form-check-label" for="doc_pfmea" style="font-size: 12px;">PFMEA</label>
                    </div>
                </div>

                <br><br>

                <strong>Process Released By:</strong>
                <span id="released_by" style="font-size: 10px"></span>
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
            <?php if ($user_role === 'QA MANAGER' || $user_role === 'QA SUPERVISOR'): ?>
                <td colspan="2">
                    <div class="d-flex justify-content-end align-items-center">
                        <div class="me-3">
                            <strong>QA Manager or his/her appointee:</strong><br>
                            (Signature & Date)
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Select Action
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item approval-action" href="#" data-action="approve" data-role="QA Manager" title="approve the NCPR">Approve</a></li>
                                <li><a class="dropdown-item approval-action" href="#" data-action="full_approve" data-role="QA Manager" title="As per Absence of the Key-Person">Full Approval</a></li>
                                <li><a class="dropdown-item approval-action" href="#" data-action="cancel" data-role="QA Manager">Cancel</a></li>
                            </ul>
                        </div>
                    </div>
                </td>

            <?php endif; ?>
            <?php if ($user_role === 'SHELDAHL REPRESENTATIVE'): ?>
                <td>
                    <strong>QA Manager or his/her appointee:</strong><br>
                    (Signature & Date)
                    <div class="signature-line">
                        <strong><span id="approvd_by_supv_mgr"></span></strong>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center" style="margin-right: auto;">
                            <div class="me-3">
                                <strong>Sheldahl / NT Representative:</strong><br>
                                (Signature & Date)
                            </div>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Select Action
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item approval-action" href="#" data-action="approve" data-role="Representative">Approve</a></li>
                                <li><a class="dropdown-item approval-action" href="#" data-action="reject" data-role="Representative">Reject</a></li>
                                <li><a class="dropdown-item approval-action" href="#" data-action="cancel" data-role="Representative">Cancel</a></li>
                            </ul>
                        </div>
                    </div>
                </td>
            <?php endif; ?>
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
                    <input type="text" name="pe_ee_head_signature" class="form-control" id="pe_ee_head_signature" placeholder="PE or EE Head">
                    <label for="pe_ee_head_signature"><strong>PE or EE Head or his/her appointee:</strong></label>
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
    <h5 class="text-center mb-5 mt-5">File Attachments</h5>
    <div id="file-list" class="d-block flex-wrap">
    </div>
</table>