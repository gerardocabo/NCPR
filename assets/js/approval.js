
$(document).ready(function () {

    $(".approval-action").click(function (e) {
        e.preventDefault();

        var action = $(this).data("action");
        var role = $(this).data("role");

        Swal.fire({
            title: "Are you sure?",
            text: "You are about to approve this action.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, approve it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                if (role === "QA Engineer") {
                    sendApprovalRequest(action, role); // ENGINEER approval function
                } else if (role === "QA Manager" || "Representative") {
                    sendSPMGRApproval(action, role); // MANAGER/SUPERVISOR approval function
                } else {
                    Swal.fire("Error", "You do not have permission to approve this request.", "error");
                }
            }
        });
    });

    function sendApprovalRequest(action, role) {
        let selectedId = $("#modal-id").text(); // Ensure selected ID is correctly retrieved

        var Containment = $("#containment").val();
        var nonConformance = $("#non-conformance").val();
        var causes = [];
        $("input[name='cause[]']:checked").each(function () {
            causes.push($(this).val());
        });

        //inputs
        var idNo = $("input[name='id_no']").val().trim();
        var name = $("input[name='name']").val().trim();
        var carNo = $("input[name='car_no']").val().trim(); // Get value of car_no input
        var scarNo = $("input[name='scar_no']").val().trim(); // Get value of car_no input
        var DA = $("input[name='document_alert']").val().trim(); // Get value of car_no input
        var contactperson = $("input[name='contact_person']").val().trim(); // Get value of car_no input
        var otherSpecify = $("input[name='other_specify']").val().trim(); // Get value of car_no input
        var yieldOff = $("input[name='yield_off']").val().trim(); // Get value of car_no input
        var regradeDA = $("input[name='da_no']").val().trim(); // Get value of car_no input
        var reworkDA = $("input[name='rework_da_no']").val().trim(); // Get value of car_no input
        var wisnum = $("input[name='wis_no']").val().trim(); // Get value of car_no input
        var repairDA = $("input[name='repair_DA']").val().trim(); // Get value of car_no input
        var scrap_amount = $("input[name='scrap_amount']").val().trim(); // Get value of car_no input
        var shipDate = $("input[name='shipment_date']").val().trim(); // Get value of car_no input

        //radio_inputs
        var correctiveAction = $("input[name='corrective_action']").val(); // Get value of corrective_action input
        var pff = $("input[name='potential_failure']").val(); // Get value of potential_failure input
        var bdReport = $("input[name='bd_report']").val(); // Get value of bd_report input
        var mrb = $("input[name='mrb']").val(); // Get value of mrb input
        var custApp = $("input[name='customer_approval']").val(); // Get value of customer_approval input

        //checkboxes
        // ✅ Collect independent checkboxes into an array
        var independent_checkbox = [];

        var car = $("input[name='car']:checked").val();
        if (car) independent_checkbox.push(car);

        var scar = $("input[name='scar']:checked").val();
        if (scar) independent_checkbox.push(scar);

        var affectedBusiness = $("input[name='affected_business']:checked").val();
        if (affectedBusiness) independent_checkbox.push(affectedBusiness);

        var otherInstructions = $("input[name='other_instructions']:checked").val();
        if (otherInstructions) independent_checkbox.push(otherInstructions);

        var dispoFrom = [];
        $("input[name='dispo_from[]']:checked").each(function () {
            dispoFrom.push($(this).val());
        });
        var IARA = [];
        $("input[name='impact_analysis[]']:checked").each(function () {
            IARA.push($(this).val());
        });

        var prod_dispo = [];
        $("input[name='product_dispo[]']:checked").each(function () {
            prod_dispo.push($(this).val());
        });

        console.log("Sending AJAX request...");
        $.ajax({
            url: "approval.php",
            type: "POST",
            data: {
                action: action,
                role: role,
                ncpr_num: selectedId,

                //inputs
                containment: Containment,
                non_conformance: nonConformance,
                cause: causes,
                id_no: idNo,
                name: name,
                car_no: carNo,
                scar_no: scarNo,
                document_alert: DA,
                contact_person: contactperson,
                other_specify: otherSpecify,
                yield_off: yieldOff,
                da_no: regradeDA,
                rework_da_no: reworkDA,
                wis_no: wisnum,
                repair_DA: repairDA,
                scrap_amount: scrap_amount,
                shipment_date: shipDate,

                //arrays of checkboxes
                independents: independent_checkbox,
                dispo_from: dispoFrom,
                IARA: IARA,
                product_dispo: prod_dispo,

                //i did forgot the radios
                corrective_action: correctiveAction,
                potential_failure: pff,
                bd_report: bdReport,
                mrb: mrb,
                customer_approval: custApp
            },
            dataType: "json", // Expect JSON response
            beforeSend: function () {
                console.log("Sending AJAX request...");
            },
            success: function (response) {
                console.log("Raw response:", response);

                if (response.status === "success") {
                    console.log("Parsed JSON:", response);

                    Swal.fire({
                        title: "Success",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        location.reload(); // Reload the page after success
                    });

                } else {
                    console.error("Error from server:", response.message);
                    Swal.fire("Error", response.message, "error");
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error, xhr.responseText);
                Swal.fire("Error", "AJAX request failed. Check console.", "error");
            }
        });
    }

    function sendSPMGRApproval(action, role) {
        let selectedId = $("#modal-id").text(); // Ensure selected ID is correctly retrieved
        console.log("Sending AJAX request for MANAGER/SUPERVISOR...");

        $.ajax({
            url: "approval.php",
            type: "POST",
            data: {
                action: action,
                role: role,
                ncpr_num: selectedId,
            },
            success: function (response) {
                console.log("Response received:", response);
                // Handle success response
                if (response.status === "success") {
                    console.log("Parsed JSON:", response);

                    Swal.fire({
                        title: "Success",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        location.reload(); // Reload the page after success
                    });

                } else {
                    console.error("Error from server:", response.message);
                    Swal.fire("Error", response.message, "error");
                }
            },
            /*error: function (xhr, status, error) {
                console.error("Error:", error);
            }*/error: function (xhr, status, error) {
                console.error("AJAX Error:", error, xhr.responseText);
                Swal.fire("Error", "AJAX request failed. Check console.", "error");
            }
        });
    }
});
